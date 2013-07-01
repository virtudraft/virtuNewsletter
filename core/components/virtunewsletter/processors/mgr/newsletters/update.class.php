<?php

class NewslettersUpdateProcessor extends modObjectUpdateProcessor {

    public $classKey = 'Newsletters';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.NewslettersUpdate';

    /**
     * Override in your derivative class to do functionality after save() is run
     * @return boolean
     */
    public function afterSave() {
        $newsHasCatOld = $this->object->getMany('NewslettersHasCategories');
        $this->modx->removeCollection('NewslettersHasCategories', $newsHasCatOld);

        $categories = $this->getProperty('categories');
        $categories = @explode(',', $categories);
        if ($categories) {
            $addCats = array();
            $newsId = $this->object->getPrimaryKey();
            foreach ($categories as $category) {
                $newsHasCat = $this->modx->newObject('NewslettersHasCategories');
                $newsHasCat->fromArray(array(
                    'newsletter_id' => $newsId,
                    'category_id' => $category,
                        ), NULL, TRUE, TRUE);
                $addCats[] = $newsHasCat;
            }
            $this->object->addMany($addCats);
            $this->object->save();
        }

        return true;
    }

}

return 'NewslettersUpdateProcessor';