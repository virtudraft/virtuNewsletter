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
 */
class TestNewsletterProcessor extends modProcessor {

    /** @var xPDOObject|modAccessibleObject $object The object being grabbed */
    public $object;

    /** @var string $objectType The object "type", this will be used in various lexicon error strings */
    public $objectType = 'virtunewsletter.TestNewsletterProcessor';

    /** @var string $classKey The class key of the Object to iterate */
    public $classKey = 'vnewsReports';

    /** @var string $primaryKeyField The primary key field to grab the object by */
    public $primaryKeyField = 'id';

    /** @var string $permission The Permission to use when checking against */
    public $permission = '';

    /** @var array $languageTopics An array of language topics to load */
    public $languageTopics = array('virtunewsletter:cmp');
    public $newsletter;

    /**
     * {@inheritDoc}
     * @return boolean
     */
    public function initialize() {
        $id = $this->getProperty('id');
        $systemEmailPrefix = $this->modx->getOption('virtunewsletter.email_prefix');
        $this->modx->virtunewsletter->setPlaceholder('id', $id, $systemEmailPrefix);
        $this->newsletter = $this->modx->getObject('vnewsNewsletters', $id);
        if (!$this->newsletter) {
            $this->modx->setDebug();
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to get newsletter w/ id:' . $id, '', __METHOD__, __FILE__, __LINE__);
            $this->modx->setDebug(FALSE);
            return 'Unable to get newsletter w/ id:' . $id;
        }

        return parent::initialize();
    }

    function process() {
        $newsletterArray = $this->newsletter->toArray();
        $email = $this->getProperty('email');
        $systemEmailPrefix = $this->modx->getOption('virtunewsletter.email_prefix');
        $subscriber = $this->modx->getObject('vnewsSubscribers', array(
            'email' => $email
        ));
        if ($subscriber) {
            $subscriberArray = $subscriber->toArray();
        } else {
            $subscriberArray = array(
                'email' => $email
            );
        }

        $confirmLinkArgs = $this->modx->virtunewsletter->getSubscriber(array('email' => $subscriberArray['email']));
        if ($confirmLinkArgs) {
            $confirmLinkArgs = array_merge($confirmLinkArgs, array('act' => 'unsubscribe'));
            $this->modx->virtunewsletter->setPlaceholders($confirmLinkArgs, $systemEmailPrefix);
        }
        $this->modx->virtunewsletter->setPlaceholders(array_merge($subscriberArray, array('id' => $newsletterArray['id'])), $systemEmailPrefix);
        $emailProvider = $this->modx->getOption('virtunewsletter.email_provider');
        if (!empty($emailProvider)) {
            $output = $this->modx->virtunewsletter->sendToEmailProvider($emailProvider, $newsletterArray['id'], array($subscriberArray));
        } else {
            $output = $this->modx->virtunewsletter->sendMail($newsletterArray['subject'], $newsletterArray['content'], $subscriberArray['email']);
        }

        return $this->success($output);
    }

}

return 'TestNewsletterProcessor';
