VirtuNewsletter.grid.Recurrences = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        baseParams: {
            action: 'mgr/newsletters/getList',
            parentId: config.record.id || 0
        },
        fields: ['id', 'parent_id', 'resource_id', 'subject', 'content', 'created_on', 'created_by', 'scheduled_for',
            'is_recurring', 'recurrence_range', 'recurrence_number', 'is_active'],
        paging: true,
        remoteSort: true,
        autoExpandColumn: 'subject',
        dateFormat: 'U',
        displayFormat: 'm/d/Y',
        columns: [
            {
                header: _('id'),
                dataIndex: 'id',
                sortable: true,
                width: 40
            }, {
                header: _('virtunewsletter.parent_id'),
                dataIndex: 'parent_id',
                sortable: true,
                width: 40
            }, {
                header: _('virtunewsletter.resource_id'),
                dataIndex: 'resource_id',
                sortable: true,
                width: 40
            }, {
                header: _('virtunewsletter.subject'),
                dataIndex: 'subject',
                sortable: true
            }, {
                header: _('virtunewsletter.created_on'),
                dataIndex: 'created_on',
                sortable: true,
                renderer: function(value) {
                    var date = Date.parseDate(value, config.dateFormat);
                    return date.format(config.displayFormat);
                }
            }, {
                header: _('virtunewsletter.scheduled_for'),
                dataIndex: 'scheduled_for',
                sortable: true,
                renderer: function(value) {
                    if (value) {
                        var date = Date.parseDate(value, config.dateFormat);
                        return date.format(config.displayFormat);
                    }
                }
            }, {
                header: _('virtunewsletter.action'),
                xtype: 'actioncolumn',
                width: 50,
                items: [
                    {
                        iconCls : 'virtunewsletter-icon-magnifier',
                        tooltip: _('virtunewsletter.view'),
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = grid.getStore().getAt(rowIndex);
                            return this.viewContent(rec.get('id'));
                        },
                        scope: this
                    }, {
                        iconCls : 'virtunewsletter-icon-cancel',
                        tooltip: _('virtunewsletter.remove'),
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = grid.getStore().getAt(rowIndex);
                            return this.removeNewsletter(rec.get('id'));
                        },
                        scope: this
                    }
                ]
            }
        ]
    });

    VirtuNewsletter.grid.Recurrences.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.grid.Recurrences, MODx.grid.Grid, {
    viewContent: function(newsid) {
        var time = new Date().getTime();
        window.open(VirtuNewsletter.config.webConnectorUrl + '?action=web/newsletters/read&newsid=' + newsid + '&preventCache=' + time, '_blank');
    },
    removeNewsletter: function(newsid) {
        MODx.msg.confirm({
            title: _('virtunewsletter.remove'),
            text: _('virtunewsletter.remove_confirm'),
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/newsletters/remove',
                id: newsid
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
Ext.reg('virtunewsletter-grid-recurrences', VirtuNewsletter.grid.Recurrences);