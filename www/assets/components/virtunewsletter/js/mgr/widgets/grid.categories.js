VirtuNewsletter.grid.Categories = function(config) {
    config = config || {};

    var ids = [];
    if (config.record && config.record.categories) {
        Ext.each(config.record.categories, function(item) {
            ids.push(item.category_id);
        });
    }
    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        baseParams: {
            action: 'mgr/categories/getList',
            ids: JSON.stringify(ids)
        },
        autoHeight: true,
        fields: ['id', 'name', 'description', 'categories'],
        paging: true,
        remoteSort: true,
        preventRender: true,
        margins: 15,
        autoExpandColumn: 'description',
        columns: [
            {
                header: _('id'),
                dataIndex: 'id',
                width: 80,
                fixed: true,
                sortable: true,
                hidden: true
            }, {
                header: _('virtunewsletter.name'),
                dataIndex: 'name',
                width: 100,
                sortable: true
            }, {
                header: _('virtunewsletter.description'),
                dataIndex: 'description',
                width: 200,
                sortable: false
            }, {
                header: _('actions'),
                xtype: 'actioncolumn',
                dataIndex: 'id',
                width: 80,
                fixed: true,
                sortable: false,
                items: [
                    {
                        iconCls: 'virtunewsletter-icon-delete virtunewsletter-icon-actioncolumn-img',
                        tooltip: _('virtunewsletter.remove'),
                        altText: _('virtunewsletter.remove'),
                        handler: function(grid, row, col) {
                            var rec = this.store.getAt(row);
                            this.removeCategory(rec.data.id);
                        },
                        scope: this
                    }, {
                        iconCls: 'virtunewsletter-icon-magnifier virtunewsletter-icon-actioncolumn-img',
                        tooltip: _('virtunewsletter.detail'),
                        altText: _('virtunewsletter.detail'),
                        handler: function(grid, row, col) {
                            var rec = this.store.getAt(row);
                            this.loadCategory(rec.data.id);
                        },
                        scope: this
                    }
                ]
            }
        ],
        tbar: [
            {
                text: _('virtunewsletter.add_new_category'),
                handler:  function() {
                    this.categoryPanel();
                },
                scope: this
            }
        ]
    });

    VirtuNewsletter.grid.Categories.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.grid.Categories, MODx.grid.Grid, {
    getMenu: function(node, e) {
        var menu = [
            {
                text: _('virtunewsletter.remove'),
                handler: function(btn, e) {
                        this.removeCategory(this.menu.record.id);
                }
            }
        ];

        return menu;
    },
    removeCategory: function(id) {

        MODx.msg.confirm({
            title: _('virtunewsletter.remove'),
            text: _('virtunewsletter.remove_confirm'),
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/categories/remove',
                id: id
            },
            listeners: {
                'success': {
                    fn: this.refresh,
                    scope: this
                }
            }
        });
    },
    loadCategory: function(id) {
        if (!this.pageMask) {
            this.pageMask = new Ext.LoadMask(Ext.getBody(), {
                msg: _('virtunewsletter.please_wait')
            });
        }
        this.pageMask.show();

        MODx.Ajax.request({
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/categories/get',
                id: id
            },
            listeners: {
                'success': {
                    fn: function(res) {
                        if (res.success === true) {
                            this.categoryPanel(res.object);
                        }
                        return this.pageMask.hide();
                    },
                    scope: this
                },
                'failure': {
                    fn: function() {
                        return this.pageMask.hide();
                    },
                    scope: this
                }
            }
        });
    },
    categoryPanel: function(record) {
        record = record|| {};
        var tabs = Ext.getCmp('virtunewsletter-categories-tabs');
        if (typeof(tabs) === 'undefined') {
            return false;
        }
        var action = 'create';
        var id = 0;
        if (typeof(record['id']) !== 'undefined') {
            id = record['id'];
            action = 'update';
        }
        var newTab = MODx.load({
            title: record.id ? _('virtunewsletter.category_update') : _('virtunewsletter.category_create'),
            closable: true,
            xtype: 'virtunewsletter-panel-category',
            baseParams: {
                action: 'mgr/categories/' + action,
                id: id
            },
            record: record
        });
        newTab.getForm().setValues(record);
        // SuperBoxSelect
        var sb;
        sb = newTab.getForm().findField('usergroups[]');
        sb.setValue(record.usergroups);

        newTab.on('success', function(o) {
            if (o.result.success === true) {
                this.refresh();
                if (typeof(record.id) === 'undefined') {
                    newTab.destroy();
                    this.loadCategory(o.result.object.id);
                }
            }
        }, this);
        tabs.add(newTab);
        tabs.setActiveTab(newTab);
    }
});
Ext.reg('virtunewsletter-grid-categories', VirtuNewsletter.grid.Categories);