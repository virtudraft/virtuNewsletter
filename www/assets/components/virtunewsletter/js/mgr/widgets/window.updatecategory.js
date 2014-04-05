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
                xtype: 'virtunewsletter-grid-categories',
                record: config.record ? config.record : '',
                anchor: '100%',
                // this tbar is for this panel only!
                tbar: [
                    {
                        xtype: 'virtunewsletter-combo-categories',
                        lazyRender: true
                    }, {
                        text: _('virtunewsletter.add'),
                        handler: this.addCategory
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
                        if (store.data.items[i].data.category_id !== 0) {
                            categories.push(store.data.items[i].data.category_id);
                        }
                    }
                    values['categories'] = categories;
                    delete(values['category_id']);
                    this.fp.getForm().setValues(values);

                    return true;
                },
                scope: this
            }
        }
    });

    VirtuNewsletter.window.UpdateCategory.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.window.UpdateCategory, MODx.Window, {
    addCategory: function() {
        var topToolbar = this.getTopToolbar();
        var combo = topToolbar.items.items[0];
        var comboValue = combo.getValue();
        var text = combo.lastSelectionText;
        if (comboValue) {
            this.data.push([comboValue, text]);
            this.getStore().loadData(this.data);
            this.getView().refresh();
        }
    },
    getGridSettingValues: function(){
        var grid = Ext.getCmp('virtunewsletter-grid-categories');
        var store = grid.getStore();
        var fields = [];
        for (var i = 0, l = store.data.items.length; i < l; i++) {
            fields.push({
                category_id: store.data.items[i].data.category_id,
                category: store.data.items[i].data.category
            });
        }

        return fields;
    }
});
Ext.reg('virtunewsletter-window-updatecategory', VirtuNewsletter.window.UpdateCategory);