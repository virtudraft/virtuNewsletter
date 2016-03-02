<?php

require_once dirname(dirname(__FILE__)) . '/vendors/mailgun-php/vendor/autoload.php';

use Mailgun\Mailgun;

class VirtuNewsletterMailgunController extends VirtuNewsletterEmailProvider {

    public function send() {
        $domain = $this->modx->getOption('virtunewsletter.mailgun.endpoint');
        if (empty($domain)) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Missing virtunewsletter.mailgun.endpoint in System Settings', '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            $this->modx->virtunewsletter->setError('Missing virtunewsletter.mailgun.endpoint in System Settings');
            return FALSE;
        }
        $apiKey = $this->modx->getOption('virtunewsletter.mailgun.api_key');
        if (empty($apiKey)) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Missing virtunewsletter.mailgun.api_key in System Settings', '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            $this->modx->virtunewsletter->setError('Missing virtunewsletter.mailgun.api_key in System Settings');
            return FALSE;
        }

        $sender = $this->getSender();
        $recipients = $this->getRecipients();
        $messageArray = $this->getMessage();
        $newsletter = $this->getNewsletter();
        $emailDebug = $this->modx->getOption('virtunewsletter.email_debug');

        // switch modx's tags to mailgun's symbols
        $systemEmailPrefix = $this->modx->getOption('virtunewsletter.email_prefix');
        $message = preg_replace_callback('/\[\[\+(' . $systemEmailPrefix . ')(\w+)\]\]/i', function($matches) {
            return '%recipient.' . strtolower($matches[2]) . '%';
        }, $messageArray['message']);

        if (!empty($sender['email_from_name'])) {
            $from = "{$sender['email_from_name']} <{$sender['email_sender']}>";
        } else {
            $from = $sender['email_sender'];
        }
        $scheduleTime = (!empty($newsletter['scheduled_for']) ? date('r', $newsletter['scheduled_for']) : false);

        $mg = new Mailgun($apiKey);
        $batchMsg = $mg->BatchMessage($domain);
        $firstName = '';
        $lastName = '';
        if (isset($sender['email_from_name']) && !empty($sender['email_from_name'])) {
            $nameParts = preg_split("/\s+/", $sender['email_from_name']);
            array_reverse($nameParts);
            $lastName = $nameParts[0];
            unset($nameParts[0]);
            if (!empty($nameParts)) {
                array_reverse($nameParts);
                $firstName = @implode(' ', $nameParts);
            }
        }
        $batchMsg->setFromAddress($sender['email_sender'], array("first"=>$firstName, "last" => $lastName));
        $batchMsg->setSubject($messageArray['subject']);
        $batchMsg->setHtmlBody($message);
        $batchMsg->setTextBody(strip_tags($message));
        $batchMsg->addTag($newsletter['subject']);
        $batchMsg->setDeliveryTime($scheduleTime);
        $batchMsg->setTestMode($emailDebug);
        $batchMsg->setClickTracking(true);
        $batchMsg->setOpenTracking(true);

        try {
            $responses = array();
            foreach ($recipients as $recipient) {
                $firstName = '';
                $lastName = '';
                if (isset($recipient['name']) && !empty($recipient['name'])) {
                    $nameParts = preg_split("/\s+/", $recipient['name']);
                    array_reverse($nameParts);
                    $lastName = $nameParts[0];
                    unset($nameParts[0]);
                    if (!empty($nameParts)) {
                        array_reverse($nameParts);
                        $firstName = @implode(' ', $nameParts);
                    }
                }
                $batchMsg->addToRecipient($recipient['email'], array("first" => $firstName, "last" => $lastName));
                $responses[] = array(
                    'email' => $recipient['email'],
                    'status' => 'sent',
                );
            }
            $batchMsg->finalize();

            $this->modx->virtunewsletter->addResponse($responses);

            $result = $batchMsg->getMessageIds();
            // $this->modx->log(modX::LOG_LEVEL_ERROR, __LINE__ . ': $result ' . print_r($result, 1));
            //    print_r($result);
            /**
             * Array
             * (
             *     [0] => <20160301113047.60700.98119@sandboxbc7bcbecaba34469aad0551876d4f380.mailgun.org>
             * )
             */
        } catch (Exception $e) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(), '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            $this->modx->virtunewsletter->setError('A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage());
            return FALSE;
        }


        return TRUE;
    }

}

return 'VirtuNewsletterMailgunController';
