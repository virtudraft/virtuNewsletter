<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013 by goldsky <goldsky@virtudraft.com>
 *
 * This file is part of virtuNewsletter, a newsletter system for MODX
 * Revolution.
 *
 * virtuNewsletter is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation version 3,
 *
 * virtuNewsletter is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * virtuNewsletter; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 */
/**
 * @package virtunewsletter
 * @subpackage processor
 */
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

        $content = $this->modx->virtunewsletter->outputContent($resourceId);

        $isRecurring = $this->getProperty('is_recurring');
        if (!$isRecurring) {
            $content = $this->modx->virtunewsletter->prepareEmailContent($content);
            $this->setProperty('recurrence_range', NULL);
            $this->setProperty('recurrence_number', NULL);
        } else {
            $recurrenceNumber= $this->getProperty('recurrence_number');
            if (empty($recurrenceNumber)) {
                $this->addFieldError('recurrence_number', $this->modx->lexicon('virtunewsletter.newsletter_err_ns_recurrence_number'));
                return FALSE;
            }
            $recurrenceRange = $this->getProperty('recurrence_range');
            if (empty($recurrenceRange)) {
                $this->addFieldError('recurrence_range', $this->modx->lexicon('virtunewsletter.newsletter_err_ns_recurrence_range'));
                return FALSE;
            }
        }

        $this->setProperty('content', $content);

        $schedule = $this->getProperty('scheduled_for');
        date_default_timezone_set('UTC');
        $schedule = strtotime($schedule);

        $this->setProperty('scheduled_for', $schedule);

        return true;
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

        $isRecurring = $this->getProperty('is_recurring');
        if ($isRecurring) {
            $this->modx->virtunewsletter->createNextRecurrence($newsId);
        } else {
            $this->modx->virtunewsletter->removeAllRecurrences($newsId);
        }
        $isActive = $this->getProperty('is_active');
        if ($isActive) {
            $this->modx->virtunewsletter->setNewsletterQueue($newsId);
        } else {
            $this->modx->virtunewsletter->removeNewsletterQueues($newsId, true);
        }

        return true;
    }

}

return 'NewslettersUpdateProcessor';