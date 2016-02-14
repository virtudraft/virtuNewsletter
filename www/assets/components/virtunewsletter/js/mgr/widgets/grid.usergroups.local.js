VirtuNewsletter.grid.LocalUsergroups = function (config) {
    config = config || {};

    var data = config.data || [], dataObject = config.dataObject || {};

    Ext.applyIf(config, {
        autoHeight: true,
        data: data,
        dataObject: dataObject,
        fields: ['usergroup_id', 'usergroup'],
        preventRender: true,
        anchor: '97%',
        autoExpandColumn: 'usergroup',
        grouping: false,
        columns: [
            {
                header: _('id'),
                dataIndex: 'usergroup_id',
                width: 20,
                sortable: true
            }, {
                header: _('virtunewsletter.name'),
                dataIndex: 'usergroup',
                sortable: true
            }, {
                header: _('actions'),
                xtype: 'actioncolumn',
                dataIndex: 'usergroup_id',
                width: 80,
                fixed: true,
                sortable: false,
                items: [
                    {
                        iconCls: 'virtunewsletter-icon-delete virtunewsletter-icon-actioncolumn-img',
                        tooltip: _('virtunewsletter.remove'),
                        altText: _('virtunewsletter.remove'),
                        handler: function (grid, row, col) {
                            var rec = grid.store.getAt(row);
                            this.removeUsergroup(rec.data.usergroup_id);
                        },
                        scope: this
                    }
                ]
            }
        ],
        tbar: [
            {
                xtype: 'virtunewsletter-combo-usergroups',
                anchor: '100%'
            }, {
                text: _('virtunewsletter.add'),
                handler: this.addUsergroup,
                scope: this
            }, {
                text: _('virtunewsletter.clear'),
                handler: function () {
                    var topToolbar = this.getTopToolbar();
                    topToolbar.items.items[0].setValue('');
                }
            }
        ]
    });

    VirtuNewsletter.grid.LocalUsergroups.superclass.constructor.call(this, config);

};
Ext.extend(VirtuNewsletter.grid.LocalUsergroups, MODx.grid.LocalGrid, {
    getMenu: function (node, e) {
        var menu = [
            {
                text: _('virtunewsletter.remove'),
                handler: function (btn, e) {
                    this.removeUsergroup(this.menu.record.usergroup_id);
                }
            }
        ];

        return menu;
    },
    addUsergroup: function() {
        var topToolbar = this.getTopToolbar();
        var combo = topToolbar.items.items[0];
        var comboValue = combo.getValue();
        var text = combo.lastSelectionText;
        if (comboValue && typeof(this.dataObject[comboValue]) === 'undefined') {
            this.data.push([comboValue, text]);
            this.getStore().loadData(this.data);
            this.dataObject[comboValue] = text;
        }
    },
    removeUsergroup: function(id) {
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
Ext.reg('virtunewsletter-grid-localusergroups', VirtuNewsletter.grid.LocalUsergroups);