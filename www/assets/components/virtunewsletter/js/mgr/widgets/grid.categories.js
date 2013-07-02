VirtuNewsletter.grid.Categories = function(config) {
    config = config || {};

    var check = Ext.getCmp('virtunewsletter-grid-categories');
    if (check) {
        check.destroy();
    }
    var data = [];
    if (config.node && config.node.attributes && config.node.attributes.categories) {
        for (var i = 0, l = config.node.attributes.categories.length; i < l; i++) {
            data.push([config.node.attributes.categories[i].category_id, config.node.attributes.categories[i].category]);
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
        this.getStore().loadData(newData);
        this.getView().refresh();
    }
});
Ext.reg('virtunewsletter-grid-categories', VirtuNewsletter.grid.Categories);