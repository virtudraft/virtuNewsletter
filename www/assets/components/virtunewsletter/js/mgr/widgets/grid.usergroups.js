VirtuNewsletter.grid.Usergroups = function(config) {
    config = config || {};

    var check = Ext.getCmp('virtunewsletter-grid-usergroups');
    if (check) {
        check.destroy();
    }
    var data = [];
    if (config.record && config.record.usergroups) {
        for (var i = 0, l = config.record.usergroups.length; i < l; i++) {
            data.push([config.record.usergroups[i].usergroup_id, config.record.usergroups[i].usergroup]);
        }
    }

    Ext.applyIf(config, {
        id: 'virtunewsletter-grid-usergroups',
        autoHeight: true,
        data: data,
        fields: ['usergroup_id', 'usergroup'],
        preventRender: true,
        anchor: '97%',
        autoExpandColumn: 'usergroup',
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
            }
        ]
    });

    VirtuNewsletter.grid.Usergroups.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.grid.Usergroups, MODx.grid.LocalGrid, {
    getMenu: function(node, e) {
        var menu = [
            {
                text: _('virtunewsletter.remove'),
                handler: this.removeUsergroup
            }
        ];

        return menu;
    },
    removeUsergroup: function(btn, e) {
        var newData = [];
        for (var i = 0, l = this.data.length; i < l; i++) {
            if (this.data[i][0] === this.menu.record.usergroup_id) {
                continue;
            }
            newData.push(this.data[i]);
        }
        this.getStore().loadData(newData);
        this.getView().refresh();
    }
});
Ext.reg('virtunewsletter-grid-usergroups', VirtuNewsletter.grid.Usergroups);