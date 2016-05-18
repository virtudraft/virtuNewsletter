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
class StatusGetComboListProcessor extends modObjectGetListProcessor
{

    public $classKey             = 'vnewsReports';
    public $languageTopics       = array('virtunewsletter:cmp');
    public $objectType           = 'virtunewsletter.StatusGetComboList';
    public $defaultSortField     = 'id';
    public $defaultSortDirection = 'ASC';

    /**
     * Get the data of the query
     * @return array
     */
    public function getData()
    {
        $data    = array();
        $results = $this->modx->query("SELECT DISTINCT `status` FROM {$this->modx->getTableName($this->classKey)}");

        while ($r = $results->fetch(PDO::FETCH_ASSOC)) {
            $data['results'][] = $r;
        }

        $data['total'] = count($data['results']);

        return $data;
    }

    public function beforeIteration(array $list)
    {
        $list[] = array(
            'status'      => '',
            'status_text' => $this->modx->lexicon('virtunewsletter.all'),
        );
        return $list;
    }

    public function prepareRow($objectArray)
    {
        $status = $this->modx->lexicon('virtunewsletter.'.$objectArray['status']);
        if ($status === 'virtunewsletter.'.$objectArray['status']) {
            $objectArray['status_text'] = $objectArray['status'];
        } else {
            $objectArray['status_text'] = $status;
        }

        return $objectArray;
    }

}

return 'StatusGetComboListProcessor';
