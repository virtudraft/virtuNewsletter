VirtuNewsletter.page.Dashboard = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        id: 'virtunewsletter-page-dashboard',
        preventRender: true,
        defaults: {
        },
        items: [
            {
                html: '<h2>' + _('virtunewsletter.dashboard') + '</h2>',
                border: false,
                bodyCssClass: 'panel-desc'
            }, {
                layout: 'column',
                items: [
                    {
                        xtype: 'virtunewsletter-panel-dashboardnewsletter',
                        columnWidth: .5,
                        bodyStyle: 'padding: 10px',
                        listeners: {
                            'afterlayout': {
                                fn: function (cmp) {
                                    var formDom = cmp.getForm().getEl().dom,
                                        _this = this;
                                    if (!_this.dashboardNewsletterMask) {
                                        _this.dashboardNewsletterMask = new Ext.LoadMask(formDom, {
                                            msg: _('virtunewsletter.please_wait')
                                        });
                                    }
                                    cmp.on('afterlayout', function () {
                                        _this.dashboardNewsletterMask.show();
                                    });
                                },
                                scope: this
                            }
                        }
                    }, {
                        xtype: 'virtunewsletter-panel-dashboardsubscribers',
                        columnWidth: .5,
                        bodyStyle: 'padding: 10px',
                        listeners: {
                            'afterlayout': {
                                fn: function (cmp) {
                                    var formDom = cmp.getForm().getEl().dom,
                                        _this = this;
                                    if (!_this.dashboardSubscribersMask) {
                                        _this.dashboardSubscribersMask = new Ext.LoadMask(formDom, {
                                            msg: _('virtunewsletter.please_wait')
                                        });
                                    }
                                    cmp.on('afterlayout', function () {
                                        _this.dashboardSubscribersMask.show();
                                    });
                                },
                                scope: this
                            }
                        }
                    }
                ]
            },
        ],
        listeners: {
            render: {
                fn: function (panel) {
                    return this.getNewsletters();
                },
                scope: this
            }
        }
    });

    VirtuNewsletter.page.Dashboard.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.page.Dashboard, MODx.Panel, {
    getNewsletters: function () {
        var _this = this;
        MODx.Ajax.request({
            url: VirtuNewsletter.config.connectorUrl + '?action=mgr/dashboard/newsletters',
            method: 'GET',
            listeners: {
                'success': {
                    fn: function (response) {
                        if (response.success === true) {
                            var panel = Ext.getCmp('virtunewsletter-panel-dashboardnewsletter');
                            if (panel) {
                                panel.form.setValues(response.object);
                            }
                            if (_this.dashboardNewsletterMask) {
                                _this.dashboardNewsletterMask.hide();
                                _this.dashboardNewsletterMask.disable();
                            }
                            // queueing ajax, because multiple requests are forbidden!
                            return this.getSubscribers();
                        }
                    },
                    scope: this
                },
                'failure': {
                    fn: function (response) {
                        console.log('response', response);
                    },
                    scope: this
                }
            }
        });
    },
    getSubscribers: function () {
        var _this = this;
        MODx.Ajax.request({
            url: VirtuNewsletter.config.connectorUrl + '?action=mgr/dashboard/subscribers',
            method: 'GET',
            listeners: {
                'success': {
                    fn: function (response) {
                        if (response.success === true) {
                            var panel = Ext.getCmp('virtunewsletter-panel-dashboardsubscribers');
                            if (panel) {
                                panel.form.setValues(response.object);
                            }
                            if (_this.dashboardSubscribersMask) {
                                _this.dashboardSubscribersMask.hide();
                                _this.dashboardSubscribersMask.disable();
                            }
                        }
                    },
                    scope: this
                },
                'failure': {
                    fn: function (response) {
                        console.log('response', response);
                    },
                    scope: this
                }
            }
        });
    }
});
Ext.reg('virtunewsletter-page-dashboard', VirtuNewsletter.page.Dashboard);