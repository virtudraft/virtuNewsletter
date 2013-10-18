VirtuNewsletter.grid.Reports = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        baseParams: {
            action: 'mgr/reports/getList',
            newsletter_id: config.record.newsid
        },
        fields: ['newsletter_id', 'subscriber_id', 'email', 'name', 'status', 'status_logged_on'],
        paging: true,
        remoteSort: true,
        anchor: '97%',
        autoExpandColumn: 'email',
        dateFormat: 'U',
        displayFormat: 'm/d/Y',
        columns: [
            {
                header: _('virtunewsletter.news_id'),
                dataIndex: 'newsletter_id',
                sortable: true,
                width: 40
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
                header: _('virtunewsletter.created_on'),
                dataIndex: 'status_logged_on',
                sortable: true,
                renderer: function(value) {
                    var date = Date.parseDate(value, config.dateFormat);
                    return date.format(config.displayFormat);
                }
            }
        ],
        tbar: [
            {
                text: _('virtunewsletter.queue_generate'),
                scope: this,
                handler: this.generateQueues
            }, {
                text: _('virtunewsletter.remove_all'),
                scope: this,
                handler: this.clearQueues
            }, {
                text: _('virtunewsletter.send'),
                scope: this,
                handler: this.send
            }
        ]
    });
    VirtuNewsletter.grid.Reports.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.grid.Reports, MODx.grid.Grid, {
    getMenu: function() {
        var menu = [
            {
                text: _('virtunewsletter.remove'),
                handler: this.removeQueue
            }
        ];

        return menu;
    },
    generateQueues: function() {
        MODx.msg.confirm({
            title: _('virtunewsletter.create_all'),
            text: _('virtunewsletter.create_all_confirm'),
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/reports/createall',
                newsletter_id: this.record.newsid
            },
            listeners: {
                'success': {
                    fn: this.refresh,
                    scope: this
                }
            }
        });
    },
    clearQueues: function() {
        MODx.msg.confirm({
            title: _('virtunewsletter.remove_all'),
            text: _('virtunewsletter.remove_all_confirm'),
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/reports/removeall',
                newsletter_id: this.record.newsid
            },
            listeners: {
                'success': {
                    fn: this.refresh,
                    scope: this
                }
            }
        });
    },
    removeQueue: function(btn, e) {
        MODx.msg.confirm({
            title: _('virtunewsletter.remove'),
            text: _('virtunewsletter.remove_confirm'),
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/reports/remove',
                newsletter_id: this.menu.record.newsletter_id,
                subscriber_id: this.menu.record.subscriber_id
            },
            listeners: {
                'success': {
                    fn: this.refresh,
                    scope: this
                }
            }
        });
    },
    send: function() {
        MODx.msg.confirm({
            title: _('virtunewsletter.send_now'),
            text: _('virtunewsletter.send_now_confirm'),
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/reports/sendall',
                newsletter_id: this.record.newsid
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
Ext.reg('virtunewsletter-grid-reports', VirtuNewsletter.grid.Reports);