<?php

class vnewsSubscribers extends xPDOSimpleObject {

    public function getCategories() {
        $output = array();
        $many = $this->getMany('SubscribersHasCategories');
        if ($many) {
            foreach ($many as $item) {
                $cat = $item->getOne('Categories');
                if ($cat) {
                    $output[] = $cat->toArray();
                }
            }
            $output = array_unique($output);
        }
        return $output;
    }

    public function getCategoryNames() {
        $output = array();
        $many = $this->getMany('SubscribersHasCategories');
        if ($many) {
            foreach ($many as $item) {
                $cat = $item->getOne('Categories');
                if ($cat) {
                    $output[] = $cat->get('name');
                }
            }
            $output = array_unique($output);
        }
        return $output;
    }

    public function getUserGroups() {
        $output = array();
        $userId = $this->get('user_id');
        $user = $this->xpdo->getObject('modUser', $userId);
        if ($user) {
            $output = $user->getUserGroups();
        }
        return $output;
    }

    public function getUserGroupNames() {
        $output = array();
        $userId = $this->get('user_id');
        $user = $this->xpdo->getObject('modUser', $userId);
        if ($user) {
            $output = $user->getUserGroupNames();
        }
        return $output;
    }

    public function getReports() {
        $output = array();
        $many = $this->getMany('Reports');
        if ($many) {
            foreach ($many as $item) {
                $output[] = $item->toArray();
            }
            $output = array_unique($output);
        }
        return $output;
    }

    /**
     * Get newsletters for current user
     * @param   array   $options    optional
     * @return  array
     */
    public function getNewsletters($options = array()) {
        $output = array();

        $c = $this->xpdo->newQuery('vnewsNewsletters');
        $c->leftJoin('vnewsNewslettersHasCategories', 'NewslettersHasCategories', 'NewslettersHasCategories.newsletter_id = vnewsNewsletters.id');
        $c->leftJoin('vnewsCategories', 'Categories', 'Categories.id = NewslettersHasCategories.category_id');
        $c->leftJoin('vnewsSubscribersHasCategories', 'SubscribersHasCategories', 'SubscribersHasCategories.category_id = Categories.id');
        $c->leftJoin('vnewsSubscribers', 'Subscribers', 'Subscribers.id = SubscribersHasCategories.subscriber_id');
        $c->where(array(
            'Subscribers.id:=' => $this->get('id')
        ));
        if (!empty($options) && is_array($options)) {
            if (isset($options['queueOnly']) && !empty($options['queueOnly'])) {
                $c->leftJoin('vnewsReports', 'Reports', 'Reports.subscriber_id = Subscribers.id');
                $c->where(array(
                    'Reports.status:=' => 'queue'
                ));
            }
            if (isset($options['upcomingOnly']) && !empty($options['upcomingOnly'])) {
                $time = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
                $c->where(array(
                    'vnewsNewsletters.scheduled_for:>=' => $time
                ));
            }
        }

        $newsletters = $this->xpdo->getCollection('vnewsNewsletters', $c);
        if ($newsletters) {
            foreach ($newsletters as $newsletter) {
                $output[] = $newsletter->toArray();
            }
        }

        return $output;
    }

    public function setCategory($categoryId) {
        $params = array(
            'subscriber_id' => $this->get('id'),
            'category_id' => $categoryId,
        );
        $subHasCat = $this->xpdo->getObject('vnewsSubscribersHasCategories', $params);
        if (!$subHasCat) {
            $subHasCat = $this->xpdo->newObject('vnewsSubscribersHasCategories');
            $params['subscribed_on'] = time();
            $subHasCat->fromArray($params);
            return $subHasCat->save();
        }

        return true;
    }

}
