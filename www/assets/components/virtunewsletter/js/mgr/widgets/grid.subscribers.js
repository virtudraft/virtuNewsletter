VirtuNewsletter.grid.Subscribers = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        id: 'virtunewsletter-grid-reports',
        url: VirtuNewsletter.config.connectorUrl,
        baseParams: {
            action: 'mgr/subscribers/getList',
            newsletter_id: config.newsletter_id
        },
        fields: ['id', 'user_id', 'email', 'name', 'usergroups', 'categories', 'is_active'],
        paging: true,
        remoteSort: true,
        anchor: '97%',
        autoExpandColumn: 'email',
        preventRender: true,
        columns: [
            {
                header: _('id'),
                dataIndex: 'id',
                sortable: true,
                width: 30
            }, {
                header: _('virtunewsletter.user_id'),
                dataIndex: 'user_id',
                sortable: true,
                width: 30
            }, {
                header: _('virtunewsletter.name'),
                dataIndex: 'name',
                sortable: true,
                width: 100
            }, {
                header: _('virtunewsletter.email'),
                dataIndex: 'email',
                sortable: true
            }, {
                header: _('virtunewsletter.categories'),
                dataIndex: 'categories',
                sortable: true
            }, {
                header: _('virtunewsletter.usergroups'),
                dataIndex: 'usergroups',
                sortable: true
            }, {
                xtype: 'checkcolumn',
                header: _('virtunewsletter.active'),
                dataIndex: 'is_active',
                sortable: true,
                width: 30,
                processEvent: function(name, e, grid, rowIndex, colIndex) {
                    if (name === 'mousedown') {
                        var record = grid.store.getAt(rowIndex);
                        record.set(this.dataIndex, !record.data[this.dataIndex]);
                        MODx.Ajax.request({
                            url: VirtuNewsletter.config.connectorUrl,
                            params: {
                                action: 'mgr/subscribers/updateFromGrid',
                                data: JSON.stringify(record.data)
                            },
                            listeners: {
                                'success': {
                                    fn: function() {
                                        Ext.getCmp('virtunewsletter-grid-reports').refresh();
                                    }
                                }
                            }
                        });
                        return false;
                    } else {
                        return Ext.grid.ActionColumn.superclass.processEvent.apply(this, arguments);
                    }
                }
            }
        ],
        tbar: [
            {
                text: _('virtunewsletter.sync_usergroups'),
                scope: this,
                handler: this.syncUsergroups
            }
        ]
    });
    VirtuNewsletter.grid.Subscribers.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.grid.Subscribers, MODx.grid.Grid, {
    syncUsergroups: function() {
        MODx.msg.confirm({
            title: _('virtunewsletter.sync_usergroups'),
            text: _('virtunewsletter.sync_usergroups_confirm'),
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/subscribers/sync'
            },
            listeners: {
                'success': {
                    fn: function() {
                        return this.refresh();
                    },
                    scope: this
                }
            }
        });
    },
    getMenu: function() {
        var menu = [
            {
                text: _('virtunewsletter.remove'),
                handler: this.removeSubscriber
            }
        ];

        return menu;
    },
    removeSubscriber: function(btn, e) {
        MODx.msg.confirm({
            title: _('virtunewsletter.subscriber_remove'),
            text: _('virtunewsletter.remove_confirm'),
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/subscribers/remove',
                id: this.menu.record.id
            },
            listeners: {
                'success': {
                    fn: this.refresh,
                    scope: this
                }
            }
        });
    }
});
Ext.reg('virtunewsletter-grid-subscribers', VirtuNewsletter.grid.Subscribers);