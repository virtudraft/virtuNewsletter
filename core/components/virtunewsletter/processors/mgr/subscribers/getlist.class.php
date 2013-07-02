<?php

class SubscribersGetListProcessor extends modObjectGetListProcessor {

    public $classKey = 'Subscribers';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.SubscribersGetList';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';

    /**
     * Prepare the row for iteration
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        $objectArray = $object->toArray();
        $objectArray['usergroups'] = '';
        if (!empty($objectArray['user_id'])) {
            $userObj = $this->modx->getObject('modUser', $objectArray['user_id']);
            if ($userObj) {
                $usergroups = $userObj->getUserGroupNames();
                if ($usergroups) {
                    $objectArray['usergroups'] = @implode(',', $usergroups);
                }
            }
        }
        return $objectArray;
    }
}

return 'SubscribersGetListProcessor';