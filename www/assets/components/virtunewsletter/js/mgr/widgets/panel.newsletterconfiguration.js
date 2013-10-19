VirtuNewsletter.panel.NewsletterConfiguration = function(config) {
    config = config || {};

    Ext.QuickTips.init();

    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        border: false,
        dateFormat: 'U',
        displayFormat: 'm/d/Y',
        items: [
            {
                xtype: 'hidden',
                fieldLabel: _('id'),
                name: 'id',
                value: config.record &&
                        config.record.newsid ? config.record.newsid : 0
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
                                value: config.record &&
                                        config.record.subject ? config.record.subject : ''
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
                                value: config.record &&
                                        config.record.resource_id ? config.record.resource_id : ''
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
                                value: config.record &&
                                        config.record.scheduled_for ? config.record.scheduled_for : ''
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
                                checked: config.record &&
                                        config.record.is_recurring ? config.record.is_recurring : 0,
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
                                                recurrenceNumber.setValue('');
                                                recurrenceNumber.clearInvalid();
                                                recurrenceRange.disable();
                                                recurrenceRange.allowBlank = true;
                                                recurrenceRange.setValue('');
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
                                value: config.record &&
                                        config.record.recurrence_number ? config.record.recurrence_number : ''
                            }, {
                                xtype: 'virtunewsletter-combo-recurrence-range',
                                fieldLabel: _('virtunewsletter.by'),
                                name: 'recurrence_range',
                                value: config.record &&
                                        config.record.recurrence_range ? config.record.recurrence_range : ''
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
                                        recurrenceNumber.setValue('');
                                        recurrenceNumber.clearInvalid();
                                        recurrenceRange.disable();
                                        recurrenceRange.allowBlank = true;
                                        recurrenceRange.setValue('');
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
                                record: config.record ? config.record : '',
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
                checked: config.record &&
                        config.record.is_active ? 1 : 0
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
                        record: config.record
                    });
                    return newsTest.show();
                }
            }
        ]
    });

    VirtuNewsletter.panel.NewsletterConfiguration.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.NewsletterConfiguration, MODx.FormPanel, {
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
                        this.pageMask.hide();
                        Ext.getCmp('virtunewsletter-tree-newsletters').refreshNode(this.config.record.id);
                        return this.refreshNewsletterPanel(response.object.id);
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
    refreshNewsletterPanel: function(newsId) {
        MODx.Ajax.request({
            url: VirtuNewsletter.config.connectorUrl + '?action=mgr/newsletters/get',
            params: {
                id: newsId
            },
            listeners: {
                'success': {
                    fn: function(response) {
                        var record = response.object;
                        var date = Date.parseDate(record.scheduled_for, this.config.dateFormat);
                        record.scheduled_for = date.format(this.config.displayFormat);
                        record.newsid = record.id;
                        return Ext.getCmp('virtunewsletter-tree-newsletters').newslettersPanel(record);
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
                id: this.config.record.newsid
            },
            listeners: {
                'success': {
                    fn: function() {
                        var newslettersTree = Ext.getCmp('virtunewsletter-tree-newsletters');
                        newslettersTree.refreshNode(this.config.record.id);
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
        window.open(VirtuNewsletter.config.webConnectorUrl + '?action=web/newsletters/read&newsid=' + this.record.newsid + '&preventCache=' + time, '_blank');
    }
});
Ext.reg('virtunewsletter-panel-newsletter-configuration', VirtuNewsletter.panel.NewsletterConfiguration);