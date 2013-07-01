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
        }
        $url = $this->modx->makeUrl($resourceId);
        $content = file_get_contents($url);
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
        $newsletterId = $this->object->getPrimaryKey();
        $categoryId = $this->getProperty('category_id');
        $params = array(
                'newsletter_id' => $newsletterId,
                'category_id' => $categoryId
        );

        $newslettersHasCategories = $this->modx->newObject('NewslettersHasCategories');
        $newslettersHasCategories->fromArray($params, '', TRUE, TRUE);
        $addMany = array($newslettersHasCategories);
        $this->object->addMany($addMany);

        


        $this->object->save();

        return true;
    }

}

return 'NewslettersCreateProcessor';