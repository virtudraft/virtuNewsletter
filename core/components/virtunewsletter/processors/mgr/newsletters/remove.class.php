<?php

class NewslettersRemoveProcessor extends modObjectRemoveProcessor {

    public $classKey = 'Newsletters';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.NewslettersRemove';

}

return 'NewslettersRemoveProcessor';