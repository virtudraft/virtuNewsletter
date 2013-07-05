<?php

class CategoriesUpdateProcessor extends modObjectUpdateProcessor {

    public $classKey = 'vnewsCategories';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.CategoriesUpdate';

    /**
     * {@inheritDoc}
     * @return boolean
     */
    public function initialize() {
        $name = $this->getProperty('name');
        if (empty($name)) {
            $this->addFieldError('name', $this->modx->lexicon('virtunewsletter.category_err_ns_name'));
        }
        return parent::initialize();
    }

    /**
     * Override in your derivative class to do functionality after save() is run
     * @return boolean
     */
    public function afterSave() {
        $catId = $this->getProperty('id');
        $this->modx->removeCollection('vnewsCategoriesHasUsergroups', array(
            'category_id' => $catId
        ));

        $usergroups = $this->getProperty('usergroups');
        $usergroups = @explode(',', $usergroups);
        if ($usergroups) {
            $addUsergroups = array();
            foreach ($usergroups as $usergroup) {
                $catHasUg = $this->modx->newObject('vnewsCategoriesHasUsergroups');
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