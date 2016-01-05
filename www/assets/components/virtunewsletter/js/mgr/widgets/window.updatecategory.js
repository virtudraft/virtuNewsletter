VirtuNewsletter.window.UpdateCategory = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        autoHeight: true,
        preventRender: true,
        fields: [
            {
                // will be used for the grid below
                xtype: 'hidden',
                name: 'categories'
            }, {
                xtype: 'virtunewsletter-grid-localcategories',
                record: config.record ? config.record : '',
                anchor: '100%'
            }
        ],
        listeners: {
            beforeSubmit: {
                fn: function(values) {
                    var grid = Ext.getCmp('virtunewsletter-grid-localcategories');
                    if (typeof(grid) !== 'undefined') {
                        var store = grid.getStore();
                        var categories = [];
                        for (var i = 0, l = store.data.items.length; i < l; i++) {
                            if (store.data.items[i].data.category_id !== 0) {
                                categories.push(store.data.items[i].data.category_id);
                            }
                        }
                        values['categories'] = categories;
                        delete(values['category_id']);
                    }

                    this.fp.getForm().setValues(values);

                    return true;
                },
                scope: this
            }
        }
    });

    VirtuNewsletter.window.UpdateCategory.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.window.UpdateCategory, MODx.Window);
Ext.reg('virtunewsletter-window-updatecategory', VirtuNewsletter.window.UpdateCategory);