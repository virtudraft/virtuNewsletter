VirtuNewsletter.page.Templates = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        id: 'virtunewsletter-page-templates',
        border: false,
        defaults: {
            border: false
        },
        items: [
            {
                html: '<h2>' + _('virtunewsletter.templates') + '</h2>'
                    + '<p>' + _('virtunewsletter.templates_desc') + '</p>',
                border: false,
                bodyCssClass: 'panel-desc'
            }, {
                xtype: 'modx-tabs',
                items: [
                    {
                        title: _('virtunewsletter.subscribing'),
                        xtype: 'form', // not modx-formpanel
                        padding: 10,
                        items: [
                            {
                                fieldLabel: _('language'),
                                name: 'virtunewsletter-template-subscribing-language',
                                id: 'virtunewsletter-template-subscribing-language',
                                xtype: 'modx-combo-language',
                                listeners: {
                                    select: {
                                        fn: function(combo, record, index) {
                                            this.getTemplate('subscribing', record.data.name)
                                        },
                                        scope: this
                                    }
                                }
                            }, {
                                fieldLabel: _('virtunewsletter.subject'),
                                name: 'virtunewsletter-template-subscribing-subject',
                                id: 'virtunewsletter-template-subscribing-subject',
                                xtype: 'textfield',
                                anchor: '100%'
                            }, {
                                fieldLabel: _('content'),
                                name: 'virtunewsletter-template-subscribing-content',
                                id: 'virtunewsletter-template-subscribing-content',
                                xtype: 'textarea',
                                anchor: '100%',
                                height: 400
                            }, {
                                xtype: 'toolbar',
                                items: [
                                    '->', {
                                        text: _('save'),
                                        handler: function(btn, e) {
                                            this.saveTemplate('subscribing');
                                        },
                                        scope: this
                                    }
                                ]
                            }
                        ]
                    }, {
                        title: _('virtunewsletter.subscribed'),
                        xtype: 'form', // not modx-formpanel
                        padding: 10,
                        items: [
                            {
                                fieldLabel: _('language'),
                                name: 'virtunewsletter-template-subscribed-language',
                                id: 'virtunewsletter-template-subscribed-language',
                                xtype: 'modx-combo-language',
                                listeners: {
                                    select: {
                                        fn: function(combo, record, index) {
                                            this.getTemplate('subscribed', record.data.name)
                                        },
                                        scope: this
                                    }
                                }
                            }, {
                                fieldLabel: _('virtunewsletter.subject'),
                                name: 'virtunewsletter-template-subscribed-subject',
                                id: 'virtunewsletter-template-subscribed-subject',
                                xtype: 'textfield',
                                anchor: '100%'
                            }, {
                                fieldLabel: _('content'),
                                name: 'virtunewsletter-template-subscribed-content',
                                id: 'virtunewsletter-template-subscribed-content',
                                xtype: 'textarea',
                                anchor: '100%',
                                height: 400
                            }, {
                                xtype: 'toolbar',
                                items: [
                                    '->', {
                                        text: _('save'),
                                        handler: function(btn, e) {
                                            this.saveTemplate('subscribed');
                                        },
                                        scope: this
                                    }
                                ]
                            }
                        ]
                    }, {
                        title: _('virtunewsletter.unsubscribing'),
                        xtype: 'form', // not modx-formpanel
                        padding: 10,
                        items: [
                            {
                                fieldLabel: _('language'),
                                name: 'virtunewsletter-template-unsubscribing-language',
                                id: 'virtunewsletter-template-unsubscribing-language',
                                xtype: 'modx-combo-language',
                                listeners: {
                                    select: {
                                        fn: function(combo, record, index) {
                                            this.getTemplate('unsubscribing', record.data.name)
                                        },
                                        scope: this
                                    }
                                }
                            }, {
                                fieldLabel: _('virtunewsletter.subject'),
                                name: 'virtunewsletter-template-unsubscribing-subject',
                                id: 'virtunewsletter-template-unsubscribing-subject',
                                xtype: 'textfield',
                                anchor: '100%'
                            }, {
                                fieldLabel: _('content'),
                                name: 'virtunewsletter-template-unsubscribing-content',
                                id: 'virtunewsletter-template-unsubscribing-content',
                                xtype: 'textarea',
                                anchor: '100%',
                                height: 400
                            }, {
                                xtype: 'toolbar',
                                items: [
                                    '->', {
                                        text: _('save'),
                                        handler: function(btn, e) {
                                            this.saveTemplate('unsubscribing');
                                        },
                                        scope: this
                                    }
                                ]
                            }
                        ]
                    }, {
                        title: _('virtunewsletter.unsubscribed'),
                        xtype: 'form', // not modx-formpanel
                        padding: 10,
                        items: [
                            {
                                fieldLabel: _('language'),
                                name: 'virtunewsletter-template-unsubscribed-language',
                                id: 'virtunewsletter-template-unsubscribed-language',
                                xtype: 'modx-combo-language',
                                listeners: {
                                    select: {
                                        fn: function(combo, record, index) {
                                            this.getTemplate('unsubscribed', record.data.name)
                                        },
                                        scope: this
                                    }
                                }
                            }, {
                                fieldLabel: _('virtunewsletter.subject'),
                                name: 'virtunewsletter-template-unsubscribed-subject',
                                id: 'virtunewsletter-template-unsubscribed-subject',
                                xtype: 'textfield',
                                anchor: '100%'
                            }, {
                                fieldLabel: _('content'),
                                name: 'virtunewsletter-template-unsubscribed-content',
                                id: 'virtunewsletter-template-unsubscribed-content',
                                xtype: 'textarea',
                                anchor: '100%',
                                height: 400
                            }, {
                                xtype: 'toolbar',
                                items: [
                                    '->', {
                                        text: _('save'),
                                        handler: function(btn, e) {
                                            this.saveTemplate('unsubscribed');
                                        },
                                        scope: this
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }
        ]
    });
    VirtuNewsletter.page.Templates.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.page.Templates, MODx.Panel, {
    getTemplate: function(name, lang) {
        this.loadMask();
        return MODx.Ajax.request({
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/templates/get',
                name: name,
                culture_key: lang
            },
            listeners: {
                'success': {
                    fn: function(response) {
                        this.hideMask();
                        if (response.success === true) {
                            Ext.getCmp('virtunewsletter-template-' + name + '-subject').setValue(response.object.subject);
                            Ext.getCmp('virtunewsletter-template-' + name + '-content').setValue(response.object.content);
                        } else {
                            Ext.getCmp('virtunewsletter-template-' + name + '-subject').reset();
                            Ext.getCmp('virtunewsletter-template-' + name + '-content').reset();
                        }
                    },
                    scope: this
                },
                'failure': {
                    fn: function() {
                        this.hideMask();
                        Ext.getCmp('virtunewsletter-template-' + name + '-subject').reset();
                        Ext.getCmp('virtunewsletter-template-' + name + '-content').reset();
                    },
                    scope: this
                }
            }
        });
    },
    saveTemplate: function(name) {
        var lang = Ext.getCmp('virtunewsletter-template-' + name + '-language');
        if (typeof (lang) === 'undefined') {
            return false;
        }
        var subject = Ext.getCmp('virtunewsletter-template-' + name + '-subject');
        if (typeof (subject) === 'undefined') {
            return false;
        }
        var textarea = Ext.getCmp('virtunewsletter-template-' + name + '-content');
        if (typeof (textarea) === 'undefined') {
            return false;
        }
        this.loadMask();
        return MODx.Ajax.request({
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/templates/update',
                name: name,
                culture_key: lang.getValue(),
                subject: subject.getValue(),
                content: textarea.getValue()
            },
            listeners: {
                'success': {
                    fn: function() {
                        this.hideMask();
                    },
                    scope: this
                },
                'failure': {
                    fn: function() {
                        this.hideMask();
                    },
                    scope: this
                }
            }
        });
    },
    loadMask: function() {
        if (!this.loadConverterMask) {
            var domHandler = Ext.getCmp('virtunewsletter-page-templates').body.dom;
            this.loadConverterMask = new Ext.LoadMask(domHandler, {
                msg: _('virtunewsletter.please_wait')
            });
        }
        this.loadConverterMask.show();
    },
    hideMask: function() {
        if (this.loadConverterMask) {
            this.loadConverterMask.hide();
        }
    }
});
Ext.reg('virtunewsletter-page-templates', VirtuNewsletter.page.Templates);