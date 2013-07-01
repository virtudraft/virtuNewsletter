VirtuNewsletter.window.Category = function(config) {
    config = config || {};

    var check = Ext.getCmp('virtunewsletter-window-category');
    if (check) {
        check.destroy();
    }
    Ext.applyIf(config, {
        id: 'virtunewsletter-window-category',
        url: VirtuNewsletter.config.connectorUrl,
        autoHeight: true,
        preventRender: true,
        fields: [
            {
                xtype: 'hidden',
                name: 'id'
            }, {
                xtype: 'textfield',
                fieldLabel: _('virtunewsletter.name') + ':',
                name: 'name',
                anchor: '100%'
            }, {
                xtype: 'textarea',
                fieldLabel: _('virtunewsletter.description') + ':',
                name: 'description',
                anchor: '100%'
            }, {
                // will be used for the grid below
                xtype: 'hidden',
                name: 'usergroups'
            }, {
                xtype: 'virtunewsletter-grid-usergroups',
                fieldLabel: _('virtunewsletter.usergroups') + ':',
                record: config.record ? config.record : '',
                anchor: '100%',
                tbar: [
                    {
                        xtype: 'virtunewsletter-combo-usergroups',
                        anchor: '100%',
                        lazyRender: true
                    }, {
                        text: _('virtunewsletter.add'),
                        handler: function() {
                            var topToolbar = this.getTopToolbar();
                            var ugCombo = topToolbar.items.items[0];
                            var usergroup = ugCombo.getValue();
                            var text = ugCombo.lastSelectionText;
                            if (usergroup) {
                                this.data.push([usergroup, text]);
                                this.getStore().loadData(this.data);
                                this.getView().refresh();
                            }
                        }
                    }, {
                        text: _('virtunewsletter.clear'),
                        handler: function() {
                            var topToolbar = this.getTopToolbar();
                            topToolbar.items.items[0].setValue('');
                        }
                    }
                ]
            }
        ],
        listeners: {
            beforeSubmit: {
                fn: function(values) {
                    var ugGrid = Ext.getCmp('virtunewsletter-grid-usergroups');
                    var store = ugGrid.getStore();
                    var usergroups = [];
                    for (var i = 0, l = store.data.items.length; i < l; i++) {
                        usergroups.push(store.data.items[i].data.usergroup_id);
                    }
                    values['usergroups'] = usergroups;
                    this.fp.getForm().setValues(values);

                    return true;
                },
                scope: this
            }
        }
    });
    VirtuNewsletter.window.Category.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.window.Category, MODx.Window);
Ext.reg('virtunewsletter-window-category', VirtuNewsletter.window.Category);