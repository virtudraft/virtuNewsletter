VirtuNewsletter.panel.Subscribers = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        id: 'virtunewsletter-panel-subscribers',
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
    VirtuNewsletter.panel.Subscribers.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.Subscribers, MODx.Panel);
Ext.reg('virtunewsletter-panel-subscribers', VirtuNewsletter.panel.Subscribers);