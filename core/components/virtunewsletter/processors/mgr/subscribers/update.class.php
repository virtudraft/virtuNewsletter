<?php

class SubscribersUpdateProcessor extends modObjectUpdateProcessor {

    public $classKey = 'Subscribers';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.SubscribersUpdate';

}

return 'SubscribersUpdateProcessor';