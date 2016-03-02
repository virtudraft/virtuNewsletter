<?php

class VirtuNewsletterMandrillController extends VirtuNewsletterEmailProvider {

    public function send() {
        $file = dirname(dirname(__FILE__)) . '/vendors/mailchimp-mandrill-api-php/src/Mandrill.php';
        if (!file_exists($file)) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Missing required file: ' . $file, '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(false);
            $this->modx->virtunewsletter->setError('Missing a required file');
            return false;
        }
        require_once $file;

        $apiKey = $this->modx->getOption('virtunewsletter.mandrill.api_key');
        if (empty($apiKey)) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Missing virtunewsletter.mandrill.api_key in System Settings', '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            $this->modx->virtunewsletter->setError('Missing virtunewsletter.mandrill.api_key in System Settings');
            return FALSE;
        }

        $sender = $this->getSender();
        $recipients = $this->getRecipients();
        $messageArray = $this->getMessage();
        $newsletter = $this->getNewsletter();
        $globalMergeVars = array();
        foreach ($newsletter as $k => $v) {
            $globalMergeVars[] = array(
                'name' => $k,
                'content' => $v
            );
        }

        $to = array();
        $mergeVars = array();
        foreach ($recipients as $recipient) {
            $to[] = array(
                'email' => $recipient['email'],
                'name' => $recipient['name'],
                'type' => 'to'
            );
            $mergeVars[] = array(
                'rcpt' => $recipient['email'],
                'vars' => array(
                    array(
                        'name' => 'name',
                        'content' => !empty($recipient['name']) ? $recipient['name'] : $recipient['email']
                    ),
                    array(
                        'name' => 'email',
                        'content' => $recipient['email']
                    ),
                    array(
                        'name' => 'subid',
                        'content' => $recipient['id']
                    ),
                    array(
                        'name' => 'hash',
                        'content' => $recipient['hash']
                    ),
                )
            );
        }

        // switch modx's tags to mandrill's symbols
        $systemEmailPrefix = $this->modx->getOption('virtunewsletter.email_prefix');
        $mandrillTags = preg_replace_callback('/\[\[\+(' . $systemEmailPrefix . ')(\w+)\]\]/i', function($matches) {
            return '*|' . strtoupper($matches[2]) . '|*';
        }, $messageArray['message']);

        try {
            $mandrill = new Mandrill($apiKey);
            $message = array(
                'html' => $mandrillTags,
                'text' => strip_tags($mandrillTags),
                'subject' => $messageArray['subject'],
                'from_email' => $sender['email_sender'],
                'from_name' => $sender['email_from_name'],
                'to' => $to,
                'headers' => array('Reply-To' => $sender['email_reply_to']),
                'important' => false,
                'track_opens' => null,
                'track_clicks' => null,
                'auto_text' => null,
                'auto_html' => null,
                'inline_css' => null,
                'url_strip_qs' => null,
                'preserve_recipients' => null,
                'view_content_link' => null,
                'bcc_address' => $sender['email_bcc_address'],
                'tracking_domain' => null,
                'signing_domain' => null,
                'return_path_domain' => null,
                'merge' => true,
                'global_merge_vars' => $globalMergeVars,
                'merge_vars' => $mergeVars,
                'tags' => null,
                'subaccount' => null,
                'google_analytics_domains' => null,
                'google_analytics_campaign' => null,
                'metadata' => null,
                'recipient_metadata' => null,
                'attachments' => null,
                'images' => null
            );
            $async = false;
            $ip_pool = 'Main Pool';
            date_default_timezone_set('UTC');
            $send_at = date('Y-m-d H:i:s');
            $result = $mandrill->messages->send($message, $async, $ip_pool/* , $send_at */);

            $this->modx->virtunewsletter->addResponse($result);
            //    print_r($result);
            /*
              Array
              (
                [0] => Array
                    (
                        [email] => recipient.email@example.com
                        [status] => sent
                        [reject_reason] => hard-bounce
                        [_id] => abc123abc123abc123abc123abc123
                    )
              )
             */
        } catch (Mandrill_Error $e) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(), '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            $this->modx->virtunewsletter->setError('A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage());
            return FALSE;
        }

        return TRUE;
    }

}

return 'VirtuNewsletterMandrillController';
