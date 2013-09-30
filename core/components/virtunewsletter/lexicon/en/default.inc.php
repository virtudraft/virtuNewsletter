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
 *
 * @package virtunewsletter
 * @subpackage lexicon
 */
$_lang['virtunewsletter'] = 'virtuNewsletter';
$_lang['virtunewsletter_desc'] = 'Newsletter Manager';
$_lang['virtunewsletter.subscriber_exists'] = 'Subscriber with email [[+email]] exists.';
$_lang['virtunewsletter.subscriber_err_save'] = 'There was an unexpected result to subscribe you. Please try again.';
$_lang['virtunewsletter.subscriber_suc_save'] = 'Thank you. You are now registered as our subscriber. Please check your mailbox to activate the subscription.';
$_lang['virtunewsletter.subscriber_err_proc'] = 'There was an error to process your subscription. Please try again.';
$_lang['virtunewsletter.subscriber_err_ne'] = 'This subscriber does not exists.';
$_lang['virtunewsletter.subscriber_suc_activated'] = 'Your account has been activated successfully.';
$_lang['virtunewsletter.subscriber_suc_deactivated'] = 'Your account has been deactivated successfully.';

$_lang['setting_virtunewsletter.readerpage'] = 'Resource to read the newsletter';
$_lang['setting_virtunewsletter.readerpage_desc'] = 'Resource\'s ID where visitor can access the newsletter via web';
$_lang['setting_virtunewsletter.usergroups'] = 'Usergroups for newsletters';
$_lang['setting_virtunewsletter.usergroups_desc'] = 'Comma delimited list of usergroups which contain the subscribers for newsletters';
$_lang['setting_virtunewsletter.email_limit'] = 'Email limit per cron';
$_lang['setting_virtunewsletter.email_limit_desc'] = 'Number of emails per hour for the cron job. Please consult your webhost about this. 0 (zero) or empty value means unlimited which will send all emails in 1 (one) batch. Default: 50.';
$_lang['setting_virtunewsletter.email_sender'] = 'Email address for "from"';
$_lang['setting_virtunewsletter.email_sender_desc'] = 'From whom the newsletter comes from.';
$_lang['setting_virtunewsletter.subscribe_confirmation_tpl'] = 'Subscription\'s confirmation tpl' ;
$_lang['setting_virtunewsletter.subscribe_confirmation_tpl_desc'] = 'Resource\'s ID as the email template for the new subscription.';
$_lang['setting_virtunewsletter.subscribe_succeeded_tpl'] = 'Subscription\'s confirmation succeeded tpl' ;
$_lang['setting_virtunewsletter.subscribe_succeeded_tpl_desc'] = 'Resource\'s ID as the email template for the completed confirmation of the new subscription.' ;
$_lang['setting_virtunewsletter.unsubscribe_confirmation_tpl'] = 'Unsubscription\'s confirmation tpl' ;
$_lang['setting_virtunewsletter.unsubscribe_confirmation_tpl_desc'] = 'Resource\'s ID as the email template for the unsubscription.';
$_lang['setting_virtunewsletter.unsubscribe_succeeded_tpl'] = 'Unsubscription\'s confirmation succeeded tpl' ;
$_lang['setting_virtunewsletter.unsubscribe_succeeded_tpl_desc'] = 'Resource\'s ID as the email template for the completed confirmation of the unsubscription.' ;
$_lang['setting_virtunewsletter.email_debug'] = 'Email debug mode';
$_lang['setting_virtunewsletter.email_debug_desc'] = 'Turn this on to dump the email\'s placeholders to MODX\'s error log without sending the email.';
$_lang['setting_virtunewsletter.email_prefix'] = 'Placeholder\'s prefix in email';
$_lang['setting_virtunewsletter.email_prefix_desc'] = 'Placeholder\'s prefix for the output\'s values in the email\'s body of the newsletter';
$_lang['setting_virtunewsletter.use_csstoinlinestyles'] = 'Use CssToInlineStyles Class';
$_lang['setting_virtunewsletter.use_csstoinlinestyles_desc'] = 'Use CssToInlineStyles Class to process automatic css classes insertion into the mail body. This does not work well in some character sets.';