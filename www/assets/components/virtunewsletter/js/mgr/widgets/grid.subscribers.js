VirtuNewsletter.grid.Subscribers = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        id: 'virtunewsletter-grid-reports',
        url: VirtuNewsletter.config.connectorUrl,
        baseParams: {
            action: 'mgr/subscribers/getList',
            newsletter_id: config.newsletter_id
        },
        fields: ['id', 'user_id', 'email', 'name', 'is_active'],
        paging: true,
        remoteSort: true,
        anchor: '97%',
        autoExpandColumn: 'email',
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
                header: _('virtunewsletter.active'),
                dataIndex: 'is_active',
                sortable: true,
                width: 30
            }
        ]
    });
    VirtuNewsletter.grid.Subscribers.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.grid.Subscribers, MODx.grid.Grid);
Ext.reg('virtunewsletter-grid-subscribers', VirtuNewsletter.grid.Subscribers);