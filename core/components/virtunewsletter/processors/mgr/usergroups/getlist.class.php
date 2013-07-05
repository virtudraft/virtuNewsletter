<?php

class UsergroupsGetListProcessor extends modObjectGetListProcessor {

    public $classKey = 'vnewsCategoriesHasUsergroups';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.UsergroupsGetList';
    public $defaultSortField = 'usergroup_id';
    public $defaultSortDirection = 'ASC';

}

return 'UsergroupsGetListProcessor';