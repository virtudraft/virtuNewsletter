<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013-2019 by goldsky <goldsky@virtudraft.com>
 *
 * This file is part of virtuNewsletter, a newsletter system for MODX
 * Revolution.
 *
 * virtuNewsletter is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation version 3,
 *
 * virtuNewsletter is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * virtuNewsletter; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 */

/**
 * @package virtunewsletter
 * @subpackage processor
 * @link https://documentation.mailgun.com/user_manual.html#webhooks
 */
class ResponseMailgunWebhookProcessor extends modObjectUpdateProcessor {
    public $objectType = 'virtunewsletter.ResponseMailgunWebhook';
    public $classKey = 'vnewsReports';
    public $languageTopics = array('virtunewsletter:web');

    public function initialize() {
        $props = $this->getProperties();
        if (!isset($props['timestamp']) ||
                empty($props['timestamp']) ||
                !isset($props['token']) ||
                empty($props['token']) ||
                !isset($props['signature']) ||
                empty($props['signature'])
        ) {
            header('X-PHP-Response-Code: 406', true, 406);
            return $this->modx->lexicon('access_denied');
        }
        $apiKey = $this->modx->getOption('virtunewsletter.mailgun.api_key');
        $hash = hash_hmac('sha256', $props['timestamp'] . $props['token'], $apiKey);
        if ($hash !== $props['signature']) {
            header('X-PHP-Response-Code: 406', true, 406);
            return $this->modx->lexicon('access_denied');
        }

        $email = $this->getProperty('recipient', false);
        $event = $this->getProperty('event', false);
        if (empty($email) || empty($event)) {
            return $this->modx->lexicon($this->objectType . '_err_ns');
        }
        $c = $this->modx->newQuery($this->classKey);
        $c->leftJoin('vnewsSubscribers', 'Subscribers', 'Subscribers.id = vnewsReports.subscriber_id');
        $c->where(array(
            'Subscribers.email' => $email
        ));
        $this->object = $this->modx->getObject($this->classKey, $c);
        if (empty($this->object)) {
            return $this->modx->lexicon($this->objectType . '_err_nfs', array('email' => $email));
        }

        return true;
    }


    public function process() {
        /**
         * delivered, dropped, bounced, complained, unsubscribed, clicked, opened
         */
        /* Run the beforeSet method before setting the fields, and allow stoppage */
        $canSave = $this->beforeSet();
        if ($canSave !== true) {
            return $this->failure($canSave);
        }
        $props = $this->getProperties();
        $this->object->fromArray(array(
            'status' => $props['event'],
            'status_logged_on' => time(),
        ));

        /* Run the beforeSave method and allow stoppage */
        $canSave = $this->beforeSave();
        if ($canSave !== true) {
            return $this->failure($canSave);
        }

        /* run object validation */
        if (!$this->object->validate()) {
            /** @var modValidator $validator */
            $validator = $this->object->getValidator();
            if ($validator->hasMessages()) {
                foreach ($validator->getMessages() as $message) {
                    $this->addFieldError($message['field'],$this->modx->lexicon($message['message']));
                }
            }
        }

        /* run the before save event and allow stoppage */
        $preventSave = $this->fireBeforeSaveEvent();
        if (!empty($preventSave)) {
            return $this->failure($preventSave);
        }

        if ($this->saveObject() == false) {
            return $this->failure($this->modx->lexicon($this->objectType.'_err_save'));
        }
        $this->afterSave();
        $this->fireAfterSaveEvent();
//        $this->logManagerAction();
        header('X-PHP-Response-Code: 200', true, 200);
        return $this->cleanup();
    }
}

return 'ResponseMailgunWebhookProcessor';
