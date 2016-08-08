VirtuNewsletter.panel.Category = function (config) {
    config = config || {};

    var allUsergroups = [];
    if (config.record && config.record.allUsergroups) {
        for (var key in config.record.allUsergroups) {
            if (config.record.allUsergroups.hasOwnProperty(key)) {
                key = Number(key);
                allUsergroups.push({
                    xtype: 'xcheckbox',
                    boxLabel: config.record.allUsergroups[key],
                    name: 'usergroups['+key+']',
                    value: key,
                    checked: config.record &&
                            config.record.usergroups &&
                            config.record.usergroups.indexOf(key) > -1 ? true : false
                });
            }
        }
    }

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
                fieldLabel: _('virtunewsletter.usergroups'),
                itemCls: 'x-check-group-alt',
                layout: 'hbox',
                items: allUsergroups
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