<?php

class CategoriesGetListProcessor extends modObjectGetListProcessor {

    public $classKey = 'vnewsCategories';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.CategoriesGetList';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';

}

return 'CategoriesGetListProcessor';