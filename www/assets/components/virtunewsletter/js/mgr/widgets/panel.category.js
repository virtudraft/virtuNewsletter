VirtuNewsletter.panel.Category = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        items: [
            {
                xtype: 'hidden',
                fieldLabel: _('id'),
                name: 'id',
                value: config.record &&
                        config.record.catid ? config.record.catid : 0
            }, {
                // will be used for the grid below
                xtype: 'hidden',
                name: 'usergroups'
            }, {
                anchor: '100%',
                layout: 'column',
                defaults: {
                    layout: 'form',
                    border: false
                },
                items: [
                    {
                        columnWidth: 1,
                        items: [
                            {
                                anchor: '100%',
                                xtype: 'textfield',
                                fieldLabel: _('virtunewsletter.category'),
                                name: 'name',
                                value: config.record &&
                                        config.record.name ? config.record.name : ''
                            }
                        ]
                    }, {
                        columnWidth: 1,
                        items: [
                            {
                                anchor: '100%',
                                xtype: 'textarea',
                                fieldLabel: _('virtunewsletter.description'),
                                name: 'description',
                                value: config.record &&
                                        config.record.description ? config.record.description : ''
                            }
                        ]
                    }, {
                        columnWidth: 1,
                        items: [
                            {
                                xtype: 'virtunewsletter-grid-usergroups',
                                fieldLabel: _('virtunewsletter.usergroups'),
                                record: config.record ? config.record : '',
                                anchor: '100%',
                                tbar: [
                                    {
                                        xtype: 'virtunewsletter-combo-usergroups',
                                        anchor: '100%'
                                    }, {
                                        text: _('virtunewsletter.add'),
                                        handler: function() {
                                            var topToolbar = this.getTopToolbar();
                                            var combo = topToolbar.items.items[0];
                                            var comboValue = combo.getValue();
                                            var text = combo.lastSelectionText;
                                            if (comboValue && this.data.in_array([comboValue, text]) === false) {
                                                this.data.push([comboValue, text]);
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
                        ]
                    }
                ]
            }
        ],
        tbar: [
            '->', {
                text: _('virtunewsletter.save'),
                handler: this.updateCategory,
                scope: this
            }, {
                text: _('virtunewsletter.close'),
                handler: this.cleanCenter,
                scope: this
            }
        ]
    });
    VirtuNewsletter.panel.Category.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.Category, MODx.FormPanel, {
    updateCategory: function(btn, evt) {
        var values = this.form.getValues();
        var grid = Ext.getCmp('virtunewsletter-grid-usergroups');
        var store = grid.getStore();

        var usergroups = [];
        for (var i = 0, l = store.data.items.length; i < l; i++) {
            usergroups.push(store.data.items[i].data.usergroup_id);
        }
        values['usergroups'] = usergroups.join(',');

        if (!this.pageMask) {
            this.pageMask = new Ext.LoadMask(Ext.getBody(), {
                msg: _('virtunewsletter.please_wait')
            });
        }
        this.pageMask.show();

        MODx.Ajax.request({
            url: VirtuNewsletter.config.connectorUrl + '?action=mgr/categories/update',
            params: values,
            listeners: {
                'success': {
                    fn: function() {
                        var newslettersTree = Ext.getCmp('virtunewsletter-tree-newsletters');
                        this.pageMask.hide();
                        return newslettersTree.refreshTree();
                    },
                    scope: this
                },
                'failure': {
                    fn: function() {
                        return this.pageMask.hide();
                    },
                    scope: this
                }
            }
        });
    },
    cleanCenter: function() {
        var contentPanel = Ext.getCmp('virtunewsletter-panel-newsletters-center');
        contentPanel.removeAll();
        var container = Ext.getCmp('modx-content');
        return container.doLayout();
    }
});
Ext.reg('virtunewsletter-panel-category', VirtuNewsletter.panel.Category);