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
        $subscriber = $modx->getObject('Subscribers', array(
            'email' => $user['email']
        ));
        if ($subscriber) {
            continue;
        }

        $subscriber = $modx->newObject('Subscribers');
        $subscriber->fromArray(array(
            'user_id' => $user['user_id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'is_active' => 1,
        ));
        $subscriber->save();
    }
}

/**
 * Generate intermediate table SubscribersHasCategories because
 * CategoriesHasUsergroups
 */
foreach ($usersArray as $user) {
    $c = $modx->newQuery('Categories');
    $c->leftJoin('CategoriesHasUsergroups', 'CategoriesHasUsergroups', array(
        'CategoriesHasUsergroups.usergroup_id:IN' => $user['usergroups']
    ));
    $categories = $modx->getCollection('Categories', $c);
    if ($categories) {
        foreach ($categories as $category) {
            $categoryId = $category->get('id');
            $subsHasCats = $modx->getObject('SubscribersHasCategories', array(
                'subscriber_id' => $user['user_id'],
                'category_id' => $categoryId
            ));
            if ($subsHasCats)
                continue;

            $subsHasCats = $modx->newObject('SubscribersHasCategories');
            $subsHasCats->fromArray(array(
                'subscriber_id' => $user['user_id'],
                'category_id' => $categoryId
            ), NULL, TRUE, TRUE);
            $subsHasCats->save();
        }
    }
}

return $this->success();