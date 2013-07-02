VirtuNewsletter.panel.NewsletterConfiguration = function(config) {
    config = config || {};

    Ext.QuickTips.init();

    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        cls: 'container',
        layout: 'anchor',
        border: false,
        items: [
            {
                xtype: 'hidden',
                fieldLabel: _('id'),
                name: 'id',
                value: config.node &&
                        config.node.attributes &&
                        config.node.attributes.newsid ? config.node.attributes.newsid : 0
            }, {
                // will be used for the grid below
                xtype: 'hidden',
                name: 'categories'
            }, {
                layout: 'column',
                columns: 2,
                defaults: {
                    layout: 'form',
                    border: false
                },
                items: [
                    {
                        columnWidth: 1,
                        items: [
                            {
                                xtype: 'textfield',
                                fieldLabel: _('virtunewsletter.subject'),
                                name: 'subject',
                                anchor: '100%',
                                value: config.node &&
                                        config.node.attributes &&
                                        config.node.attributes.subject ? config.node.attributes.subject : ''
                            }
                        ]
                    }, {
                        columnWidth: .5,
                        items: [
                            {
                                xtype: 'numberfield',
                                fieldLabel: _('virtunewsletter.resource_id'),
                                name: 'resource_id',
                                value: config.node &&
                                        config.node.attributes &&
                                        config.node.attributes.resource_id ? config.node.attributes.resource_id : ''
                            }
                        ]
                    }, {
                        columnWidth: .5,
                        items: [
                            {
                                xtype: 'datefield',
                                fieldLabel: _('virtunewsletter.scheduled_for'),
                                name: 'scheduled_for',
                                value: config.node &&
                                        config.node.attributes &&
                                        config.node.attributes.scheduled_for ? config.node.attributes.scheduled_for : ''
                            }
                        ]
                    }, {
                        columnWidth: 1,
                        layout: 'hbox',
                        defaults: {
                            flex: 1
                        },
                        items: [
                            {
                                xtype: 'xcheckbox',
                                boxLabel: _('virtunewsletter.is_recurring'),
                                name: 'is_recurring',
                                value: config.node &&
                                        config.node.attributes &&
                                        config.node.attributes.is_recurring ? config.node.attributes.is_recurring : 0,
                                listeners: {
                                    check: {
                                        fn: function(cb, checked) {
                                            if (checked) {
                                                this.form.findField('recurrence_times').enable();
                                                this.form.findField('recurrence_unit').enable();
                                            } else {
                                                this.form.findField('recurrence_times').disable();
                                                this.form.findField('recurrence_unit').disable();
                                            }
                                        },
                                        scope: this
                                    }
                                }
                            }, {
                                xtype: 'numberfield',
                                fieldLabel: _('virtunewsletter.number_of_times'),
                                name: 'recurrence_times',
                                value: config.node &&
                                        config.node.attributes &&
                                        config.node.attributes.recurrence_times ? config.node.attributes.recurrence_times : ''
                            }, {
                                xtype: 'virtunewsletter-combo-recurrenceunit',
                                fieldLabel: _('virtunewsletter.by'),
                                name: 'recurrence_unit',
                                value: config.node &&
                                        config.node.attributes &&
                                        config.node.attributes.recurrence_unit ? config.node.attributes.recurrence_unit : ''
                            }
                        ],
                        listeners: {
                            'render': {
                                fn: function(obj) {
                                    // for initial loading
                                    var isRecurring = this.form.findField('is_recurring');
                                    if (!isRecurring.value) {
                                        this.form.findField('recurrence_times').disable();
                                        this.form.findField('recurrence_unit').disable();
                                    } else {
                                        this.form.findField('recurrence_times').enable();
                                        this.form.findField('recurrence_unit').enable();
                                    }
                                },
                                scope: this
                            }
                        }
                    }, {
                        columnWidth: 1,
                        items: [
                            {
                                xtype: 'virtunewsletter-grid-categories',
                                fieldLabel: _('virtunewsletter.categories'),
                                node: config.node ? config.node : '',
                                anchor: '100%',
                                tbar: [
                                    {
                                        xtype: 'virtunewsletter-combo-categories',
                                        anchor: '100%',
                                        lazyRender: true
                                    }, {
                                        text: _('virtunewsletter.add'),
                                        handler: function() {
                                            var topToolbar = this.getTopToolbar();
                                            var combo = topToolbar.items.items[0];
                                            var comboValue = combo.getValue();
                                            var text = combo.lastSelectionText;
                                            if (comboValue) {
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
                handler: this.updateNewsletter,
                scope: this
            }, {
                text: _('virtunewsletter.remove'),
                handler: this.removeNewsletter,
                scope: this
            }, {
                text: _('virtunewsletter.view'),
                handler: this.viewContent,
                scope: this
            }, {
                text: _('virtunewsletter.close'),
                handler: this.cleanCenter,
                scope: this
            }
        ]
    });
    VirtuNewsletter.panel.NewsletterConfiguration.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.NewsletterConfiguration, MODx.FormPanel, {
    updateNewsletter: function(btn, evt) {
        var values = this.form.getValues();
        var grid = Ext.getCmp('virtunewsletter-grid-categories');
        var store = grid.getStore();

        var categories = [];
        for (var i = 0, l = store.data.items.length; i < l; i++) {
            categories.push(store.data.items[i].data.category_id);
        }
        values['categories'] = categories.join(',');

        if (!this.pageMask) {
            this.pageMask = new Ext.LoadMask(Ext.getBody(), {
                msg: _('virtunewsletter.please_wait')
            });
        }
        this.pageMask.show();

        MODx.Ajax.request({
            url: VirtuNewsletter.config.connectorUrl + '?action=mgr/newsletters/update',
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
    removeNewsletter: function() {
        var node = this.cm.activeNode;

        MODx.msg.confirm({
            title: _('virtunewsletter.remove'),
            text: _('virtunewsletter.remove_confirm'),
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/newsletter/remove',
                id: node.attributes.newsid
            },
            listeners: {
                'success': {
                    fn: function() {
                        this.refreshNode(node.id);
                        var contentPanel = Ext.getCmp('virtunewsletter-panel-newsletter-center');
                        contentPanel.removeAll();
                        var container = Ext.getCmp('modx-content');
                        return container.doLayout();
                    },
                    scope: this
                }
            }
        });
    },
    cleanCenter: function() {
        var contentPanel = Ext.getCmp('virtunewsletter-panel-newsletter-center');
        contentPanel.removeAll();
        var container = Ext.getCmp('modx-content');
        return container.doLayout();
    },
    viewContent: function() {
        var time = new Date().getTime();
        window.open(VirtuNewsletter.config.webConnectorUrl + '?action=web/newsletters/read&newsid=' + this.node.attributes.newsid + '&preventCache=' + time, '_blank');
    }
});
Ext.reg('virtunewsletter-panel-newsletter-configuration', VirtuNewsletter.panel.NewsletterConfiguration);