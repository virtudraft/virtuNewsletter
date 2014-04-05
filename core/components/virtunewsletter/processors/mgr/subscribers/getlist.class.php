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
class SubscribersGetListProcessor extends modObjectGetListProcessor {

    public $classKey = 'vnewsSubscribers';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.SubscribersGetList';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';

    /**
     * Can be used to adjust the query prior to the COUNT statement
     *
     * @param xPDOQuery $c
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where(array(
                'name:LIKE' => '%' . $query . '%',
                'OR:email:LIKE' => '%' . $query . '%',
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
        $objectArray['usergroups'] = '';
        if (!empty($objectArray['user_id'])) {
            $userObj = $this->modx->getObject('modUser', $objectArray['user_id']);
            if ($userObj) {
                $usergroups = $userObj->getUserGroupNames();
                if ($usergroups) {
                    $objectArray['usergroups'] = @implode(',', $usergroups);
                }
            }
        }

        $subscribersHasCategories = $object->getMany('vnewsSubscribersHasCategories');
        $objectArray['categories'] = '';
        if ($subscribersHasCategories) {
            $categories = array();
            $objectArray['categories'] = array();
            foreach ($subscribersHasCategories as $subsHasCats) {
                $category = $subsHasCats->getOne('vnewsCategories');
                $categories[] = $category->get('name');
                $objectArray['categories'][] = array(
                    'category_id' => $category->get('id'),
                    'category' => $category->get('name'),
                );
            }
            $objectArray['categories_text'] = @implode(', ', $categories);
        }

        return $objectArray;
    }

}

return 'SubscribersGetListProcessor';
