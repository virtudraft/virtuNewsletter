<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013-2016 by goldsky <goldsky@virtudraft.com>
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
class SendAllNewslettersProcessor extends modProcessor {

    /** @var xPDOObject|modAccessibleObject $object The object being grabbed */
    public $object;

    /** @var string $objectType The object "type", this will be used in various lexicon error strings */
    public $objectType = 'virtunewsletter.SendAllNewsletters';

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
        $newsletterId = $this->getProperty('newsletter_id');
        $this->newsletter = $this->modx->getObject('vnewsNewsletters', $newsletterId);
        if (!$this->newsletter) {
            return 'Missing the newsletter';
        }
        $limit = $this->modx->getOption('virtunewsletter.email_limit');
        $this->setProperty('limit', $limit);

        return parent::initialize();
    }

    function process() {
        $c = $this->modx->newQuery($this->classKey);
        $c->leftJoin('vnewsNewsletters', 'Newsletters', 'Newsletters.id = vnewsReports.newsletter_id');
        $c->leftJoin('vnewsSubscribers', 'Subscribers', 'Subscribers.id = vnewsReports.subscriber_id');
        $c->select(array(
            'vnewsReports.*',
            'Newsletters.subject',
            'Subscribers.email',
            'Subscribers.name',
        ));

        date_default_timezone_set('UTC');
        $c->where(array(
            'vnewsReports.status' => 'queue',
        ));
        $isRecurring = $this->newsletter->get('is_recurring');
        if ($isRecurring) {
            $children = $this->newsletter->getMany('Children');
            $childrenIds = array();
            if ($children) {
                foreach ($children as $child) {
                    $childrenIds[] = $child->get('id');
                }
            }
            if (!empty($childrenIds)) {
                $c->where(array(
                    'newsletter_id:IN' => $childrenIds,
                ));
            }
        } else {
            $c->where(array(
                'newsletter_id' => $this->newsletter->get('id'),
            ));
        }

        $limit = $this->modx->getOption('virtunewsletter.email_limit');
        $c->limit($limit);

        $queues = $this->modx->getCollection($this->classKey, $c);
        $outputReports = array();
        if ($queues) {
            $emailProvider = $this->modx->getOption('virtunewsletter.email_provider');
            if (!empty($emailProvider)) {
                $queuesArray = array();
                foreach ($queues as $queue) {
                    $queuesArray[] = $queue->toArray();
                }
                $result = $this->modx->virtunewsletter->sendToEmailProvider($emailProvider, $this->newsletter->get('id'), $queuesArray);
                if (!$result) {
                    $error = $this->modx->virtunewsletter->getError();
                    return $this->failure($error);
                } else {
                    $output = $this->modx->virtunewsletter->getOutput();
                    foreach ($output as $item) {
                        if (isset($item['email']) && isset($item['status'])) {
                            $c = $this->modx->newQuery($this->classKey);
                            $c->leftJoin('vnewsSubscribers', 'Subscribers', 'Subscribers.id = vnewsReports.subscriber_id');
                            $c->where(array(
                                'Subscribers.email' => $item['email']
                            ));
                            $itemQueue = $this->modx->getObject($this->classKey, $c);
                            if ($itemQueue) {
                                $itemQueue->set('status_logged_on', time());
                                $itemQueue->set('status', $item['status']);
                                if ($itemQueue->save() === FALSE) {
                                    $this->modx->setDebug();
                                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to update a queue! ' . print_r($queue->toArray(), TRUE), '', __METHOD__, __FILE__, __LINE__);
                                    $this->modx->setDebug(FALSE);
                                }
                            }
                        }
                    }
                    $outputReports = $output;
                }
            } else {
                foreach ($queues as $queue) {
                    $sent = $this->modx->virtunewsletter->sendNewsletter($this->newsletter->get('id'), $queue->get('subscriber_id'));
                    if ($sent) {
                        $queue->set('status_logged_on', time());
                        $queue->set('status', 'sent');
                        if ($queue->save() === FALSE) {
                            $this->modx->setDebug();
                            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to update a queue! ' . print_r($queue->toArray(), TRUE), '', __METHOD__, __FILE__, __LINE__);
                            $this->modx->setDebug(FALSE);
                        } else {
                            $outputReports[] = $this->modx->virtunewsletter->getPlaceholders();
                        }
                    } else {
                        $this->modx->setDebug();
                        $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to send a queue! ' . print_r($queue->toArray(), TRUE), '', __METHOD__, __FILE__, __LINE__);
                        $this->modx->setDebug(FALSE);
                    }
                }
            }
        }

        return $this->success('', $outputReports);
    }

}

return 'SendAllNewslettersProcessor';
