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
        $client = new Mailgun($apiKey);
        foreach ($loopRecipients as $queue) {
            $to = array();
            $mergeVars = array();
            $responses = array();
            foreach ($queue as $recipient){
                $to[] = $recipient['email'];
                $mergeVars[$recipient['email']] = array(
                    'name'=> !empty($recipient['name']) ? $recipient['name'] : $recipient['email'],
                    'id'=>$recipient['id']
                );
                $responses[] = array(
                    'email' => $recipient['email'],
                    'status' => 'sent',
                );
            }
            $content = $this->modx->virtunewsletter->processEmailMessage($newsletter['id']);  
            $postData['to'] = implode(', ',$to);
            $postData['html'] = $content;
            $postData['text'] = strip_tags($content);
            $postData['subject'] = $messageArray['subject'];
            $postData['recipient-variables'] = $this->modx->toJSON($mergeVars);

            try {
                    //$this->modx->log(modX::LOG_LEVEL_ERROR, 'to:'. $emailAddress);
                    $result = $client->sendMessage($domain, $postData);
                    if (!empty($result) && !empty($result->http_response_code) && $result->http_response_code == '200') {
                        $this->modx->virtunewsletter->addResponse($responses);
                    }
                } catch (Exception $e) {
                    $this->modx->setDebug();
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'A mailgun error occurred: ' . get_class($e) . ' - ' . $e->getMessage(), '', __METHOD__, __FILE__, __LINE__);
                    $this->modx->setDebug(FALSE);
                    $this->modx->virtunewsletter->setError('A mailgun error occurred: ' . get_class($e) . ' - ' . $e->getMessage());
                    return FALSE;
                }

        }


        return TRUE;
    }

}

return 'VirtuNewsletterMailgunController';
