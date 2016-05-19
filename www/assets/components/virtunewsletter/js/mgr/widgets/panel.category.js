VirtuNewsletter.panel.Category = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        baseCls: 'modx-formpanel',
        bodyStyle: 'padding: 10px;',
        border: false,
        layout: 'form',
        labelAlign: 'left',
        labelWidth: 100,
        items: [
            {
                xtype: 'hidden',
                fieldLabel: _('id'),
                name: 'id'
            }, {
                anchor: '100%',
                xtype: 'textfield',
                fieldLabel: _('virtunewsletter.category'),
                name: 'name'
            }, {
                anchor: '100%',
                xtype: 'textarea',
                fieldLabel: _('virtunewsletter.description'),
                name: 'description'
            }, {
                anchor: '100%',
                xtype: 'virtunewsletter-combo-sbusergroups',
                fieldLabel: _('virtunewsletter.usergroups'),
                name: 'usergroups[]'
            }
        ],
        tbar: [
            '->', {
                text: _('virtunewsletter.save'),
                handler: function(){
                    this.submit();
                },
                scope: this
            }
        ]
    });
    VirtuNewsletter.panel.Category.superclass.constructor.call(this, config);
};

Ext.extend(VirtuNewsletter.panel.Category, MODx.FormPanel);
Ext.reg('virtunewsletter-panel-category', VirtuNewsletter.panel.Category);