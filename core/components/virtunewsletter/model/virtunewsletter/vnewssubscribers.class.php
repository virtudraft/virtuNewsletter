<?php
class vnewsSubscribers extends xPDOSimpleObject {
    
    public function getCategories() {
        $output = array();
        $many = $this->getMany('vnewsSubscribersHasCategories');
        if ($many) {
            foreach ($many as $item) {
                $cat = $item->getOne('vnewsCategories');
                if ($cat) {
                    $output[] = $cat->toArray();
                }
            }
            $output = array_unique($output);
        }
        return $output;
    }
    
    public function getUsergroups() {
        $output = array();
        $userId = $this->get('user_id');
        $user = $this->xpdo->getObject('modUser', $userId);
        if ($user) {
            $output = $user->getUserGroups();
        }
        return $output;
    }
    
    public function getReports() {
        $output = array();
        $many = $this->getMany('vnewsReports');
        if ($many) {
            foreach ($many as $item) {
                $output[] = $item->toArray();
            }
            $output = array_unique($output);
        }
        return $output;
    }
    
    public function getNewsletters() {
        $output = array();

        $c = $this->xpdo->newQuery('vnewsNewsletters');
        $c->leftJoin('vnewsNewslettersHasCategories', 'vnewsNewslettersHasCategories', 'vnewsNewslettersHasCategories.newsletter_id = vnewsNewsletters.id');
        $c->leftJoin('vnewsCategories', 'vnewsCategories', 'vnewsCategories.id = vnewsNewslettersHasCategories.category_id');
        $c->leftJoin('vnewsSubscribersHasCategories', 'vnewsSubscribersHasCategories', 'vnewsSubscribersHasCategories.category_id = vnewsCategories.id');
        $c->leftJoin('vnewsSubscribers', 'vnewsSubscribers', 'vnewsSubscribers.id = vnewsSubscribersHasCategories.subscriber_id');
        $c->where(array(
            'vnewsSubscribers.id' => $this->get('id')
        ));

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
            'category_id' => $categoryId
        );
        $subHasCat = $this->xpdo->getObject('vnewsSubscribersHasCategories', $params);
        if (!$subHasCat) {
            $subHasCat = $this->xpdo->newObject('vnewsSubscribersHasCategories');
            $subHasCat->fromArray($params, NULL, TRUE, TRUE);
            return $subHasCat->save();
        }
        
        return TRUE;
    }
}