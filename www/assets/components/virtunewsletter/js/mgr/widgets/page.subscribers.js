VirtuNewsletter.page.Subscribers = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        id: 'virtunewsletter-page-subscribers',
        border: false,
        autoHeight: true,
        defaults: {
            border: false
        },
        items: [
            {
                html: '<h2>' + _('virtunewsletter.subscribers') + '</h2>'
                    + '<p>' + _('virtunewsletter.subscribers_desc') + '</p>',
                border: false,
                bodyCssClass: 'panel-desc'
            }, {
                xtype: 'virtunewsletter-grid-subscribers',
                cls: 'main-wrapper',
                preventRender: true
            }
        ]
    });
    VirtuNewsletter.page.Subscribers.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.page.Subscribers, MODx.Panel);
Ext.reg('virtunewsletter-page-subscribers', VirtuNewsletter.page.Subscribers);