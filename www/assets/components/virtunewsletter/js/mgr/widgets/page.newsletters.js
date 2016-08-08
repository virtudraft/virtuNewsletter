VirtuNewsletter.page.Newsletters = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        id: 'virtunewsletter-page-newsletters',
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
                id: 'virtunewsletter-newsletters-tabs',
                xtype: 'modx-tabs',
                bodyStyle: 'min-height: 500px;',
                autoHeight: true,
                enableTabScroll: true,
                defaults: {
                    autoScroll: true
                },
                padding: 10,
                items: [
                    {
                        title: _('virtunewsletter.list'),
                        xtype: 'virtunewsletter-grid-newsletters'
                    }
                ]
            }
        ]
    });
    VirtuNewsletter.page.Newsletters.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.page.Newsletters, MODx.Panel);
Ext.reg('virtunewsletter-page-newsletters', VirtuNewsletter.page.Newsletters);