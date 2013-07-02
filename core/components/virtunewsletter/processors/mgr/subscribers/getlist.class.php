<?php

class SubscribersGetListProcessor extends modObjectGetListProcessor {

    public $classKey = 'Subscribers';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.SubscribersGetList';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';

}

return 'SubscribersGetListProcessor';