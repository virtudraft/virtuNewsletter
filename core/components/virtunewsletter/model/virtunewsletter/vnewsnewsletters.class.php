<?php

class vnewsNewsletters extends xPDOSimpleObject {

    public function getSubscribers($includeInactive = FALSE) {
        $output = array();

        $c = $this->xpdo->newQuery('vnewsSubscribers');
        $c->leftJoin('vnewsSubscribersHasCategories', 'SubscribersHasCategories', 'SubscribersHasCategories.subscriber_id = vnewsSubscribers.id');
        $c->leftJoin('vnewsCategories', 'Categories', 'Categories.id = SubscribersHasCategories.category_id');
        $c->leftJoin('vnewsNewslettersHasCategories', 'NewslettersHasCategories', 'NewslettersHasCategories.category_id = Categories.id');
        $c->leftJoin('vnewsNewsletters', 'Newsletters', 'Newsletters.id = NewslettersHasCategories.newsletter_id');
        $c->where(array(
            'Newsletters.id' => $this->get('id')
        ));
        if (empty($includeInactive)) {
            $c->where(array(
                'SubscribersHasCategories.unsubscribed_on:=' => NULL,
                'vnewsSubscribers.is_active:=' => 1,
            ));
        }

        $subscribers = $this->xpdo->getCollection('vnewsSubscribers', $c);
        if ($subscribers) {
            foreach ($subscribers as $subscriber) {
                $output[] = $subscriber->toArray();
            }
        }

        return $output;
    }

    public function countSubscribers($includeInactive = FALSE) {
        $output = array();

        $c = $this->xpdo->newQuery('vnewsSubscribers');
        $c->leftJoin('vnewsSubscribersHasCategories', 'SubscribersHasCategories', 'SubscribersHasCategories.subscriber_id = vnewsSubscribers.id');
        $c->leftJoin('vnewsCategories', 'Categories', 'Categories.id = SubscribersHasCategories.category_id');
        $c->leftJoin('vnewsNewslettersHasCategories', 'NewslettersHasCategories', 'NewslettersHasCategories.category_id = Categories.id');
        $c->leftJoin('vnewsNewsletters', 'Newsletters', 'Newsletters.id = NewslettersHasCategories.newsletter_id');
        $c->where(array(
            'Newsletters.id' => $this->get('id')
        ));
        if (empty($includeInactive)) {
            $c->where(array(
                'SubscribersHasCategories.unsubscribed_on:=' => NULL,
                'vnewsSubscribers.is_active:=' => 1,
            ));
        }

        return $this->xpdo->getCount('vnewsSubscribers', $c);
    }

}
