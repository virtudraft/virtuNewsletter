<?php

class NewslettersUpdateProcessor extends modObjectUpdateProcessor {

    public $classKey = 'vnewsNewsletters';
    public $languageTopics = array('virtunewsletter:cmp');
    public $objectType = 'virtunewsletter.NewslettersUpdate';

    public function initialize() {
        $primaryKey = $this->getProperty($this->primaryKeyField, false);
        if (empty($primaryKey))
            return $this->modx->lexicon($this->objectType . '_err_ns');
        $this->object = $this->modx->getObject($this->classKey, $primaryKey);
        if (empty($this->object))
            return $this->modx->lexicon($this->objectType . '_err_nfs', array($this->primaryKeyField => $primaryKey));

        if ($this->checkSavePermission && $this->object instanceof modAccessibleObject && !$this->object->checkPolicy('save')) {
            return $this->modx->lexicon('access_denied');
        }

        $subject = $this->getProperty('subject');
        if (empty($subject)) {
            $this->addFieldError('subject', $this->modx->lexicon('virtunewsletter.newsletter_err_ns_subject'));
            return FALSE;
        }
        $resourceId = $this->getProperty('resource_id');
        if (empty($resourceId)) {
            $this->addFieldError('resource_id', $this->modx->lexicon('virtunewsletter.newsletter_err_ns_resource_id'));
            return FALSE;
        }
        $scheduledFor = $this->getProperty('scheduled_for');
        if (empty($scheduledFor)) {
            $this->addFieldError('scheduled_for', $this->modx->lexicon('virtunewsletter.newsletter_err_ns_scheduled_for'));
            return FALSE;
        }
        $categories = $this->getProperty('categories');
        $categories = @explode(',', $categories);
        if (empty($categories) || (isset($categories[0]) && empty($categories[0]))) {
            $this->addFieldError('categories', $this->modx->lexicon('virtunewsletter.newsletter_err_ns_categories'));
            return FALSE;
        }

        $schedule = $this->getProperty('scheduled_for');
        date_default_timezone_set('UTC');
        $schedule = strtotime($schedule);

        $this->setProperty('scheduled_for', $schedule);

        return true;
    }

    /**
     * Override in your derivative class to do functionality before save() is run
     * @return boolean
     */
    public function beforeSave() {
        $resourceId = $this->getProperty('resource_id');
        $oldContent = $this->object->get('content');
        $ctx = $this->modx->getObject('modResource', $resourceId)->get('context_key');
        $url = $this->modx->makeUrl($resourceId, $ctx, '', 'full');
        if (empty($url)) {
            $this->addFieldError('resource_id', $this->modx->lexicon('virtunewsletter.newsletter_err_empty_url'));
            return FALSE;
        }
        $newContent = file_get_contents($url);
        if (empty($newContent)) {
            $this->addFieldError('resource_id', $this->modx->lexicon('virtunewsletter.newsletter_err_empty_content'));
            return FALSE;
        }
        if ($oldContent !== $newContent) {
            $this->setProperty('content', $newContent);
        }

        return !$this->hasErrors();
    }

    /**
     * Override in your derivative class to do functionality after save() is run
     * @return boolean
     */
    public function afterSave() {
        $newsId = $this->getProperty('id');
        $this->modx->removeCollection('vnewsNewslettersHasCategories', array(
            'newsletter_id' => $newsId
        ));

        $categories = $this->getProperty('categories');
        $categories = @explode(',', $categories);
        if ($categories) {
            $addCats = array();
            foreach ($categories as $category) {
                $category = intval($category);
                $newsHasCat = $this->modx->newObject('vnewsNewslettersHasCategories');
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