<?php

class CategoriesRemoveProcessor extends modObjectRemoveProcessor {

    public $classKey = 'vnewsCategories';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.CategoriesRemove';

}

return 'CategoriesRemoveProcessor';