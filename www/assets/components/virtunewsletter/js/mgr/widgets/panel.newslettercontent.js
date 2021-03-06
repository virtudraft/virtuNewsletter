VirtuNewsletter.panel.NewsletterContent = function (config) {
    config = config || {};

    var tabItems = [
        {
            title: _('virtunewsletter.configurations'),
            preventRender: true,
            xtype: 'virtunewsletter-panel-newsletter-configuration',
            record: config.record
        }
    ];
    if (config.record && config.record.id) {
        tabItems.push({
            title: _('virtunewsletter.reports'),
            preventRender: true,
            xtype: 'virtunewsletter-grid-reports',
            record: config.record
        });
    }
    if (config.record.is_recurring) {
        tabItems.push({
            title: _('virtunewsletter.recurrences'),
            preventRender: true,
            xtype: 'virtunewsletter-grid-recurrences',
            record: config.record
        });
    }
    Ext.applyIf(config, {
        defaults: {
            border: false
        },
        border: true,
        autoHeight: true,
        items: tabItems
    });
    VirtuNewsletter.panel.NewsletterContent.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.NewsletterContent, MODx.Tabs);
Ext.reg('virtunewsletter-panel-newsletter-content', VirtuNewsletter.panel.NewsletterContent);