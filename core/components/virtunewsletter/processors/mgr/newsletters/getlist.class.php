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
class NewslettersGetListProcessor extends modObjectGetListProcessor {

    public $classKey = 'vnewsNewsletters';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.NewslettersGetList';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';

    /**
     * Can be used to adjust the query prior to the COUNT statement
     *
     * @param xPDOQuery $c
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $parentId = $this->getProperty('parentId');
        if (!empty($parentId)){
            $c->where(array(
                'parent_id' => $parentId
            ));
        }
        return $c;
    }

    /**
     * Prepare the row for iteration
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        $objectArray = $object->toArray();
        if (empty($objectArray['scheduled_for'])) {
            $objectArray['scheduled_for'] = '';
        }
        $objectArray['subscribers'] = $object->countSubscribers();

        $c = $this->modx->newQuery('vnewsReports');
        $c->where(array(
            'newsletter_id' => $objectArray['id'],
            'status' => queue,
        ));
        $dateFormat = $this->modx->getOption('manager_date_format', null, 'Y-m-d');
        $timeFormat = $this->modx->getOption('manager_time_format', null, 'g:i a');
        $objectArray['created_on_formatted'] = '';
        if (!empty($objectArray['created_on'])) {
            $dateTime = DateTime::createFromFormat('U', $objectArray['created_on']);
            $objectArray['created_on_formatted'] = $dateTime->format($dateFormat);
        }
        $objectArray['scheduled_for_formatted'] = '';
        if (!empty($objectArray['scheduled_for'])) {
            $dateTime = DateTime::createFromFormat('U', $objectArray['scheduled_for']);
            $objectArray['scheduled_for_formatted'] = $dateTime->format($dateFormat);
        }
        $objectArray['queue'] = $this->modx->getCount('vnewsReports', $c);
        $objectArray['queue_subscriber'] = $objectArray['queue'].' / '.$objectArray['subscribers'];
        $objectArray['stopped_at_time'] = '';
        $objectArray['stopped_at_date'] = '';
        $stoppedAt = $objectArray['stopped_at'];
        $objectArray['stopped_at_formatted'] = '';
        if (!empty($stoppedAt)) {
            $dateTime = DateTime::createFromFormat('U', $stoppedAt);
            $objectArray['stopped_at_formatted'] = $dateTime->format("$dateFormat $timeFormat");
            $objectArray['stopped_at_time'] = $dateTime->format($timeFormat);
            $objectArray['stopped_at_date'] = $dateTime->format($dateFormat);
        }

        return $objectArray;
    }
}

return 'NewslettersGetListProcessor';