VirtuNewsletter.grid.Categories = function(config) {
    config = config || {};

    var check = Ext.getCmp('virtunewsletter-grid-categories');
    if (check) {
        check.destroy();
    }

    if (!this.data) {
        var data = [];
        if (config.record && config.record.categories) {
            for (var i = 0, l = config.record.categories.length; i < l; i++) {
                if (config.record.categories[i].category_id !== 0) {
                    data.push([config.record.categories[i].category_id, config.record.categories[i].category]);
                }
            }
        }
    }

    Ext.applyIf(config, {
        id: 'virtunewsletter-grid-categories',
        autoHeight: true,
        data: data,
        fields: ['category_id', 'category'],
        preventRender: true,
        anchor: '97%',
        autoExpandColumn: 'category',
        columns: [
            {
                header: _('id'),
                dataIndex: 'category_id',
                width: 20,
                sortable: true
            }, {
                header: _('virtunewsletter.name'),
                dataIndex: 'category',
                sortable: true
            }
        ]
    });

    VirtuNewsletter.grid.Categories.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.grid.Categories, MODx.grid.LocalGrid, {
    getMenu: function(node, e) {
        var menu = [
            {
                text: _('virtunewsletter.remove'),
                handler: this.removeCategories
            }
        ];

        return menu;
    },
    removeCategories: function(btn, e) {
        var newData = [];
        for (var i = 0, l = this.data.length; i < l; i++) {
            if (this.data[i][0] === this.menu.record.category_id) {
                continue;
            }
            newData.push(this.data[i]);
        }

        this.data = newData;
        this.getStore().loadData(newData);
        this.getView().refresh();
        return newData;
    }
});
Ext.reg('virtunewsletter-grid-categories', VirtuNewsletter.grid.Categories);