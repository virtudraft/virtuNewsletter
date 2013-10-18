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
        $categoryId = (int)$this->getProperty('category_id');
        if (!empty($categoryId)) {
            $c->innerJoin('vnewsNewslettersHasCategories', 'vnewsNewslettersHasCategories');
            $c->leftJoin('vnewsCategories', 'vnewsCategories', 'vnewsCategories.id = vnewsNewslettersHasCategories.category_id');
            $c->where(array(
                'vnewsCategories.id' => $categoryId
            ));
        } elseif ($categoryId === 0) {
            $criteria = $this->modx->newQuery('vnewsNewslettersHasCategories');
            $criteria->distinct();
            $newsHasCats = $this->modx->getCollection('vnewsNewslettersHasCategories', $criteria);
            $newsHasCatsArray = array();
            if ($newsHasCats) {
                foreach ($newsHasCats as $item) {
                    $newsHasCatsArray[] = $item->get('newsletter_id');
                }
            }

            $c->where(array(
                'id:NOT IN' => $newsHasCatsArray
            ));
        }

        $c->where(array(
            'parent_id' => 0
        ));
        
        return $c;
    }

    /**
     * Prepare the row for iteration
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        $objectArray = $object->toArray();
        $objectArray['newsid'] = $objectArray['id'];
        $objectArray['text'] = $objectArray['subject'] . ' (' . $objectArray['id'] . ')';
        $objectArray['leaf'] = TRUE;
        $objectArray['scheduled_for'] = date('m/d/Y', $objectArray['scheduled_for']);

        $categories = $object->getMany('vnewsNewslettersHasCategories');
        $categoriesArray = array();
        if ($categories) {
            foreach ($categories as $category) {
                $categoryId = $category->get('category_id');
                $categoryObj = $this->modx->getObject('vnewsCategories', $categoryId);
                if ($categoryObj) {
                    $categoriesArray[] = array(
                        'category_id' => $categoryId,
                        'category' => $categoryObj->get('name')
                    );
                }
            }
        } else {
            $categoriesArray[] = array(
                'category_id' => 0,
                'category' => 'uncategorized'
            );
        }
        $objectArray['categories'] = $categoriesArray;

        unset($objectArray['id']); // avoid Ext component's ID

        return $objectArray;
    }

}

return 'NewslettersGetListProcessor';