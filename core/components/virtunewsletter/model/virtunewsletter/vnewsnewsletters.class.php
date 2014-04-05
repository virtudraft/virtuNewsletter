<?php

class vnewsNewsletters extends xPDOSimpleObject {

    public function getSubscribers($includeInactive = FALSE) {
        $output = array();

        $c = $this->xpdo->newQuery('vnewsSubscribers');
        $c->leftJoin('vnewsSubscribersHasCategories', 'vnewsSubscribersHasCategories', 'vnewsSubscribersHasCategories.subscriber_id = vnewsSubscribers.id');
        $c->leftJoin('vnewsCategories', 'vnewsCategories', 'vnewsCategories.id = vnewsSubscribersHasCategories.category_id');
        $c->leftJoin('vnewsNewslettersHasCategories', 'vnewsNewslettersHasCategories', 'vnewsNewslettersHasCategories.category_id = vnewsCategories.id');
        $c->leftJoin('vnewsNewsletters', 'vnewsNewsletters', 'vnewsNewsletters.id = vnewsNewslettersHasCategories.newsletter_id');
        $c->where(array(
            'vnewsNewsletters.id' => $this->get('id')
        ));
        if (empty($includeInactive)) {
            $c->where(array(
                'vnewsSubscribers.is_active' => 1,
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

}
