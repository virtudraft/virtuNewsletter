VirtuNewsletter.grid.Reports = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        baseParams: {
            action: 'mgr/reports/getList',
            newsletter_id: config.record.id
        },
        fields: ['id', 'newsletter_id', 'subscriber_id', 'email', 'name', 'status', 'status_text', 'status_logged_on'],
        paging: true,
        remoteSort: true,
        anchor: '97%',
        autoExpandColumn: 'email',
        dateFormat: config.dateFormat || 'U',
        displayFormat: config.displayFormat || 'Y-m-d',
        columns: [
            {
                header: _('id'),
                dataIndex: 'id',
                sortable: true,
                width: 80,
                fixed: true,
                hidden: true
            }, {
                header: _('virtunewsletter.news_id'),
                dataIndex: 'newsletter_id',
                sortable: true,
                width: 80,
                fixed: true
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
                sortable: true,
                width: 80,
                fixed: true,
                hidden: true
            }, {
                header: _('virtunewsletter.status'),
                dataIndex: 'status_text',
                sortable: false,
                width: 80
            }, {
                header: _('virtunewsletter.created_on'),
                dataIndex: 'status_logged_on',
                sortable: true,
                width: 100,
                fixed: true,
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
                text: _('virtunewsletter.send_all'),
                scope: this,
                handler: this.sendAll
            }, '->', {
                xtype: 'virtunewsletter-combo-status',
                id: 'virtunewsletter-filterStatus-' + config.record.id,
                width: 100,
                listeners: {
                    'select': {fn: this.filterStatus, scope: this}
                }
            }, {
                xtype: 'textfield',
                id: 'virtunewsletter-search-' + config.record.id,
                emptyText: _('virtunewsletter.search...'),
                listeners: {
                    'change': {fn: this.search, scope: this},
                    'render': {fn: function(cmp) {
                            new Ext.KeyMap(cmp.getEl(), {
                                key: Ext.EventObject.ENTER,
                                fn: function() {
                                    this.fireEvent('change', this);
                                    this.blur();
                                    return true;
                                },
                                scope: cmp
                            });
                        },
                        scope: this}
                }
            }, {
                text: _('virtunewsletter.clear'),
                scope: this,
                handler: this.clearFilter
            }
        ]
    });
    VirtuNewsletter.grid.Reports.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.grid.Reports, MODx.grid.Grid, {
    search: function(tf, nv, ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },
    filterStatus: function(combo, record, index) {
        var s = this.getStore();
        s.baseParams.status = record.data.status;
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },
    clearFilter: function() {
        var s = this.getStore();
        s.baseParams.status = '';
        s.baseParams.query = '';
        Ext.getCmp('virtunewsletter-filterStatus-' + this.config.record.id).reset();
        Ext.getCmp('virtunewsletter-search-' + this.config.record.id).reset();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },
    getMenu: function() {
        var menu = [{
                text: _('virtunewsletter.send'),
                handler: this.send
            }, {
                text: _('virtunewsletter.remove'),
                handler: this.removeQueue
            }
        ];

        if (this.menu.record.status !== 'queue') {
            menu.push({
                text: _('virtunewsletter.requeue'),
                handler: this.reQueue
            });
        }

        return menu;
    },
    generateQueues: function() {
        MODx.msg.confirm({
            title: _('virtunewsletter.create_all'),
            text: _('virtunewsletter.create_all_confirm'),
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/reports/createall',
                newsletter_id: this.record.id
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
                newsletter_id: this.record.id
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
    reQueue: function(btn, e) {
        MODx.Ajax.request({
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/reports/update',
                newsletter_id: this.menu.record.newsletter_id,
                subscriber_id: this.menu.record.subscriber_id,
                status: 'queue'
            },
            listeners: {
                'success': {
                    fn: function() {
                        this.refresh();
                    },
                    scope:this
                }
            }
        });
    },
    sendAll: function() {
        if (!this.pageMask) {
            this.pageMask = new Ext.LoadMask(Ext.getBody(), {
                msg: _('virtunewsletter.please_wait')
            });
        }
        this.pageMask.show();
        MODx.msg.confirm({
            title: _('virtunewsletter.send_now'),
            text: _('virtunewsletter.send_now_confirm'),
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/reports/sendall',
                newsletter_id: this.record.id
            },
            listeners: {
                'success': {
                    fn: function(){
                        this.refresh();
                        this.pageMask.hide();
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
    send: function () {
        MODx.Ajax.request({
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/reports/send',
                newsletter_id: this.menu.record.newsletter_id,
                subscriber_id: this.menu.record.subscriber_id
            },
            listeners: {
                'success': {
                    fn: function () {
                        this.refresh();
                    },
                    scope: this
                }
            }
        });
    }
});
Ext.reg('virtunewsletter-grid-reports', VirtuNewsletter.grid.Reports);