VirtuNewsletter.panel.NewsletterConfiguration = function (config) {
    config = config || {};

    Ext.QuickTips.init();

    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        baseParams: {
            action: 'mgr/newsletters/' + (config.record && config.record.id ? 'update' : 'create'),
//            id: config.record && config.record.id ? config.record.id : 0
        },
        baseCls: 'modx-formpanel',
        border: false,
        dateFormat: 'U',
        displayFormat: 'm/d/Y',
        layout: 'form',
        labelAlign: 'left',
        labelWidth: 100,
        autoHeight: true,
        items: [
            {
                xtype: 'hidden',
                fieldLabel: _('id'),
                name: 'id',
                value: config.record && config.record.id ? config.record.id : 0
            }, {
                // will be used for the grid below
                xtype: 'hidden',
                name: 'categories'
            }, {
                xtype: 'textfield',
                fieldLabel: _('virtunewsletter.subject'),
                name: 'subject',
                allowBlank: false,
                anchor: '100%',
                value: config.record && config.record.subject ? config.record.subject : ''
            }, {
                layout: 'column',
                columns: 2,
                defaults: {
                    layout: 'form',
                    border: false
                },
                items: [
                    {
                        columnWidth: .5,
                        items: [
                            {
                                xtype: 'virtunewsletter-combo-resources',
                                anchor: '100%',
                                fieldLabel: _('virtunewsletter.resource_id'),
                                name: 'resource_id',
                                value: config.record && config.record.resource_id ? config.record.resource_id : ''
                            }
//                            {
//                                xtype: 'modx-field-parent-change',
//                                anchor: '100%',
//                                fieldLabel: _('virtunewsletter.resource_id'),
//                                name: 'resource_id2',
//                                value: config.record && config.record.resource_id ? config.record.resource_id : '',
//                                allowBlank: false
//                            }, {
//                                xtype: 'hidden',
//                                name: 'resource_id',
//                                value: config.record && config.record.resource_id ? config.record.resource_id : '',
//                                id: 'modx-resource-parent-hidden'
//                            }
                        ]
                    }, {
                        columnWidth: .5,
                        items: [
                            {
                                xtype: 'datefield',
                                fieldLabel: _('virtunewsletter.scheduled_for'),
                                name: 'scheduled_for',
                                allowBlank: true,
                                value: config.record &&
                                    config.record.scheduled_for &&
                                    config.record.scheduled_for > 0 ?
                                    config.record.scheduled_for :
                                    ''
                            }
                        ]
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
                        checked: config.record && config.record.is_recurring ? config.record.is_recurring : 0,
                        listeners: {
                            check: {
                                fn: function (cb, checked) {
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
                        value: config.record && config.record.recurrence_number ? config.record.recurrence_number : ''
                    }, {
                        xtype: 'virtunewsletter-combo-recurrence-range',
                        fieldLabel: _('virtunewsletter.by'),
                        name: 'recurrence_range',
                        value: config.record && config.record.recurrence_range ? config.record.recurrence_range : ''
                    }
                ],
                listeners: {
                    'render': {
                        fn: function (obj) {
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
                fieldLabel: _('virtunewsletter.categories'),
                items: [
                    {
                        xtype: 'virtunewsletter-grid-localcategories',
                        record: config.record ? config.record : {},
                        anchor: '100%'
                    }
                ]
            }, {
                xtype: 'xcheckbox',
                boxLabel: _('virtunewsletter.active'),
                name: 'is_active',
                anchor: '100%',
                checked: config.record && config.record.is_active ? 1 : 0
            }
        ],
        tbar: [
            '->', {
//                text: _('virtunewsletter.recache'),
                text: _('save'),
//                handler: this.updateNewsletter,
                handler: function(){
                    this.submit();
                },
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
                handler: function () {
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

    this.on('beforeSubmit', function (form, options, config) {
        var grid = Ext.getCmp('virtunewsletter-grid-localcategories');
        var store = grid.getStore();
        var categories = [];
        for (var i = 0, l = store.data.items.length; i < l; i++) {
            if (store.data.items[i].data.category_id !== 0) {
                categories.push(store.data.items[i].data.category_id);
            }
        }
        this.getForm().findField('categories').setValue(categories.join(','));

        return true;
    }, this);
};

Ext.extend(VirtuNewsletter.panel.NewsletterConfiguration, MODx.FormPanel, {
    removeNewsletter: function () {
        MODx.msg.confirm({
            title: _('virtunewsletter.remove'),
            text: _('virtunewsletter.remove_confirm'),
            url: VirtuNewsletter.config.connectorUrl + '?action=mgr/newsletters/remove',
            params: {
                id: this.config.record.id
            },
            listeners: {
                'success': {
                    fn: function () {
                        var container = Ext.getCmp('modx-content');
                        return container.doLayout();
                    },
                    scope: this
                }
            }
        });
    },
    cleanCenter: function () {
        var contentPanel = Ext.getCmp('virtunewsletter-panel-newsletters-center');
        contentPanel.removeAll();
        var container = Ext.getCmp('modx-content');
        return container.doLayout();
    },
    viewContent: function () {
        var time = new Date().getTime();
        window.open(VirtuNewsletter.config.webConnectorUrl + '?action=web/newsletters/read&newsid=' + this.record.id + '&preventCache=' + time, '_blank');
    }
});
Ext.reg('virtunewsletter-panel-newsletter-configuration', VirtuNewsletter.panel.NewsletterConfiguration);