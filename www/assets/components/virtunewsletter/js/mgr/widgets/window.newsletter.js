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
                                name: 'resource_id'
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
                                            if (checked) {
                                                this.fp.getForm().findField('recurrence_times').enable();
                                                this.fp.getForm().findField('recurrence_unit').enable();
                                            } else {
                                                this.fp.getForm().findField('recurrence_times').disable();
                                                this.fp.getForm().findField('recurrence_unit').disable();
                                            }
                                        },
                                        scope: this
                                    }
                                }
                            }, {
                                xtype: 'numberfield',
                                fieldLabel: _('virtunewsletter.number_of_times'),
                                name: 'recurrence_times'
                            }, {
                                xtype: 'virtunewsletter-combo-recurrenceunit',
                                fieldLabel: _('virtunewsletter.by'),
                                name: 'recurrence_unit'
                            }
                        ],
                        listeners: {
                            'render': {
                                fn: function(obj) {
                                    // for initial loading
                                    var isRecurring = this.fp.getForm().findField('is_recurring');
                                    if (!isRecurring.value) {
                                        this.fp.getForm().findField('recurrence_times').disable();
                                        this.fp.getForm().findField('recurrence_unit').disable();
                                    } else {
                                        this.fp.getForm().findField('recurrence_times').enable();
                                        this.fp.getForm().findField('recurrence_unit').enable();
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
            }
        ],
        listeners: {
            beforeSubmit: {
                fn: function(values) {
                    var grid = Ext.getCmp('virtunewsletter-grid-categories');
                    var store = grid.getStore();
                    var categories = [];
                    for (var i = 0, l = store.data.items.length; i < l; i++) {
                        categories.push(store.data.items[i].data.category_id);
                    }
                    values['categories'] = categories;
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