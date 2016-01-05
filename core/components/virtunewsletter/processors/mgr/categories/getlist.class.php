<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013-2015 by goldsky <goldsky@virtudraft.com>
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
class CategoriesGetListProcessor extends modObjectGetListProcessor {

    public $classKey = 'vnewsCategories';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.CategoriesGetList';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'asc';

    /**
     * Can be used to adjust the query prior to the COUNT statement
     *
     * @param xPDOQuery $c
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $props = $this->getProperties();
        if (isset($props['ids'])) {
            $ids = json_decode($props['ids']);
            if (!empty($ids)) {
                $c->where(array(
                    'id:IN' => $ids
                ));
            }
        }

        return $c;
    }

}

return 'CategoriesGetListProcessor';