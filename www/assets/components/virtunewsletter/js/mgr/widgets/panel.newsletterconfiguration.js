VirtuNewsletter.panel.NewsletterConfiguration = function (config) {
    config = config || {};

    Ext.QuickTips.init();

    var tbar = ['->'];
    tbar.push({
        text: _('save'),
        handler: this.submit,
        scope: this
    });
    if (config.record && config.record.id) {
        tbar.push({
            text: _('virtunewsletter.remove'),
            handler: this.removeNewsletter,
            scope: this
        });
        tbar.push({
            text: _('virtunewsletter.view'),
            handler: this.viewContent,
            scope: this
        });
        tbar.push({
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
        });
    }
    tbar.push({
        text: _('virtunewsletter.close'),
        handler: this.closeTab,
        scope: this
    });

    var allCategories = [];
    if (config.record && config.record.allCategories) {
        for (var key in config.record.allCategories) {
            if (config.record.allCategories.hasOwnProperty(key)) {
                key = Number(key);
                allCategories.push({
                    xtype: 'xcheckbox',
                    boxLabel: config.record.allCategories[key],
                    name: 'categories['+key+']',
                    value: key,
                    checked: config.record &&
                            config.record.categories &&
                            config.record.categories.indexOf(key) > -1 ? true : false
                });
            }
        }
    }

    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        baseParams: {
            action: 'mgr/newsletters/' + (config.record && config.record.id ? 'update' : 'create'),
//            id: config.record && config.record.id ? config.record.id : 0
        },
        baseCls: 'modx-formpanel',
        bodyStyle: 'overflow: hidden;',
        border: false,
        dateFormat: config.dateFormat || 'U',
        displayFormat: config.displayFormat || 'Y-m-d',
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
                                format: 'Y-m-d', // make it display correct but sends it to server as Y-m-d
                                dateFormat:'Y-m-d',
                                submitFormat: 'U',
                                renderer: Ext.util.Format.dateRenderer('Y-m-d'),
                                altFormats:'U|u|m/d/Y|n/j/Y|n/j/y|m/j/y|n/d/y|m/j/Y|n/d/Y|m-d-y|m-d-Y|m/d|m-d|md|mdy|mdY|d|Y-m-d|n-j|n/j',
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
                fieldLabel: _('virtunewsletter.is_recurring'),
                layout: 'hbox',
                items: [
                    {
                        flex: 0,
                        xtype: 'xcheckbox',
                        boxLabel: _('yes'),
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
                        flex: 0,
                        xtype: 'numberfield',
                        fieldLabel: _('virtunewsletter.number_of_times'),
                        name: 'recurrence_number',
                        value: config.record && config.record.recurrence_number ? config.record.recurrence_number : ''
                    }, {
                        flex: 0,
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
                itemCls: 'x-check-group-alt',
                items: allCategories
            }, {
                fieldLabel: _('virtunewsletter.active'),
                xtype: 'xcheckbox',
                boxLabel: _('yes'),
                name: 'is_active',
                anchor: '100%',
                checked: config.record && config.record.is_active ? 1 : 0
            }
        ],
        tbar: tbar
    });

    VirtuNewsletter.panel.NewsletterConfiguration.superclass.constructor.call(this, config);

    this.on('success', function(response) {
        var grid = Ext.getCmp('virtunewsletter-grid-newsletters');
        if (typeof(grid) !== 'undefined') {
            grid.refresh();
            var data = response.result.object;
            if (data.action = "mgr/newsletters/create") {
                var create = Ext.getCmp('virtunewsletter-panel-newsletter-content-tab-new');
                if (typeof(create) !== 'undefined') {
                    create.destroy();
                    grid.newsletterPanel(data);
                }
            }
        }
    });

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
                        this.closeTab();
                        var grid = Ext.getCmp('virtunewsletter-grid-newsletters');
                        if (grid) {
                            grid.refresh();
                        }
                        var container = Ext.getCmp('modx-content');
                        return container.doLayout();
                    },
                    scope: this
                },
                'failure': {
                    fn: function () {},
                    scope: this
                }
            }
        });
    },
    closeTab: function () {
        var tabs = Ext.getCmp('virtunewsletter-newsletters-tabs');
        var tab = Ext.getCmp('virtunewsletter-panel-newsletter-content-tab-' + this.config.record.id);
        if (typeof (tabs) !== 'undefined' && typeof (tab) !== 'undefined') {
            tabs.remove(tab);
            tabs.setActiveTab(0);
        }
    },
    viewContent: function () {
        var time = new Date().getTime();
        window.open(VirtuNewsletter.config.webConnectorUrl + '?action=web/newsletters/read&newsid=' + this.record.id + '&preventCache=' + time, '_blank');
    }
});
Ext.reg('virtunewsletter-panel-newsletter-configuration', VirtuNewsletter.panel.NewsletterConfiguration);