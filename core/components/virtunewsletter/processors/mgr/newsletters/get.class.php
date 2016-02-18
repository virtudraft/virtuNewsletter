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
class NewslettersGetProcessor extends modObjectGetProcessor {

    /** @var string $objectType The object "type", this will be used in various lexicon error strings */
    public $objectType = 'virtunewsletter.NewslettersGet';

    /** @var string $classKey The class key of the Object to iterate */
    public $classKey = 'vnewsNewsletters';

    /** @var array $languageTopics An array of language topics to load */
    public $languageTopics = array('virtunewsletter:cmp');

    /**
     * Return the response
     * @return array
     */
    public function cleanup() {
        $objectArray = $this->object->toArray();

        $categories = $this->object->getMany('NewslettersHasCategories');
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

        return $this->success('', $objectArray);
    }

}

return 'NewslettersGetProcessor';
