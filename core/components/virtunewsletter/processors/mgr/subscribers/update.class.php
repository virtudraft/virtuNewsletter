<?php

class SubscribersUpdateProcessor extends modObjectUpdateProcessor {

    public $classKey = 'vnewsSubscribers';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.SubscribersUpdate';

}

return 'SubscribersUpdateProcessor';