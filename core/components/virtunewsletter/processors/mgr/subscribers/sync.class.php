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
class SyncSubscribersProcessor extends modProcessor {

    /** @var xPDOObject|modAccessibleObject $object The object being grabbed */
    public $object;

    /** @var string $objectType The object "type", this will be used in various lexicon error strings */
    public $objectType = 'virtunewsletter.SyncSubscribersProcessor';

    /** @var string $classKey The class key of the Object to iterate */
    public $classKey = 'vnewsReports';

    /** @var string $primaryKeyField The primary key field to grab the object by */
    public $primaryKeyField = 'id';

    /** @var string $permission The Permission to use when checking against */
    public $permission = '';

    /** @var array $languageTopics An array of language topics to load */
    public $languageTopics = array('virtunewsletter:cmp');
    public $usergroups;

    /**
     * {@inheritDoc}
     * @return boolean
     */
    public function initialize() {
        $usergroups = $this->modx->getOption('virtunewsletter.usergroups');
        if (empty($usergroups)) {
            return $this->failure('Missing the "virtunewsletter.usergroups" in the System Settings!');
        }

        $this->usergroups = array_map('trim', @explode(',', $usergroups));

        return parent::initialize();
    }

    function process() {
        /**
         * Collect names from registered user in MODX's database
         */
        $usersArray = array();
        $userIds = array();
        $includeInactive = (boolean) $this->modx->getOption('virtunewsletter.sync_include_inactive_users', null, true);
        $defaultActivation = (int) $this->modx->getOption('virtunewsletter.sync_default_activation', null, 0);
        $start = (int) $this->getProperty('start', 0);
        $limit = (int) $this->getProperty('limit', 20);
        $i = 0;
        foreach ($this->usergroups as $usergroup) {
            $usergroupObj = $this->modx->getObject('modUserGroup', array(
                'name' => $usergroup
            ));
            if (!$usergroupObj) {
                $this->modx->setDebug();
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to get $usergroup: ' . $usergroup, '', __METHOD__, __FILE__, __LINE__);
                $this->modx->setDebug(false);
                continue;
            }
            $users = $usergroupObj->getUsersIn(array(
                'start' => $start,
                'limit' => $limit,
            ));
            if ($users) {
                foreach ($users as $user) {
                    $userArray = $user->toArray();
                    if (!$includeInactive && $userArray['active'] !== 1) {
                        continue;
                    }
                    $profile = $user->getOne('Profile');
                    $profileArray = $profile->toArray();
                    if (empty($profileArray['email'])) {
                        continue;
                    }
                    $userIds[] = $userArray['id'];
                    $isActive = 0;
                    if ($defaultActivation === 2) {
                        $isActive = $userArray['active'];
                    } else if ($defaultActivation === 0 || $defaultActivation === 1) {
                        $isActive = $defaultActivation;
                    }
                    $usersArray[] = array(
                        'user_id' => $userArray['id'],
                        'email' => $profileArray['email'],
                        'name' => !empty($profileArray['fullname']) ? $profileArray['fullname'] : $userArray['username'],
                        'usergroups' => $user->getUserGroups(),
                        'is_active' => $isActive,
                    );
                    ++$i;
                }
            }
        }

        // remove all registered users that are NOT in the groups
        //$this->modx->removeCollection('vnewsSubscribers', array(
        //    'user_id:!=' => 0,
        //    'AND:user_id:NOT IN' => $userIds,
        //));

        /**
         * Generate users
         */
        if ($usersArray) {
            foreach ($usersArray as $user) {
                $subscriber = $this->modx->getObject('vnewsSubscribers', array(
                    'email' => $user['email']
                ));
                if (!$subscriber) {
                    $subscriber = $this->modx->newObject('vnewsSubscribers');
                    $params = array(
                        'user_id' => $user['user_id'],
                        'email' => $user['email'],
                        'name' => $user['name'],
                        'is_active' => 1,
                        'hash' => $this->modx->virtunewsletter->setHash($user['email'])
                    );
                    $subscriber->fromArray($params);
                    if ($subscriber->save() === false) {
                        $this->modx->setDebug();
                        $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to save a new subscriber! ' . print_r($params, TRUE), '', __METHOD__, __FILE__, __LINE__);
                        $this->modx->setDebug(false);
                        continue;
                    }
                } else {
                    $subscriber->set('user_id', $user['user_id']);
                    $subscriber->set('name', $user['name']);
                    if ($subscriber->save() === false) {
                        $this->modx->setDebug();
                        $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to update existing subscriber! ' . print_r($user, TRUE), '', __METHOD__, __FILE__, __LINE__);
                        $this->modx->setDebug(false);
                        continue;
                    }
                }

                $subscriberId = $subscriber->getPrimaryKey();

                /**
                 * Generate intermediate table vnewsSubscribersHasCategories for usergroups
                 * because vnewsCategoriesHasUsergroups
                 */
                $c = $this->modx->newQuery('vnewsCategories');
                $c->leftJoin('vnewsCategoriesHasUsergroups', 'CategoriesHasUsergroups', 'CategoriesHasUsergroups.category_id = vnewsCategories.id');
                $c->where(array(
                    'CategoriesHasUsergroups.usergroup_id:IN' => $user['usergroups']
                ));

                $categories = $this->modx->getCollection('vnewsCategories', $c);
                if ($categories) {
                    $time = time();
                    foreach ($categories as $category) {
                        $categoryId = $category->get('id');
                        $subsHasCats = $this->modx->getObject('vnewsSubscribersHasCategories', array(
                            'subscriber_id' => $subscriberId,
                            'category_id' => $categoryId
                        ));
                        if ($subsHasCats) {
                            continue;
                        }

                        $subsHasCats = $this->modx->newObject('vnewsSubscribersHasCategories');
                        $params = array(
                            'subscriber_id' => $subscriberId,
                            'category_id' => $categoryId,
                            'subscribed_on' => $time,
                        );
                        $subsHasCats->fromArray($params);
                        if ($subsHasCats->save() === false) {
                            $this->modx->setDebug();
                            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to connect subscriber to category! ' . print_r($params, TRUE), '', __METHOD__, __FILE__, __LINE__);
                            $this->modx->setDebug(false);
                            continue;
                        }
                    }
                }

                $this->modx->virtunewsletter->addSubscriberQueues($subscriberId, false);
            }
        }

        return $this->success('', array(
            'count' => $i,
            'start' => $start + $limit,
        ));
    }

}

return 'SyncSubscribersProcessor';
