VirtuNewsletter.panel.Category = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        baseCls: 'modx-formpanel',
        layout: 'form',
        labelAlign: 'left',
        labelWidth: 100,
        items: [
            {
                xtype: 'hidden',
                fieldLabel: _('id'),
                name: 'id'
            }, {
                // will be used for the grid below
                xtype: 'hidden',
                name: 'usergroups'
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
                items: [
                    {
                        xtype: 'virtunewsletter-grid-localusergroups',
                        id: 'virtunewsletter-grid-localusergroups',
                        fieldLabel: _('virtunewsletter.usergroups'),
                        data: config.record && config.record.usergroups_grid ? config.record.usergroups_grid : [],
                        dataObject: config.record && config.record.usergroups_objects? config.record.usergroups_objects : {},
                        anchor: '100%'
                    }
                ]
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

    this.on('beforeSubmit', function (form, options, config) {
        var grid = Ext.getCmp('virtunewsletter-grid-localusergroups');
        var store = grid.getStore();
        var usergroups = [];
        for (var i = 0, l = store.data.items.length; i < l; i++) {
            usergroups.push(store.data.items[i].data.usergroup_id);
        }
        this.getForm().findField('usergroups').setValue(usergroups.join(','));

        return true;
    }, this);

};

Ext.extend(VirtuNewsletter.panel.Category, MODx.FormPanel);
Ext.reg('virtunewsletter-panel-category', VirtuNewsletter.panel.Category);