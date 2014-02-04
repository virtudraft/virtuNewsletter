VirtuNewsletter.window.Schedule = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        fields: [
            {
                xtype: 'hidden',
                name: 'id'
            }, {
                xtype: 'textfield',
                fieldLabel: _('virtunewsletter.subject'),
                name: 'subject',
                allowBlank: false,
                anchor: '100%'
            }, {
                layout: 'column',
                columns: 2,
                defaults: {
                    layout: 'form'
                },
                items: [
                    {
                        columnWidth: .5,
                        items: [
                            {
                                xtype: 'numberfield',
                                fieldLabel: _('virtunewsletter.resource_id'),
                                name: 'resource_id',
                                allowBlank: false
                            }
                        ]
                    }, {
                        columnWidth: .5,
                        items: [
                            {
                                xtype: 'datefield',
                                fieldLabel: _('virtunewsletter.scheduled_for'),
                                name: 'scheduled_for'
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
                                listeners: {
                                    check: {
                                        fn: function(cb, checked) {
                                            var recurrenceNumber = this.fp.getForm().findField('recurrence_number');
                                            var recurrenceRange = this.fp.getForm().findField('recurrence_range');
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
                                                recurrenceNumber.value = '';
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
                                name: 'recurrence_number'
                            }, {
                                xtype: 'virtunewsletter-combo-recurrence-range',
                                fieldLabel: _('virtunewsletter.by'),
                                name: 'recurrence_range'
                            }
                        ],
                        listeners: {
                            'render': {
                                fn: function(obj) {
                                    // for initial loading
                                    var isRecurring = this.fp.getForm().findField('is_recurring');
                                    var recurrenceNumber = this.fp.getForm().findField('recurrence_number');
                                    var recurrenceRange = this.fp.getForm().findField('recurrence_range');
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
                    }
                ]
            }, {
                // will be used for the grid below
                xtype: 'hidden',
                name: 'categories'
            }, {
                xtype: 'virtunewsletter-grid-categories',
                fieldLabel: _('virtunewsletter.categories') + ':',
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
            }, {
                xtype: 'xcheckbox',
                boxLabel: _('virtunewsletter.active'),
                name: 'is_active',
                anchor: '100%'
            }
        ],
        listeners: {
            beforeSubmit: {
                fn: function(values) {
                    var grid = Ext.getCmp('virtunewsletter-grid-categories');
                    var store = grid.getStore();
                    var categories = [];
                    for (var i = 0, l = store.data.items.length; i < l; i++) {
                        if (store.data.items[i].data.category_id !== 0) {
                            categories.push(store.data.items[i].data.category_id);
                        }
                    }
                    values['categories'] = categories;
                    /**
                     * typecasting
                     * for some reason, xcheckbox fails on form submission.
                     * it went uncheck each time.
                     */
                    values['is_recurring'] = values['is_recurring'] - 0;
                    values['is_active'] = values['is_active'] - 0;
                    this.fp.getForm().setValues(values);

                    return true;
                },
                scope: this
            }
        }
    });
    VirtuNewsletter.window.Schedule.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.window.Schedule, MODx.Window);
Ext.reg('virtunewsletter-window-schedule', VirtuNewsletter.window.Schedule);