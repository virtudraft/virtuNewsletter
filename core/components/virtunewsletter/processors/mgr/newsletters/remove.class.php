<?php

class NewslettersRemoveProcessor extends modObjectRemoveProcessor {

    public $classKey = 'vnewsNewsletters';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.NewslettersRemove';

}

return 'NewslettersRemoveProcessor';