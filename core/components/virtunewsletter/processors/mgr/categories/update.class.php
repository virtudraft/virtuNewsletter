<?php

class CategoriesUpdateProcessor extends modObjectUpdateProcessor {

    public $classKey = 'Categories';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.CategoriesUpdate';

    /**
     * Override in your derivative class to do functionality after save() is run
     * @return boolean
     */
    public function afterSave() {
        $catId = $this->getProperty('id');
        $this->modx->removeCollection('CategoriesHasUsergroups', array(
            'category_id' => $catId
        ));

        $usergroups = $this->getProperty('usergroups');
        $usergroups = @explode(',', $usergroups);
        if ($usergroups) {
            $addUsergroups = array();
            foreach ($usergroups as $usergroup) {
                $catHasUg = $this->modx->newObject('CategoriesHasUsergroups');
                $catHasUg->fromArray(array(
                    'category_id' => $catId,
                    'usergroup_id' => $usergroup,
                        ), NULL, TRUE, TRUE);
                $addUsergroups[] = $catHasUg;
            }
            $this->object->addMany($addUsergroups);
            $this->object->save();
        }

        return true;
    }

}

return 'CategoriesUpdateProcessor';