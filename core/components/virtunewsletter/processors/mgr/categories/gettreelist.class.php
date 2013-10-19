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
class CategoriesGetTreeListProcessor extends modObjectGetListProcessor {

    public $classKey = 'vnewsCategories';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.CategoriesGetTreeList';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';

    /**
     * Prepare the row for iteration
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        $objectArray = $object->toArray();
        $objectArray['catid'] = $objectArray['id'];
        $objectArray['text'] = $objectArray['name'];
        $hasChildren = $this->_hasChildren($objectArray['catid']);
        $objectArray['leaf'] = $hasChildren ? FALSE : TRUE;

        $usergroups = $object->getMany('vnewsCategoriesHasUsergroups');
        $usergroupsArray = array();
        if ($usergroups) {
            foreach ($usergroups as $usergroup) {
                $usergroupId = $usergroup->get('usergroup_id');
                $c = $this->modx->newQuery('modUserGroup');
                $c->where(array(
                    'id' => $usergroupId
                ));

                $modUserGroup = $this->modx->getObject('modUserGroup', $c);
                if ($modUserGroup) {
                    $usergroupsArray[] = array(
                        'usergroup_id' => $usergroupId,
                        'usergroup' => $modUserGroup->get('name')
                    );
                }
            }
        }
        $objectArray['usergroups'] = $usergroupsArray;

        unset($objectArray['id']); // avoid Ext component's ID

        return $objectArray;
    }

    private function _hasChildren($catid) {
        return $this->modx->getCount('vnewsNewslettersHasCategories', array(
                    'category_id' => $catid
        ));
    }

    /**
     * Can be used to insert a row before iteration
     * @param array $list
     * @return array
     */
    public function afteriteration(array $list) {
        $c = $this->modx->newQuery('vnewsNewslettersHasCategories');
        $c->distinct(TRUE);
        $newsHasCats = $this->modx->getCollection('vnewsNewslettersHasCategories', $c);
        if ($newsHasCats) {
            $newsHasCatsArray = array();
            foreach ($newsHasCats as $newsHasCat) {
                $newsHasCatsArray[] = $newsHasCat->get('newsletter_id');
            }
        }

        $leaf = true;
        if (!empty($newsHasCatsArray)) {
            $orphanNewsletters = $this->modx->getCollection('vnewsNewsletters', array(
                'id:NOT IN' => $newsHasCatsArray
            ));

            $hasChildren = $orphanNewsletters ? TRUE : FALSE;
            $leaf = $hasChildren ? FALSE : TRUE;
        }
        array_unshift($list, array(
            'name' => $this->modx->lexicon('virtunewsletter.uncategorized'),
            'description' => $this->modx->lexicon('virtunewsletter.uncategorized_desc'),
            'catid' => 0,
            'text' => $this->modx->lexicon('virtunewsletter.uncategorized'),
            'leaf' => $leaf,
            'usergroups' => array(
                'usergroup_id' => 0,
                'usergroup' => ''
            ),
        ));

        return $list;
    }

}

return 'CategoriesGetTreeListProcessor';