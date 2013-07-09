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
$usergroups = $this->modx->getOption('virtunewsletter.usergroups');
if (empty($usergroups)) {
    return $this->failure('Missing the "virtunewsletter.usergroups" in the System Settings!');
}

$usergroups = @explode(',', $usergroups);
array_walk($usergroups, create_function('&$v', '$v = trim($v);'));

/**
 * Prepare
 */
$usersArray = array();
foreach ($usergroups as $usergroup) {
    $usergroupObj = $this->modx->getObject('modUserGroup', array(
        'name' => $usergroup
    ));
    if (!$usergroupObj) {
        $this->modx->setDebug();
        $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to get $usergroup: ' . $usergroup, '', __METHOD__, __FILE__, __LINE__);
        $this->modx->setDebug(FALSE);
        continue;
    }
    $users = $usergroupObj->getUsersIn();
    if ($users) {
        foreach ($users as $user) {
            $userArray = $user->toArray();
            $profile = $user->getOne('Profile');
            $profileArray = $profile->toArray();
            $usersArray[] = array(
                'user_id' => $userArray['id'],
                'email' => $profileArray['email'],
                'name' => $profileArray['fullname'],
                'usergroups' => $user->getUserGroups()
            );
        }
    }
}

/**
 * Generate users
 */
if ($usersArray) {

    foreach ($usersArray as $user) {
        $subscriber = $this->modx->getObject('vnewsSubscribers', array(
            'email' => $user['email']
        ));
        if ($subscriber) {
            continue;
        }

        $subscriber = $this->modx->newObject('vnewsSubscribers');
        $subscriber->fromArray(array(
            'user_id' => $user['user_id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'is_active' => 1,
            'hash' => $this->modx->virtunewsletter->setHash($user['email'])
        ));
        $subscriber->save();
        $subscriberId = $subscriber->getPrimaryKey();

        /**
         * Generate intermediate table vnewsSubscribersHasCategories for usergroups
         * because vnewsCategoriesHasUsergroups
         */
        $c = $this->modx->newQuery('vnewsCategories');
        $c->leftJoin('vnewsCategoriesHasUsergroups', 'vnewsCategoriesHasUsergroups', array(
            'vnewsCategoriesHasUsergroups.usergroup_id:IN' => $user['usergroups']
        ));
        $categories = $this->modx->getCollection('vnewsCategories', $c);
        if ($categories) {
            foreach ($categories as $category) {
                $categoryId = $category->get('id');
                $subsHasCats = $this->modx->getObject('vnewsSubscribersHasCategories', array(
                    'subscriber_id' => $subscriberId,
                    'category_id' => $categoryId
                ));
                if ($subsHasCats)
                    continue;

                $subsHasCats = $this->modx->newObject('vnewsSubscribersHasCategories');
                $subsHasCats->fromArray(array(
                    'subscriber_id' => $subscriberId,
                    'category_id' => $categoryId
                        ), NULL, TRUE, TRUE);
                $subsHasCats->save();
            }
        }
    }
}

return $this->success();