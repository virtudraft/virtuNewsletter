<?php

abstract class VirtuNewsletterEmailProvider {

    public $modx;
    public $config;
    public $sender = array();
    public $recipients = array();
    public $message = array();
    public $newsletter = array();

    public function __construct(modX $modx, $config = array()) {
        $this->modx =& $modx;
        if (is_array($config)) {
            $this->config = $config;
        }
    }

    public function setSender($senderInfo) {
        $this->sender = $senderInfo;
    }

    public function getSender() {
        return $this->sender;
    }

    public function setRecipients($recipients) {
        $this->recipients = $recipients;
    }

    public function getRecipients() {
        return $this->recipients;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setNewsletter($newsletter) {
        $this->newsletter = $newsletter;
    }

    public function getNewsletter() {
        return $this->newsletter;
    }

    abstract function send();
}
