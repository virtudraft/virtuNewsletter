VirtuNewsletter.panel.Subscribers = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        id: 'virtunewsletter-panel-subscribers',
        border: false,
        baseCls: 'modx-formpanel',
        defaults: {
            border: false
        },
        bodyStyle: 'overflow-y: scroll;',
        items: [
            {
                html: '<p>' + _('virtunewsletter.subscribers_desc') + '</p>',
                border: false,
                bodyCssClass: 'panel-desc'
            }, {
                xtype: 'virtunewsletter-grid-subscribers',
                cls: 'main-wrapper',
                preventRender: true
            }
        ],
        listeners: {
            beforerender: {
                fn: function(panel) {
                    var homeCenter = Ext.getCmp('virtunewsletter-panel-home-center');
                    panel.height = homeCenter.lastSize.height;
                },
                scope: this
            }
        }
    });
    VirtuNewsletter.panel.Subscribers.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.Subscribers, MODx.Panel);
Ext.reg('virtunewsletter-panel-subscribers', VirtuNewsletter.panel.Subscribers);