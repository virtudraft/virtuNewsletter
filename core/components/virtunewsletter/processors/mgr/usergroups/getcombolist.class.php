<?php

class UsergroupsGetComboListProcessor extends modObjectGetListProcessor {

    public $classKey = 'modUserGroup';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.UsergroupsGetComboList';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';

}

return 'UsergroupsGetComboListProcessor';