<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013-2015 by goldsky <goldsky@virtudraft.com>
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
include_once dirname(__FILE__) . '/update.class.php';

class ReportSendProcessor extends ReportUpdateProcessor {

    /**
     * {@inheritDoc}
     * @return mixed
     */
    public function process() {
        $newsletterId = $this->getProperty('newsletter_id');
        $emailProvider = $this->modx->getOption('virtunewsletter.email_provider');
        if (!empty($emailProvider)) {
            $objectsArray = array($this->object->toArray());
            $result = $this->modx->virtunewsletter->sendToEmailProvider($emailProvider, $newsletterId, $objectsArray);
            if (!$result) {
                $error = $this->modx->virtunewsletter->getError();
                return $this->failure($error);
            } else {
                $output = $this->modx->virtunewsletter->getOutput();
                foreach ($output as $item) {
                    if (isset($item['email']) && isset($item['status'])) {
                        $this->object->set('status_logged_on', time());
                        $this->object->set('status', $item['status']);
                    }
                }
            }
        } else {
            $sent = $this->modx->virtunewsletter->sendNewsletter($newsletterId, $this->object->get('subscriber_id'));
            if ($sent) {
                $this->object->set('status_logged_on', time());
                $this->object->set('status', 'sent');
            } else {
                $this->modx->setDebug();
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to send a queue! ' . print_r($this->object->toArray(), TRUE), '', __METHOD__, __FILE__, __LINE__);
                $this->modx->setDebug(FALSE);
                return $this->failure('Failed to send a queue!');
            }
        }

        return parent::process();
    }

}

return 'ReportSendProcessor';
