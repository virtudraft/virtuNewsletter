<?php

$usergroups = $modx->getOption('virtunewsletter.usergroups');
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
    $usergroupObj = $modx->getObject('modUserGroup', array(
        'name' => $usergroup
    ));
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
        $subscriber = $modx->getObject('vnewsSubscribers', array(
            'email' => $user['email']
        ));
        if ($subscriber) {
            continue;
        }

        $subscriber = $modx->newObject('vnewsSubscribers');
        $subscriber->fromArray(array(
            'user_id' => $user['user_id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'is_active' => 1,
        ));
        $subscriber->save();
        $subscriberId = $subscriber->getPrimaryKey();

        /**
         * Generate intermediate table vnewsSubscribersHasCategories for usergroups
         * because vnewsCategoriesHasUsergroups
         */
        $c = $modx->newQuery('vnewsCategories');
        $c->leftJoin('vnewsCategoriesHasUsergroups', 'vnewsCategoriesHasUsergroups', array(
            'vnewsCategoriesHasUsergroups.usergroup_id:IN' => $user['usergroups']
        ));
        $categories = $modx->getCollection('vnewsCategories', $c);
        if ($categories) {
            foreach ($categories as $category) {
                $categoryId = $category->get('id');
                $subsHasCats = $modx->getObject('vnewsSubscribersHasCategories', array(
                    'subscriber_id' => $subscriberId,
                    'category_id' => $categoryId
                ));
                if ($subsHasCats)
                    continue;

                $subsHasCats = $modx->newObject('vnewsSubscribersHasCategories');
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