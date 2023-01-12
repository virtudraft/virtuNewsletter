<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013-2023 by goldsky <goldsky@virtudraft.com>
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
class SubscribersUpdateProcessor extends modObjectUpdateProcessor {

    public $classKey = 'vnewsSubscribers';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.SubscribersUpdate';

    /**
     * Override in your derivative class to do functionality after save() is run
     * @return boolean
     */
    public function afterSave() {
        $subId = $this->getProperty('id');
        $cbCategories = $this->getProperty('categories');
        $categories = array();
        foreach($cbCategories as $k => $v) {
            if(empty($v)) {
                continue;
            }
            $categories[] = $k;
        }

        if (!empty($categories)) {
            // remove diff first
            $diffs = $this->modx->getCollection('vnewsSubscribersHasCategories', array(
                'subscriber_id:=' => $subId,
                'category_id:NOT IN' => $categories,
            ));
            if ($diffs) {
                foreach ($diffs as $diff) {
                    $diff->remove();
                }
            }

            // categories to subscriber
            $addCats = array();
            foreach ($categories as $category) {
                if (empty($category)) {
                    continue;
                }
                $category = intval($category);
                $subHasCat = $this->modx->getObject('vnewsSubscribersHasCategories', array(
                    'subscriber_id' => $subId,
                    'category_id' => $category,
                ));
                if (empty($subHasCat)) {
                    $subHasCat = $this->modx->newObject('vnewsSubscribersHasCategories');
                    $subHasCat->fromArray(array(
                        'subscriber_id' => $subId,
                        'category_id' => $category,
                        'subscribed_on' => time()
                            ));
                    $addCats[] = $subHasCat;
                }
            }
            if (!empty($addCats)) {
                $this->object->addMany($addCats);
                $this->object->save();
            }

        }

        $isActive = $this->getProperty('is_active');
        if ($isActive) {
            $this->modx->virtunewsletter->addSubscriberQueues($this->getProperty('id'));
        } else {
            $this->modx->virtunewsletter->removeSubscriberQueues($this->getProperty('id'));
        }

        return true;
    }

}

return 'SubscribersUpdateProcessor';