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
class CategoriesGetProcessor extends modObjectGetProcessor {

    /** @var string $objectType The object "type", this will be used in various lexicon error strings */
    public $objectType = 'virtunewsletter.CategoriesGet';

    /** @var string $classKey The class key of the Object to iterate */
    public $classKey = 'vnewsCategories';

    /** @var array $languageTopics An array of language topics to load */
    public $languageTopics = array('virtunewsletter:cmp');

    /**
     * Return the response
     * @return array
     */
    public function cleanup() {
        $objectArray = $this->object->toArray();

        $usergroups = $this->object->getMany('CategoriesHasUsergroups');
        $usergroupsArray = array();
        $usergroupsGrid = array();
        if ($usergroups) {
            foreach ($usergroups as $usergroup) {
                $usergroupId = $usergroup->get('usergroup_id');
                $c = $this->modx->newQuery('modUserGroup');
                $c->where(array(
                    'id' => $usergroupId
                ));

                $modUserGroup = $this->modx->getObject('modUserGroup', $c);
                if ($modUserGroup) {
                    $usergroupsArray[] = $usergroupId;
                    $usergroupsObjects[$usergroupId] = $modUserGroup->get('name');
                    $usergroupsGrid[] = array(
                        $usergroupId,
                        $modUserGroup->get('name')
                    );
                }
            }
        }
        $objectArray['usergroups'] = @implode(',', $usergroupsArray);
        $objectArray['usergroups_grid'] = $usergroupsGrid;
        $objectArray['usergroups_objects'] = $usergroupsObjects;

        return $this->success('', $objectArray);
    }

}

return 'CategoriesGetProcessor';
