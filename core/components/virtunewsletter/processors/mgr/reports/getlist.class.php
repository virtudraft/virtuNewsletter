<?php

class ReportsGetListProcessor extends modObjectGetListProcessor {

    public $classKey = 'Reports';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.ReportsGetList';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';

    /**
     * Can be used to adjust the query prior to the COUNT statement
     *
     * @param xPDOQuery $c
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $newsletterId = $this->getProperty('newsletter_id');
        if (!empty($newsletterId)) {
            $c->where(array(
                'newsletter_id' => $newsletterId
            ));
        }
        $c->leftJoin('Subscribers', 'Subscribers', 'Subscribers.user_id = Reports.subscriber_id');
        $c->select(array(
            'Reports.*',
            $this->modx->getSelectColumns('Subscribers', 'Subscribers', null, array('id', 'is_active'), TRUE),
        ));
        return $c;
    }

}

return 'ReportsGetListProcessor';