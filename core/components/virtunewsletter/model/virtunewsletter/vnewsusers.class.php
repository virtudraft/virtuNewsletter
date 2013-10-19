<?php

class vnewsUsers extends modUser {

    public function __construct(xPDO & $xpdo) {
        parent::__construct($xpdo);
        $this->set('class_key', 'vnewsUsers');
    }

}
