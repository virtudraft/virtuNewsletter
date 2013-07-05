VirtuNewsletter.panel.NewsletterContent = function(config) {
    config = config || {};

    Ext.apply(config, {
        border: false,
        baseCls: 'modx-formpanel',
        items: [
            {
                xtype: 'modx-tabs',
                defaults: {
                    border: false
                },
                bodyStyle: 'padding:20px;',
                border: true,
                items: [
                    {
                        title: _('virtunewsletter.configurations'),
                        preventRender: true,
                        xtype: 'virtunewsletter-panel-newsletter-configuration',
                        node: config.node
                    }, {
                        title: _('virtunewsletter.reports'),
                        preventRender: true,
                        xtype: 'virtunewsletter-grid-reports',
                        newsletter_id: config.node &&
                                config.node.attributes &&
                                config.node.attributes.newsid ?
                                config.node.attributes.newsid : ''
                    }
                ]
            }
        ]
    });
    VirtuNewsletter.panel.NewsletterContent.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.NewsletterContent, MODx.Panel);
Ext.reg('virtunewsletter-panel-newsletter-content', VirtuNewsletter.panel.NewsletterContent);