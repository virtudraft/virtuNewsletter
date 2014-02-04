<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013 by goldsky <goldsky@virtudraft.com>
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
class ReportsGetListProcessor extends modObjectGetListProcessor {

    public $classKey = 'vnewsReports';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.ReportsGetList';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';

    /**
     * Can be used to adjust the query prior to the COUNT statement
     *
     * @param xPDOQuery $c
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $newsletterId = $this->getProperty('newsletter_id');
        if (!empty($newsletterId)) {
            $ids = $this->_newsletterDescendants($newsletterId);
            $c->where(array(
                'newsletter_id:IN' => $ids,
            ));
        }
        $c->leftJoin('vnewsSubscribers', 'vnewsSubscribers', 'vnewsSubscribers.id = vnewsReports.subscriber_id');
        $c->select(array(
            'vnewsReports.*',
            $this->modx->getSelectColumns('vnewsSubscribers', 'vnewsSubscribers', null, array('id', 'is_active'), TRUE),
        ));

        return $c;
    }

    private function _newsletterDescendants($newsletterId) {
        $ids = array($newsletterId);
        $collection = $this->modx->getCollection('vnewsNewsletters', array(
            'parent_id' => $newsletterId
        ));
        if ($collection) {
            foreach ($collection as $item) {
                $ids[] = $item->get('id');
            }
        }
        return $ids;
    }

    /**
     * Prepare the row for iteration
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        $objectArray = parent::prepareRow($object);
        $status = $this->modx->lexicon('virtunewsletter.' . $objectArray['status']);
        if ($status) {
            $objectArray['status'] = $status;
        }
        return $objectArray;
    }
}

return 'ReportsGetListProcessor';