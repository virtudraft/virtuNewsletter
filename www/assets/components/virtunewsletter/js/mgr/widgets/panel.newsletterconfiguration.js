VirtuNewsletter.panel.NewsletterConfiguration = function(config) {
    config = config || {};

    Ext.QuickTips.init();

    Ext.apply(config, {
        baseCls: 'modx-formpanel',
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
                                allowBlank: false,
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
                                allowBlank: false,
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
                                allowBlank: false,
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
                                checked: config.node &&
                                        config.node.attributes &&
                                        config.node.attributes.is_recurring ? config.node.attributes.is_recurring : 0,
                                listeners: {
                                    check: {
                                        fn: function(cb, checked) {
                                            var recurrenceNumber = this.form.findField('recurrence_number');
                                            var recurrenceRange = this.form.findField('recurrence_range');
                                            if (checked) {
                                                recurrenceNumber.enable();
                                                recurrenceNumber.allowBlank = false;
                                                if (recurrenceNumber.value === '') {
                                                    recurrenceNumber.markInvalid(_('virtunewsletter.newsletter_err_ns_recurrence_number'));
                                                }
                                                recurrenceRange.enable();
                                                recurrenceRange.allowBlank = false;
                                                if (recurrenceRange.value === '') {
                                                    recurrenceRange.markInvalid(_('virtunewsletter.newsletter_err_ns_recurrence_range'));
                                                }
                                            } else {
                                                recurrenceNumber.disable();
                                                recurrenceNumber.allowBlank = true;
                                                recurrenceNumber.value = '';
                                                recurrenceNumber.clearInvalid();
                                                recurrenceRange.disable();
                                                recurrenceRange.allowBlank = true;
                                                recurrenceRange.value = '';
                                                recurrenceRange.clearInvalid();
                                            }
                                        },
                                        scope: this
                                    }
                                }
                            }, {
                                xtype: 'numberfield',
                                fieldLabel: _('virtunewsletter.number_of_times'),
                                name: 'recurrence_number',
                                value: config.node &&
                                        config.node.attributes &&
                                        config.node.attributes.recurrence_number ? config.node.attributes.recurrence_number : ''
                            }, {
                                xtype: 'virtunewsletter-combo-recurrence-range',
                                fieldLabel: _('virtunewsletter.by'),
                                name: 'recurrence_range',
                                value: config.node &&
                                        config.node.attributes &&
                                        config.node.attributes.recurrence_range ? config.node.attributes.recurrence_range : ''
                            }
                        ],
                        listeners: {
                            'render': {
                                fn: function(obj) {
                                    // for initial loading
                                    var isRecurring = this.form.findField('is_recurring');
                                    var recurrenceNumber = this.form.findField('recurrence_number');
                                    var recurrenceRange = this.form.findField('recurrence_range');
                                    if (!isRecurring.checked) {
                                        recurrenceNumber.disable();
                                        recurrenceNumber.allowBlank = true;
                                        recurrenceNumber.value = '';
                                        recurrenceNumber.clearInvalid();
                                        recurrenceRange.disable();
                                        recurrenceRange.allowBlank = true;
                                        recurrenceRange.value = '';
                                        recurrenceRange.clearInvalid();
                                    } else {
                                        recurrenceNumber.enable();
                                        recurrenceNumber.allowBlank = false;
                                        if (recurrenceNumber.value === '') {
                                            recurrenceNumber.markInvalid(_('virtunewsletter.newsletter_err_ns_recurrence_number'));
                                        }
                                        recurrenceRange.enable();
                                        recurrenceRange.allowBlank = false;
                                        if (recurrenceRange.value === '') {
                                            recurrenceRange.markInvalid(_('virtunewsletter.newsletter_err_ns_recurrence_range'));
                                        }
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
                                // this tbar is for this panel only!
                                tbar: [
                                    {
                                        xtype: 'virtunewsletter-combo-categories',
                                        anchor: '100%',
                                        lazyRender: true
                                    }, {
                                        text: _('virtunewsletter.add'),
                                        handler: this.addCategory
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
            }, {
                xtype: 'xcheckbox',
                boxLabel: _('virtunewsletter.active'),
                name: 'is_active',
                anchor: '100%',
                checked: config.node &&
                        config.node.attributes &&
                        config.node.attributes.is_active ? 1 : 0
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
            }, {
                text: _('virtunewsletter.test'),
                handler: function() {
                    var newsTest = new VirtuNewsletter.window.NewsletterTest({
                        baseParams: {
                            action: 'mgr/newsletters/test'
                        },
                        node: config.node
                    });
                    return newsTest.show();
                }
            }
        ]
    });

    VirtuNewsletter.panel.NewsletterConfiguration.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.NewsletterConfiguration, MODx.FormPanel, {
    testNewsletter: function() {

    },
    addCategory: function() {
        var topToolbar = this.getTopToolbar();
        var combo = topToolbar.items.items[0];
        var comboValue = combo.getValue();
        var text = combo.lastSelectionText;
        if (comboValue) {
            this.data.push([comboValue, text]);
            this.getStore().loadData(this.data);
            this.getView().refresh();
        }
    },
    updateNewsletter: function(btn, evt) {
        var values = this.form.getValues();
        var grid = Ext.getCmp('virtunewsletter-grid-categories');
        var store = grid.getStore();

        var categories = [];
        for (var i = 0, l = store.data.items.length; i < l; i++) {
            if (store.data.items[i].data.category_id !== 0) {
                categories.push(store.data.items[i].data.category_id);
            }
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
                    fn: function(response) {
                        console.log('response', response);
                        var newslettersTree = Ext.getCmp('virtunewsletter-tree-newsletters');
                        this.pageMask.hide();
                        newslettersTree.refreshNode(this.config.node.attributes.id);
                        var node = this.config.node;
                        console.log('node', node);
                        newslettersTree.newslettersPanel(node);
                        return true;
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
        MODx.msg.confirm({
            title: _('virtunewsletter.remove'),
            text: _('virtunewsletter.remove_confirm'),
            url: VirtuNewsletter.config.connectorUrl + '?action=mgr/newsletters/remove',
            params: {
                id: this.config.node.attributes.newsid
            },
            listeners: {
                'success': {
                    fn: function() {
                        var newslettersTree = Ext.getCmp('virtunewsletter-tree-newsletters');
                        newslettersTree.refreshNode(this.config.node.attributes.id);
                        var contentPanel = Ext.getCmp('virtunewsletter-panel-newsletters-center');
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
        var contentPanel = Ext.getCmp('virtunewsletter-panel-newsletters-center');
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