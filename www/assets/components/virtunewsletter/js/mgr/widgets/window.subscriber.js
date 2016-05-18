VirtuNewsletter.window.Subscriber = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        autoHeight: true,
        preventRender: true,
        labelAlign: 'left',
        fields: [
            {
                xtype: 'hidden',
                name: 'id'
            }, {
                xtype: 'hidden',
                name: 'is_active'
            }, {
                xtype: 'textfield',
                fieldLabel: _('name'),
                name: 'name'
            }, {
                xtype: 'textfield',
                fieldLabel: _('email'),
                name: 'email'
            }, {
                xtype: 'textfield',
                fieldLabel: _('virtunewsletter.email_provider'),
                name: 'email_provider'
            }, {
                xtype: 'displayfield',
                cls: 'desc-under',
                html: _('virtunewsletter.email_provider_desc'),
            }
        ]
    });

    VirtuNewsletter.window.Subscriber.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.window.Subscriber, MODx.Window);
Ext.reg('virtunewsletter-window-subscriber', VirtuNewsletter.window.Subscriber);