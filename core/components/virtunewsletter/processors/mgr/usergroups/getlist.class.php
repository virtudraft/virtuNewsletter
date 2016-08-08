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
class UsergroupsGetListProcessor extends modObjectGetListProcessor {

    public $classKey = 'modUserGroup';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.UsergroupsGetComboList';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';

    /**
     * Can be used to adjust the query prior to the COUNT statement
     *
     * @param xPDOQuery $c
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $query = $this->getProperty('query', false);
        $query = trim($query);
        // SuperBoxSelect
        $valuesqry = $this->getProperty('valuesqry', false);
        if ($query && empty($valuesqry)) {
            $c->where(array(
                'name:LIKE' => "$query%"
            ));
        } elseif ($valuesqry && !empty($query)) {
            $ids = @explode(',', $query);
            $c->where(array(
                'id:IN' => $ids
            ));
        }

        return $c;
    }

}

return 'UsergroupsGetListProcessor';
