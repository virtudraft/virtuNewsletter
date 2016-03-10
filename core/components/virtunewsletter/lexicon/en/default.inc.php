<?php

/**
 * virtuNewsletter
 *
 * Copyright 2013-2016 by goldsky <goldsky@virtudraft.com>
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
$_lang['virtunewsletter.subscriber_err_ns'] = 'Unspecified email address.';
$_lang['virtunewsletter.subscriber_err_invalid_email'] = 'Invalid email address.';
$_lang['virtunewsletter.subscriber_err_save'] = 'There was an unexpected result to subscribe you. Please try again.';
$_lang['virtunewsletter.subscriber_suc_save'] = 'Thank you. You are now registered as our subscriber. Please check your mailbox to activate the subscription.';
$_lang['virtunewsletter.subscriber_err_proc'] = 'There was an error to process your subscription. Please try again.';
$_lang['virtunewsletter.subscriber_err_ne'] = 'This subscriber does not exists.';
$_lang['virtunewsletter.subscriber_suc_activated'] = 'Your account has been activated successfully.';
$_lang['virtunewsletter.subscriber_suc_deactivated'] = 'Your account has been deactivated successfully.';
$_lang['virtunewsletter.subscriber_unsubscribing'] = 'You are about to unsubscribe from our newsletter. Please check your mailbox and click the link on our sent email to confirm about this unsubscription.';

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
$_lang['setting_virtunewsletter.use_csstoinlinestyles_desc'] = 'Use CssToInlineStyles Class to process automatic css classes insertion into the mail body. This does not work well for some character sets.';
$_lang['setting_virtunewsletter.assets_url'] = 'Web URL to assets folder';
$_lang['setting_virtunewsletter.assets_url_desc'] = 'Set relative URL to assets folder of component';
$_lang['setting_virtunewsletter.core_path'] = 'Path to core folder';
$_lang['setting_virtunewsletter.core_path_desc'] = 'Set absolute path to core folder of component';
$_lang['setting_virtunewsletter.email_bcc_address'] = 'BCC address for newsletter';
$_lang['setting_virtunewsletter.email_bcc_address_desc'] = 'BCC address when newsletter is sent.';
$_lang['setting_virtunewsletter.email_from_name'] = 'Name of the sender';
$_lang['setting_virtunewsletter.email_from_name_desc'] = 'Name of the sender for newsletter';
$_lang['setting_virtunewsletter.email_provider'] = 'Email provider';
$_lang['setting_virtunewsletter.email_provider_desc'] = 'Email provider which generates the newsletter. Default: <em>empty</em> (this website), built-in options: <a href="http://mandrill.com/" title="Mandrill is a transactional email platform from MailChimp" target="_blank">"mandrill"</a> and <a href="https://mailgun.com/" title="Transactional Email API Service for Developers by Rackspace" target="_blank">"mailgun"</a>.';
$_lang['setting_virtunewsletter.email_reply_to'] = 'Email address to reply';
$_lang['setting_virtunewsletter.email_reply_to_desc'] = 'Email address for the recipients to reply';
$_lang['setting_virtunewsletter.mandrill.api_key'] = 'Mandrill\'s API key';
$_lang['setting_virtunewsletter.mandrill.api_key_desc'] = 'If you are using Mandrill\'s service, get the Mandrill\'s API key from its <a href="http://mandrill.com/" title="Mandrill is a transactional email platform from MailChimp" target="_blank">website</a>.';
$_lang['setting_virtunewsletter.mailgun.api_key'] = 'Mailgun\'s API key';
$_lang['setting_virtunewsletter.mailgun.api_key_desc'] = 'If you are using Mailgun\'s service, get the Mailgun\'s API key from its <a href="http://mailgun.com/" title="Transactional Email API Service for Developers by Rackspace - Mailgun" target="_blank">website</a>.';
$_lang['setting_virtunewsletter.mailgun.endpoint'] = 'Mailgun\'s URL Endpoint';
$_lang['setting_virtunewsletter.mailgun.endpoint_desc'] = 'Mailgun\'s API Base URL for the validated domain. Check <a href="https://documentation.mailgun.com/api-intro.html#base-url" target="_blank">API</a>.';
$_lang['setting_virtunewsletter.sync_default_activation'] = 'Default activation on sync';
$_lang['setting_virtunewsletter.sync_default_activation_desc'] = '0: inactive, 1: active, 2: follow user\'s active status. Default: 0';
$_lang['setting_virtunewsletter.sync_include_inactive_users'] = 'Include inactive users on sync';
$_lang['setting_virtunewsletter.sync_include_inactive_users_desc'] = 'No: will skip inactive users. Default: Yes';
