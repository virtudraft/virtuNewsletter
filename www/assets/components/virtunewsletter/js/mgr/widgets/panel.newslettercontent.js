VirtuNewsletter.panel.NewsletterContent = function(config) {
    config = config || {};

    Ext.apply(config, {
        border: false,
        baseCls: 'modx-formpanel',
        items: [
            {
                xtype: 'modx-tabs',
                defaults: {
                    border: false,
                    autoHeight: true
                },
                bodyStyle: 'padding:20px; overflow-y: scroll;',
                border: true,
                items: [
                    {
                        title: _('virtunewsletter.configurations'),
                        preventRender: true,
                        xtype: 'virtunewsletter-panel-newsletter-configuration',
                        node: config.node
                    }, {
                        title: _('virtunewsletter.content'),
                        preventRender: true,
                        html: config.node &&
                                config.node.attributes &&
                                config.node.attributes.content ?
                                config.node.attributes.content : '',
                        listeners: {
                            'afterrender': {
                                fn: function(panel) {
                                    var topTab = Ext.getCmp('virtunewsletter-panel-home');
//                                    console.log('topTab.getHeight()', topTab.getHeight());
                                    topTab.doLayout();
                                }
                            }
                        }
                    }, {
                        title: _('virtunewsletter.reports'),
                        preventRender: true,
                        xtype: 'virtunewsletter-grid-reports',
                        newsletter_id: config.node &&
                                config.node.attributes &&
                                config.node.attributes.newsid ?
                                config.node.attributes.newsid : ''
                    }
                ]
            }
        ]
    });
    VirtuNewsletter.panel.NewsletterContent.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.NewsletterContent, MODx.Panel);
Ext.reg('virtunewsletter-panel-newsletter-content', VirtuNewsletter.panel.NewsletterContent);