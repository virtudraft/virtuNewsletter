VirtuNewsletter.page.Categories = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        id: 'virtunewsletter-page-categories',
        border: false,
        autoHeight: true,
        defaults: {
            border: false
        },
        items: [
            {
                html: '<h2>' + _('virtunewsletter.categories') + '</h2>',
                border: false,
                bodyCssClass: 'panel-desc'
            }, {
                id: 'virtunewsletter-categories-tabs',
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
                        xtype: 'virtunewsletter-grid-categories'
                    }
                ]
            }
        ]
    });
    VirtuNewsletter.page.Categories.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.page.Categories, MODx.Panel);
Ext.reg('virtunewsletter-page-categories', VirtuNewsletter.page.Categories);