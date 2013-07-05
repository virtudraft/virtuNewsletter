VirtuNewsletter.grid.Reports = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        baseParams: {
            action: 'mgr/reports/getList',
            newsletter_id: config.newsletter_id
        },
        fields: ['id', 'newsletter_id', 'subscriber_id', 'email', 'name', 'current_occurrence_time', 'status', 'status_logged_on', 'next_occurrence_time'],
        paging: true,
        remoteSort: true,
        anchor: '97%',
        autoExpandColumn: 'email',
        dateFormat: 'U',
        displayFormat: 'm/d/Y',
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
                header: _('virtunewsletter.current_occurrence_time'),
                dataIndex: 'current_occurrence_time',
                sortable: true,
                renderer: function(value) {
                    if (value) {
                        var date = Date.parseDate(value, config.dateFormat);
                        return date.format(config.displayFormat);
                    }
                }
            }, {
                header: _('virtunewsletter.status'),
                dataIndex: 'status',
                sortable: true
            }, {
                header: _('virtunewsletter.date'),
                dataIndex: 'status_logged_on',
                sortable: true,
                renderer: function(value) {
                    var date = Date.parseDate(value, config.dateFormat);
                    return date.format(config.displayFormat);
                }
            }, {
                header: _('virtunewsletter.next_occurrence_time'),
                dataIndex: 'next_occurrence_time',
                sortable: true,
                renderer: function(value) {
                    if (value) {
                        var date = Date.parseDate(value, config.dateFormat);
                        return date.format(config.displayFormat);
                    }
                }
            }
        ]
    });
    VirtuNewsletter.grid.Reports.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.grid.Reports, MODx.grid.Grid);
Ext.reg('virtunewsletter-grid-reports', VirtuNewsletter.grid.Reports);