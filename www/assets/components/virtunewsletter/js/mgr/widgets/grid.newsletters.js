VirtuNewsletter.grid.Newsletters = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        id: 'virtunewsletter-grid-newsletters',
        url: VirtuNewsletter.config.connectorUrl,
        baseParams: {
            action: 'mgr/newsletters/getList',
            parentId: config.record && config.record.id ? config.record.id : 0
        },
        autoHeight: true,
        fields: ['id', 'subject', 'scheduled_for', 'subscribers', 'queue', 'is_active'],
        paging: true,
        remoteSort: true,
        preventRender: true,
        margins: 15,
        autoExpandColumn: 'subject',
        columns: [
            {
                header: _('id'),
                dataIndex: 'id',
                width: 40,
                sortable: true,
                hidden: true
            }, {
                header: _('virtunewsletter.subject'),
                dataIndex: 'subject',
                sortable: true
            }, {
                header: _('virtunewsletter.scheduled_for'),
                dataIndex: 'scheduled_for',
                sortable: true,
                width: 200,
                fixed: true
            }, {
                header: _('virtunewsletter.subscribers'),
                dataIndex: 'subscribers',
                sortable: true,
                width: 100,
                fixed: true
            }, {
                header: _('virtunewsletter.queue'),
                dataIndex: 'queue',
                sortable: true,
                width: 100,
                fixed: true
            }, {
                xtype: 'checkcolumn',
                header: _('virtunewsletter.active'),
                dataIndex: 'is_active',
                sortable: false,
                width: 70,
                fixed: true
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
                            this.removeNewsletter(rec.data.id);
                        },
                        scope: this
                    }, {
                        iconCls: 'virtunewsletter-icon-magnifier virtunewsletter-icon-actioncolumn-img',
                        tooltip: _('virtunewsletter.detail'),
                        altText: _('virtunewsletter.detail'),
                        handler: function(grid, row, col) {
                            var rec = this.store.getAt(row);
                            this.loadNewsletter(rec.data.id);
                        },
                        scope: this
                    }
                ]
            }
        ],
        tbar: [
            {
                text: _('virtunewsletter.add_new_schedule'),
                handler:  function() {
                    this.newsletterPanel();
                },
                scope: this
            }
        ]
    });

    VirtuNewsletter.grid.Newsletters.superclass.constructor.call(this, config);

    this.getStore().on('load', function() {
        Ext.getCmp('virtunewsletter-newsletters-tabs').doLayout();
    }, this);
};
Ext.extend(VirtuNewsletter.grid.Newsletters, MODx.grid.Grid, {
    getMenu: function(node, e) {
        var menu = [
            {
                text: _('virtunewsletter.remove'),
                handler: function(btn, e) {
                        this.removeNewsletter(this.menu.record.id);
                }
            }
        ];

        return menu;
    },
    removeNewsletter: function(id) {
        MODx.msg.confirm({
            title: _('virtunewsletter.remove'),
            text: _('virtunewsletter.remove_confirm'),
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/newsletters/remove',
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
    loadNewsletter: function(id) {
        if (!this.pageMask) {
            this.pageMask = new Ext.LoadMask(Ext.getBody(), {
                msg: _('virtunewsletter.please_wait')
            });
        }
        this.pageMask.show();

        MODx.Ajax.request({
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/newsletters/get',
                id: id
            },
            listeners: {
                'success': {
                    fn: function(res) {
                        if (res.success === true) {
                            this.newsletterPanel(res.object);
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
    newsletterPanel: function(record) {
        record = record|| {};
        var tabs = Ext.getCmp('virtunewsletter-newsletters-tabs');
        if (typeof(tabs) === 'undefined') {
            return false;
        }
        var newTab = MODx.load({
            title: record.id ? _('virtunewsletter.schedule_update') : _('virtunewsletter.schedule_create'),
            closable: true,
            xtype: 'virtunewsletter-panel-newsletter-content',
            id: 'virtunewsletter-panel-newsletter-content-tab-' + record.id,
            record: record
        });
        tabs.add(newTab);
        tabs.setActiveTab(newTab);
    }
});
Ext.reg('virtunewsletter-grid-newsletters', VirtuNewsletter.grid.Newsletters);