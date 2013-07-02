<?php

class NewslettersGetListProcessor extends modObjectGetListProcessor {

    public $classKey = 'Newsletters';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.NewslettersGetList';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';

    /**
     * Can be used to adjust the query prior to the COUNT statement
     *
     * @param xPDOQuery $c
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $categoryId = $this->getProperty('category_id');
        if (!empty($categoryId)) {
            $c->innerJoin('NewslettersHasCategories', 'NewslettersHasCategories');
            $c->leftJoin('Categories', 'Categories', 'Categories.id = NewslettersHasCategories.category_id');
            $c->where(array(
                'Categories.id' => $categoryId
            ));
        }
        return $c;
    }

    /**
     * Prepare the row for iteration
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        $objectArray = $object->toArray();
        $objectArray['newsid'] = $objectArray['id'];
        $objectArray['text'] = $objectArray['subject'];
        $objectArray['leaf'] = TRUE;
        $objectArray['scheduled_for'] = date('m/d/Y', $objectArray['scheduled_for']);

        $categories = $object->getMany('NewslettersHasCategories');
        $categoriesArray = array();
        if ($categories) {
            foreach ($categories as $category) {
                $categoryId = $category->get('category_id');
                $categoryObj = $this->modx->getObject('Categories', $categoryId);
                if ($categoryObj) {
                    $categoriesArray[] = array(
                        'category_id' => $categoryId,
                        'category' => $categoryObj->get('name')
                    );
                }
            }
        }
        $objectArray['categories'] = $categoriesArray;

        unset($objectArray['id']); // avoid Ext component's ID

        return $objectArray;
    }

}

return 'NewslettersGetListProcessor';