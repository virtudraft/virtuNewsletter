VirtuNewsletter.grid.Subscribers = function(config) {
    config = config || {};

    var checkBoxSelMod = new Ext.grid.CheckboxSelectionModel({
        checkOnly: false
    });
    Ext.applyIf(config, {
        id: 'virtunewsletter-grid-subscribers',
        url: VirtuNewsletter.config.connectorUrl,
        baseParams: {
            action: 'mgr/subscribers/getList',
            newsletter_id: config.newsletter_id
        },
        fields: ['id', 'user_id', 'email', 'name', 'usergroups','email_provider',
            'categories_text', 'categories', 'is_active'],
        paging: true,
        remoteSort: true,
        anchor: '97%',
        autoExpandColumn: 'email',
        preventRender: true,
        sm: checkBoxSelMod,
        columns: [
            checkBoxSelMod,
            {
                header: _('id'),
                dataIndex: 'id',
                sortable: true,
                hidden: true,
                width: 30
            }, {
                header: _('virtunewsletter.user_id'),
                dataIndex: 'user_id',
                sortable: true,
                hidden: true,
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
                dataIndex: 'categories_text',
                sortable: true
            }, {
                header: _('virtunewsletter.usergroups'),
                dataIndex: 'usergroups',
                sortable: true
            }, {
                header: _('virtunewsletter.email_provider'),
                dataIndex: 'email_provider',
                sortable: false
            }, {
                xtype: 'checkcolumn',
                header: _('virtunewsletter.active'),
                dataIndex: 'is_active',
                sortable: true,
                width: 70,
                fixed: true,
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
                                        grid.refresh();
                                    }
                                },
                                'failure': {
                                    fn: function(r) {
                                        grid.refresh();
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
                text: _('actions'),
                menu: {
                    xtype: 'menu',
                    plain: true,
                    items: [
                        {
                            text: _('delete'),
                            handler: this.batchDelete,
                            scope: this
                        }, {
                            text: _('virtunewsletter.update'),
                            handler: this.batchUpdate,
                            scope: this
                        }
                    ]
                }
            }, {
                xtype: 'textfield',
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
            }, '->', {
                text: _('virtunewsletter.import_csv'),
                icon: '../assets/components/virtunewsletter/img/table_import.png',
                handler: {
                    xtype: 'virtunewsletter-window-importcsv',
                    blankValues: true
                }
            }, {
                text: _('virtunewsletter.export_csv'),
                icon: '../assets/components/virtunewsletter/img/table_export.png',
                handler: this.exportCsv
            }, {
                text: _('virtunewsletter.sync_usergroups'),
                icon: '../assets/components/virtunewsletter/img/arrow_refresh.png',
                scope: this,
                handler: this.syncUsergroups
            }
        ]
    });
    VirtuNewsletter.grid.Subscribers.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.grid.Subscribers, MODx.grid.Grid, {
    search: function(tf, nv, ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },
    syncUsergroups: function() {
        if (!this.pageMask) {
            this.pageMask = new Ext.LoadMask(Ext.getBody(), {
                msg: _('virtunewsletter.please_wait')
            });
        }
        this.pageMask.show();
        MODx.msg.confirm({
            title: _('virtunewsletter.sync_usergroups'),
            text: _('virtunewsletter.sync_usergroups_confirm'),
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/subscribers/sync'
            },
            listeners: {
                'success': {
                    fn: function(r) {
                        if (r.success) {
                            if (r.object && r.object.count && r.object.start) {
                                this.resyncUsergroups(r.object.start);
                            }
                            if (!r.object.count) {
                                this.pageMask.hide();
                                return this.refresh();
                            }
                        }
                    },
                    scope: this
                },
                'failure': {
                    fn: function(r) {
                    },
                    scope: this
                },
                'cancel': {
                    fn: function() {
                        this.pageMask.hide();
                    },
                    scope: this
                }
            }
        });
    },
    resyncUsergroups: function(start) {
        start = start || 0;

        MODx.Ajax.request({
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/subscribers/sync',
                start: start
            },
            listeners: {
                'success': {
                    fn: function(r) {
                        if (r.success) {
                            if (r.object && r.object.count && r.object.start) {
                                this.resyncUsergroups(r.object.start);
                            }
                            if (!r.object.count) {
                                if (this.pageMask) {
                                    this.pageMask.hide();
                                }
                                return this.refresh();
                            }
                        }
                    },
                    scope: this
                }
            }
        });
    },
    getMenu: function() {
        var menu = [
            {
                text: _('virtunewsletter.subscriber_update'),
                handler: this.updateSubscriber
            }, '-', {
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
    },
    exportCsv: function() {
        MODx.Ajax.request({
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/subscribers/exportcsv'
            },
            listeners: {
                'success': {
                    fn: function(r) {
                        location.href = VirtuNewsletter.config.connectorUrl + '?action=mgr/subscribers/exportcsv&download=' + r.message + '&HTTP_MODAUTH=' + MODx.siteId;
                    },
                    scope: this
                }
            }
        });
    },
    getSelectedAsList: function() {
        var selected = this.getSelectionModel().getSelections();
        if (selected.length <= 0)
            return false;

        var cs = [];
        Ext.each(selected, function(item, idx) {
            cs.push(item.id);
        });
        return cs.join();
    },
    batchDelete: function(btn, e) {
        var ids = this.getSelectedAsList();
        if (!ids) {
            return false;
        }
        MODx.msg.confirm({
            title: _('delete'),
            text: _('virtunewsletter.subscribers_delete_confirm'),
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/subscribers/batchdelete',
                ids: ids
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
    batchUpdate: function(btn, e) {
        var ids = this.getSelectedAsList();
        if (!ids) {
            return false;
        }
        var win = new VirtuNewsletter.window.BatchSubscribers({
            title: _('virtunewsletter.batch_update'),
            baseParams: {
                action: 'mgr/subscribers/batchupdate',
                subscriberIds: ids
            }
        });
        win.reset();
        win.show();
        win.on('success', this.refresh, this);
    },
    updateSubscriber: function() {
        var win = new VirtuNewsletter.window.Subscriber({
            title: _('virtunewsletter.subscriber_update'),
            baseParams: {
                action: 'mgr/subscribers/update'
            }
        });
        win.reset();
        win.setValues(this.menu.record);
        // SuperBoxSelect
        var sb;
        sb = win.fp.getForm().findField('categories[]');
        sb.setValue(this.menu.record.categories);

        win.show();
        win.on('success', this.refresh, this);
    }
});
Ext.reg('virtunewsletter-grid-subscribers', VirtuNewsletter.grid.Subscribers);