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
//        $readerPage = $this->modx->getOption('virtunewsletter.readerpage');
//        if (!empty($readerPage) && is_numeric($readerPage)) {
//            $url = $this->modx->makeUrl($readerPage, '', 'newsid=' . $objectArray['id']);
//        }
        $url = dirname($this->modx->virtunewsletter->config['connectorUrl']) . '/web.php?action=web/newsletters/read&newsid=' . $objectArray['id'];
        $objectArray['content'] = '<iframe src="' . $url . '" height="400" width="100%"></iframe>';
        $objectArray['leaf'] = TRUE;
        $objectArray['scheduled_for'] = date('m/d/Y', $objectArray['scheduled_for']);
        $category = $object->getOne('NewslettersHasCategories');
        if ($category) {
            $objectArray['category_id'] = $category->get('category_id');
        }
        unset($objectArray['id']); // avoid Ext component's ID

        return $objectArray;
    }

}

return 'NewslettersGetListProcessor';