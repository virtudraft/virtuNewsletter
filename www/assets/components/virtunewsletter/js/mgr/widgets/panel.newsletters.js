VirtuNewsletter.panel.Newsletters = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        id: 'virtunewsletter-panel-newsletters',
        border: false,
        autoHeight: true,
        defaults: {
            border: false
        },
        items: [
            {
                html: '<h2>' + _('virtunewsletter.newsletters') + '</h2>',
                border: false,
                bodyCssClass: 'panel-desc'
            }, {
                xtype: 'modx-tabs',
                bodyStyle: 'min-height: 500px;',
                autoHeight: true,
                defaults: {
                    xtype: 'modx-tabs',
                    enableTabScroll: true,
                    defaults: {
                        autoScroll: true
                    },
                    padding: 10,
                    autoHeight: true
                },
                items: [
                    {
                        title: _('virtunewsletter.newsletters'),
                        id: 'virtunewsletter-newsletters-tabs',
                        items: [
                            {
                                title: _('virtunewsletter.list'),
                                xtype: 'virtunewsletter-grid-newsletters'
                            }
                        ]
                    }, {
                        title: 'Categories',
                        id: 'virtunewsletter-categories-tabs',
                        items: [
                            {
                                title: _('virtunewsletter.list'),
                                xtype: 'virtunewsletter-grid-categories'
                            }
                        ]
                    }
                ]
            }
        ]
    });
    VirtuNewsletter.panel.Newsletters.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.Newsletters, MODx.Panel);
Ext.reg('virtunewsletter-panel-newsletters', VirtuNewsletter.panel.Newsletters);