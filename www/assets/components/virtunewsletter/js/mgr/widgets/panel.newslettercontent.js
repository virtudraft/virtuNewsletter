VirtuNewsletter.panel.NewsletterContent = function(config) {
    config = config || {};

    var tabItems = [
        {
            title: _('virtunewsletter.configurations'),
            preventRender: true,
            xtype: 'virtunewsletter-panel-newsletter-configuration',
            node: config.node
        }, {
            title: _('virtunewsletter.reports'),
            preventRender: true,
            xtype: 'virtunewsletter-grid-reports',
            node: config.node
        }
    ];
    if (config.node.attributes.is_recurring) {
        tabItems.push({
            title: _('virtunewsletter.recurrences'),
            preventRender: true,
            xtype: 'virtunewsletter-grid-recurrences',
            node: config.node
        });
    }
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
                items: tabItems
            }
        ]
    });
    VirtuNewsletter.panel.NewsletterContent.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.NewsletterContent, MODx.Panel);
Ext.reg('virtunewsletter-panel-newsletter-content', VirtuNewsletter.panel.NewsletterContent);