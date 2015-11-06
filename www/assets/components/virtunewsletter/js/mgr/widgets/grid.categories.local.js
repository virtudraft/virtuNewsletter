VirtuNewsletter.grid.LocalCategories = function(config) {
    config = config || {};

    var check = Ext.getCmp('virtunewsletter-grid-localcategories');
    if (check) {
        check.destroy();
    }

    if (!config.data) {
        config.data = [];
        if (config.record && config.record.categories) {
            for (var i = 0, l = config.record.categories.length; i < l; i++) {
                if (config.record.categories[i].category_id !== 0) {
                    config.data.push([config.record.categories[i].category_id, config.record.categories[i].category]);
                }
            }
        }
    }

    if (!config.dataObject) {
        config.dataObject = {};
        if (config.record && config.record.categories) {
            for (var i = 0, l = config.record.categories.length; i < l; i++) {
                if (config.record.categories[i].category_id !== 0) {
                    config.dataObject[config.record.categories[i].category_id] = config.record.categories[i].category;
                }
            }
        }
    }

    Ext.applyIf(config, {
        id: 'virtunewsletter-grid-localcategories',
        autoHeight: true,
        data: config.data,
        dataObject: config.dataObject,
        fields: ['category_id', 'category'],
        preventRender: true,
        margins: 15,
        autoExpandColumn: 'category',
        columns: [
            {
                header: _('id'),
                dataIndex: 'category_id',
                width: 40,
                sortable: true,
                hidden: true
            }, {
                header: _('virtunewsletter.name'),
                dataIndex: 'category',
                sortable: true
            }, {
                header: _('actions'),
                xtype: 'actioncolumn',
                dataIndex: 'category_id',
                width: 80,
                fixed: true,
                sortable: false,
                items: [
                    {
                        iconCls: 'virtunewsletter-icon-delete virtunewsletter-icon-actioncolumn-img',
                        tooltip: _('virtunewsletter.remove'),
                        altText: _('virtunewsletter.remove'),
                        handler: function(grid, row, col) {
                            var rec = grid.store.getAt(row);
                            this.removeCategory(rec.data.category_id);
                        },
                        scope: this
                    }
                ]
            }
        ],
        tbar: [
            {
                xtype: 'virtunewsletter-combo-categories',
                anchor: '100%',
                lazyRender: true
            }, {
                text: _('virtunewsletter.add'),
                handler: this.addCategory
            }, {
                text: _('virtunewsletter.clear'),
                handler: function () {
                    var topToolbar = this.getTopToolbar();
                    topToolbar.items.items[0].setValue('');
                }
            }
        ]
    });

    VirtuNewsletter.grid.LocalCategories.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.grid.LocalCategories, MODx.grid.LocalGrid, {
    getMenu: function(node, e) {
        var menu = [
            {
                text: _('virtunewsletter.remove'),
                handler: function(btn, e) {
                    this.removeCategory(this.menu.record.category_id);
                }
            }
        ];

        return menu;
    },
    addCategory: function () {
        var topToolbar = this.getTopToolbar();
        var combo = topToolbar.items.items[0];
        var comboValue = combo.getValue();
        var text = combo.lastSelectionText;
        if (comboValue && typeof(this.dataObject[comboValue]) === 'undefined') {
            this.data.push([comboValue, text]);
            this.dataObject[comboValue] = text;
            this.getStore().loadData(this.data);
        }
    },
    removeCategory: function(id) {
        var newData = [];
        for (var i = 0, l = this.data.length; i < l; i++) {
            if (this.data[i][0] === id) {
                continue;
            }
            newData.push(this.data[i]);
        }
        this.data = newData;
        delete this.dataObject[id];
        this.getStore().loadData(newData);
        return newData;
    }
});
Ext.reg('virtunewsletter-grid-localcategories', VirtuNewsletter.grid.LocalCategories);