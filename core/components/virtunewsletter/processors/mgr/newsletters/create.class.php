<?php

class NewslettersCreateProcessor extends modObjectCreateProcessor {

    public $classKey = 'Newsletters';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.NewslettersCreate';

    /**
     * {@inheritDoc}
     * @return boolean
     */
    public function initialize() {
        $resourceId = $this->getProperty('resource_id');
        if (empty($resourceId)) {
            $this->addFieldError('resource_id', $this->modx->lexicon('virtunewsletter.newsletter_err_ns_resource_id'));
            return FALSE;
        }
        $ctx = $this->modx->getObject('modResource', $resourceId)->get('context_key');
        $url = $this->modx->makeUrl($resourceId, $ctx, '', 'full');
        if (empty($url)) {
            $this->addFieldError('resource_id', $this->modx->lexicon('virtunewsletter.newsletter_err_empty_url'));
            return FALSE;
        }
        $content = file_get_contents($url);
        if (empty($content)) {
            $this->addFieldError('resource_id', $this->modx->lexicon('virtunewsletter.newsletter_err_empty_content'));
            return FALSE;
        }
        $this->setProperty('content', $content);
        $this->setProperty('created_on', time());
        $userId = $this->modx->user->get('id');
        $this->setProperty('created_by', $userId);
        $schedule = $this->getProperty('scheduled_for');
        date_default_timezone_set('UTC');
        $schedule = strtotime($schedule);

        $this->setProperty('scheduled_for', $schedule);
        $this->setProperty('is_recurring', $this->getProperty('is_recurring'));

        return parent::initialize();
    }

    /**
     * Override in your derivative class to do functionality before save() is run
     * @return boolean
     */
    public function afterSave() {
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

return 'NewslettersCreateProcessor';