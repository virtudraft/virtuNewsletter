<?php

include_once dirname(dirname(__FILE__)) . '/vendors/mailgun-php/vendor/autoload.php';

use Mailgun\Mailgun;

class VirtuNewsletterMailgunController extends VirtuNewsletterEmailProvider {

    public function send() {
        if (!class_exists('Mailgun')) {
            $err = 'Missing Mailgun\'s class file. Please add the "mailgun-php" library into ' . dirname(dirname(__FILE__)) . '/vendors/';
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, $err, '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            $this->modx->virtunewsletter->setError($err);
            return FALSE;
        }

        $domain = $this->modx->getOption('virtunewsletter.mailgun.endpoint');
        if (empty($domain)) {
            $err = 'Missing virtunewsletter.mailgun.endpoint in System Settings';
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, $err, '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            $this->modx->virtunewsletter->setError($err);
            return FALSE;
        }
        $apiKey = $this->modx->getOption('virtunewsletter.mailgun.api_key');
        if (empty($apiKey)) {
            $err = 'Missing virtunewsletter.mailgun.api_key in System Settings';
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, $err, '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            $this->modx->virtunewsletter->setError($err);
            return FALSE;
        }

        $sender = $this->getSender();
        $recipients = $this->getRecipients();
        $messageArray = $this->getMessage();
        $newsletter = $this->getNewsletter();
        $emailDebug = $this->modx->getOption('virtunewsletter.email_debug');
        $batchMode = $this->modx->getOption('virtunewsletter.mailgun.batch_mode');
        $systemEmailPrefix = $this->modx->getOption('virtunewsletter.email_prefix');

        /**
         * Note: The maximum number of recipients allowed for Batch Sending is 1,000.
         * @link https://documentation.mailgun.com/user_manual.html#batch-sending
         */
        $batchLimit = 1000; // Note: Mailgun limits the number of recipients per message to 1000
        $loopRecipients = array_chunk($recipients, $batchLimit);

        if (!empty($sender['email_from_name'])) {
            $from = "{$sender['email_from_name']} <{$sender['email_sender']}>";
        } else {
            $from = $sender['email_sender'];
        }
        $client = new Mailgun($apiKey);
        if ($batchMode) {
            $message = preg_replace_callback('/\[\[\+(' . preg_quote($systemEmailPrefix) . ')(\w+)\]\]/i', function($matches) {
                return '%recipient.' . strtolower($matches[2]) . '%';
            }, $messageArray['message']);
            foreach ($loopRecipients as $queue) {
                $batchMsg = $client->BatchMessage($domain);
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
                $scheduleTime = (!empty($newsletter['scheduled_for']) ? date('r', $newsletter['scheduled_for']) : false);
                $batchMsg->setDeliveryTime($scheduleTime);
                $batchMsg->setTestMode($emailDebug);
                $batchMsg->setClickTracking(true);
                $batchMsg->setOpenTracking(true);

                try {
                    $responses = array();
                    foreach ($queue as $recipient) {
                        $name = isset($recipient['name']) && !empty($recipient['name']) ? $recipient['name'] : '';
                        $firstName = '';
                        $lastName = '';
                        if (!empty($name)) {
                            $nameParts = preg_split("/\s+/", $recipient['name']);
                            array_reverse($nameParts);
                            $lastName = $nameParts[0];
                            unset($nameParts[0]);
                            if (!empty($nameParts)) {
                                array_reverse($nameParts);
                                $firstName = @implode(' ', $nameParts);
                            }
                        }
                        $variables = array(
                            "first" => $firstName,
                            "last"  => $lastName,
                            "name"  => $name,
                        );
                        $variables = array_merge($variables, $recipient);
                        $variables = array_merge($variables, $messageArray);
                        unset($variables['message']);

                        $batchMsg->addToRecipient($recipient['email'], $variables);
                        $responses[] = array(
                            'email' => $recipient['email'],
                            'status' => 'sent',
                        );
                    }
                    $batchMsg->finalize();

                    $this->modx->virtunewsletter->addResponse($responses);

                    $result = $batchMsg->getMessageIds();
                    // $this->modx->log(modX::LOG_LEVEL_ERROR, __LINE__ . ': $result ' . print_r($result, 1));
                    /**
                     * Array
                     * (
                     *     [0] => <20160301113047.60700.98119@sandboxbc7bcbecaba34469aad0551876d4f380.mailgun.org>
                     * )
                     */
                } catch (Exception $e) {
                    $err = 'A mailgun error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
                    $this->modx->setDebug();
                    $this->modx->log(modX::LOG_LEVEL_ERROR, $err, '', __METHOD__, __FILE__, __LINE__);
                    $this->modx->setDebug(FALSE);
                    $this->modx->virtunewsletter->setError($err);
                    return FALSE;
                }
            }
        } else {
            $postData = array(
                'from' => $from,
                "o:tag" => $newsletter['subject'],
                "o:testmode" => ($emailDebug ? 'yes' : 'no'),
                "o:tracking" => 'yes',
                "o:tracking-clicks" => 'yes',
                "o:tracking-opens" => 'yes',
            );
            if (!empty($newsletter['scheduled_for'])) {
                $postData['o:deliverytime'] = date('r', $newsletter['scheduled_for']);
            }
            foreach ($loopRecipients as $queue) {
                foreach ($queue as $recipient) {
                    $confirmLinkArgs = $this->modx->virtunewsletter->getSubscriber(array('email' => $recipient['email']));
                    $confirmLinkArgs = array_merge($confirmLinkArgs, array('act' => 'unsubscribe'));
                    $phs = array_merge($recipient, $confirmLinkArgs, array(
                        // to avoid confusion on template
                        'id' => $newsletter['id'],
                        'newsid' => $newsletter['id'],
                        'subid' => $recipient['id']
                    ));
                    $this->modx->virtunewsletter->setPlaceholders($phs, $systemEmailPrefix);
                    $content = $this->modx->virtunewsletter->processEmailMessage($newsletter['id']);

                    $postData['html'] = $content;
                    $postData['text'] = strip_tags($content);

                    $phs = $this->modx->virtunewsletter->getPlaceholders();
                    $subject = $this->modx->virtunewsletter->parseTplCode($messageArray['subject'], $phs);
                    $subject = $this->modx->virtunewsletter->processElementTags($subject);
                    $postData['subject'] = $subject;

                    if (!empty($recipient['name'])) {
                        $emailAddress = "{$recipient['name']} <{$recipient['email']}>";
                    } else {
                        $emailAddress = $recipient['email'];
                    }
                    $postData['to'] = $emailAddress;
                    try {
                        $result = $client->sendMessage($domain, $postData);
                        if (!empty($result) && !empty($result->http_response_code) && $result->http_response_code == '200') {
                            $this->modx->virtunewsletter->addResponse(array(array(
                                'email' => $recipient['email'],
                                'status' => 'sent',
                            )));
                        }
                    } catch (Exception $e) {
                        $err = 'A mailgun error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
                        $this->modx->setDebug();
                        $this->modx->log(modX::LOG_LEVEL_ERROR, $err, '', __METHOD__, __FILE__, __LINE__);
                        $this->modx->setDebug(FALSE);
                        $this->modx->virtunewsletter->setError($err);
                        return FALSE;
                    }
                }
            }
        }


        return TRUE;
    }

}

return 'VirtuNewsletterMailgunController';
