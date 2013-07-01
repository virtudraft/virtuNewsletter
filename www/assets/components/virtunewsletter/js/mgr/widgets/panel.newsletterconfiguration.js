VirtuNewsletter.panel.NewsletterConfiguration = function(config) {
    config = config || {};
    console.log('config', config);
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
                xtype: 'textfield',
                fieldLabel: _('virtunewsletter.subject'),
                name: 'subject',
                anchor: '100%',
                value: config.node &&
                        config.node.attributes &&
                        config.node.attributes.subject ? config.node.attributes.subject : ''
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
                                xtype: 'virtunewsletter-combo-categories',
                                fieldLabel: _('virtunewsletter.category'),
                                name: 'category_id',
                                value: config.node &&
                                        config.node.attributes &&
                                        config.node.attributes.category_id ? config.node.attributes.category_id : 0
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
                            }, {
                                xtype: 'xcheckbox',
                                boxLabel: _('virtunewsletter.is_recurring'),
                                name: 'is_recurring',
                                value: config.node &&
                                        config.node.attributes &&
                                        config.node.attributes.is_recurring ? config.node.attributes.is_recurring : 0
                            }
                        ]
                    }
                ]
            }
        ],
        bbar: [
            {
                text: _('virtunewsletter.save'),
                handler: this.updateNewsletter,
                scope: this
            }, {
                text: _('virtunewsletter.remove'),
                handler: this.removeNewsletter,
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

        MODx.Ajax.request({
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/newsletters/update',
                id: this.config.node.attributes.newsid,
                subject: values.subject
            },
            listeners: {
                'success': {
                    fn: function() {
                        var newslettersTree = Ext.getCmp('virtunewsletter-tree-newsletters');
                        return newslettersTree.refreshNode(this.config.node.id);
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
    cleanCenter: function(){
        var contentPanel = Ext.getCmp('virtunewsletter-panel-newsletter-center');
        contentPanel.removeAll();
        var container = Ext.getCmp('modx-content');
        return container.doLayout();
    }
});
Ext.reg('virtunewsletter-panel-newsletter-configuration', VirtuNewsletter.panel.NewsletterConfiguration);