VirtuNewsletter.window.Subscriber = function (config) {
    config = config || {};

    var allCategories = [];
    if (config.record && config.record.allCategories) {
        for (var key in config.record.allCategories) {
            key = Number(key);
            if (config.record.allCategories.hasOwnProperty(key)) {
                allCategories.push({
                    xtype: 'xcheckbox',
                    boxLabel: config.record.allCategories[key],
                    name: 'categories['+key+']',
                    value: key
                });
            }
        }
    }

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
                name: 'is_active',
                anchor: '100%'
            }, {
                xtype: 'textfield',
                fieldLabel: _('name'),
                name: 'name',
                anchor: '100%'
            }, {
                xtype: 'textfield',
                fieldLabel: _('email'),
                name: 'email',
                anchor: '100%'
            }, {
                xtype: 'textfield',
                fieldLabel: _('virtunewsletter.email_provider'),
                name: 'email_provider',
                anchor: '100%'
            }, {
                xtype: 'displayfield',
                cls: 'desc-under',
                html: _('virtunewsletter.email_provider_desc'),
            }, {
                fieldLabel: _('virtunewsletter.categories'),
                itemCls: 'x-check-group-alt',
                items: allCategories
            }
        ]
    });

    VirtuNewsletter.window.Subscriber.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.window.Subscriber, MODx.Window);
Ext.reg('virtunewsletter-window-subscriber', VirtuNewsletter.window.Subscriber);