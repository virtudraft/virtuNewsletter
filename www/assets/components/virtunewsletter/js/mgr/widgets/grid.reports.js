VirtuNewsletter.grid.Reports = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        baseParams: {
            action: 'mgr/reports/getList',
            newsletter_id: config.newsletter_id
        },
        fields: ['id', 'newsletter_id', 'subscriber_id', 'email', 'name', 'status', 'status_changed_on'],
        paging: true,
        remoteSort: true,
        anchor: '97%',
        autoExpandColumn: 'email',
        columns: [
            {
                header: _('id'),
                dataIndex: 'id',
                sortable: true,
                width: 60
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
                header: _('virtunewsletter.status'),
                dataIndex: 'status',
                sortable: true
            }, {
                header: _('virtunewsletter.date'),
                dataIndex: 'status_changed_on',
                sortable: true
            }
        ]
    });
    VirtuNewsletter.grid.Reports.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.grid.Reports, MODx.grid.Grid);
Ext.reg('virtunewsletter-grid-reports', VirtuNewsletter.grid.Reports);