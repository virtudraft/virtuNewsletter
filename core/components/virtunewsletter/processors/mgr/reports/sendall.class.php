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
            'Subscribers.email_provider',
        ));

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
            $outputReports = $this->modx->virtunewsletter->sendQueuesToProvider($queues);
        }

        return $this->success('', $outputReports);
    }

}

return 'SendAllNewslettersProcessor';
