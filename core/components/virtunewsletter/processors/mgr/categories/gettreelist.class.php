<?php

class CategoriesGetTreeListProcessor extends modObjectGetListProcessor {

    public $classKey = 'Categories';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.CategoriesGetTreeList';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';

    /**
     * Prepare the row for iteration
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        $objectArray = $object->toArray();
        $objectArray['catid'] = $objectArray['id'];
        $objectArray['text'] = $objectArray['name'];
        $hasChildren = $this->_hasChildren($objectArray['catid']);
        $objectArray['leaf'] = $hasChildren ? FALSE : TRUE;

        $usergroups = $object->getMany('CategoriesHasUsergroups');
        $usergroupsArray = array();
        if ($usergroups) {
            foreach ($usergroups as $usergroup) {
                $usergroupId = $usergroup->get('usergroup_id');
                $c = $this->modx->newQuery('modUserGroup');
                $c->where(array(
                    'id' => $usergroupId
                ));

                $modUserGroup = $this->modx->getObject('modUserGroup', $c);
                if ($modUserGroup) {
                    $usergroupsArray[] = array(
                        'usergroup_id' => $usergroupId,
                        'usergroup' => $modUserGroup->get('name')
                    );
                }
            }
        }
        $objectArray['usergroups'] = $usergroupsArray;

        unset($objectArray['id']); // avoid Ext component's ID

        return $objectArray;
    }

    private function _hasChildren($catid) {
        return $this->modx->getCount('NewslettersHasCategories', array(
            'category_id' => $catid
        ));
    }
}

return 'CategoriesGetTreeListProcessor';