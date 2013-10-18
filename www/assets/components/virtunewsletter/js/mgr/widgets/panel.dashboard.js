VirtuNewsletter.panel.Dashboard = function(config) {
    config = config || {};

    Ext.apply(config, {
        id: 'virtunewsletter-panel-dashboard',
        baseCls: 'modx-formpanel',
        bodyStyle: 'min-height: 500px; overflow-y: scroll;',
        preventRender: true,
        layout: 'column',
        defaults: {
        },
        items: [
            {
                title: _('virtunewsletter.newsletters'),
                xtype: 'virtunewsletter-panel-dashboardnewsletter',
                columnWidth: .5,
                bodyStyle: 'padding: 10px'
            }, {
                title: _('virtunewsletter.subscribers'),
                xtype: 'virtunewsletter-panel-dashboardsubscribers',
                columnWidth: .5,
                bodyStyle: 'padding: 10px'
            }
        ],
        listeners: {
            render: {
                fn: function(panel) {
                    return this.getNewsletters();
                },
                scope: this
            },
            beforerender: {
                fn: function(panel) {
                    var homeCenter = Ext.getCmp('virtunewsletter-panel-home-center');
                    panel.height = homeCenter.lastSize.height;
                },
                scope: this
            }
        }
    });

    VirtuNewsletter.panel.Dashboard.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.Dashboard, MODx.Panel, {
    getNewsletters: function() {
        MODx.Ajax.request({
            url: VirtuNewsletter.config.connectorUrl + '?action=mgr/dashboard/newsletters',
            listeners: {
                'success': {
                    fn: function(response) {
                        if (response.success === true) {
                            var panel = Ext.getCmp('virtunewsletter-panel-dashboardnewsletter');
                            panel.form.setValues(response.object);
                            // queueing ajax, because multiple requests are forbidden!
                            return this.getSubscribers();
                        }
                    },
                    scope: this
                },
                'failure': {
                    fn: function(response) {
                        console.log('response', response);
                    },
                    scope: this
                }
            }
        });
    },
    getSubscribers: function() {
        MODx.Ajax.request({
            url: VirtuNewsletter.config.connectorUrl + '?action=mgr/dashboard/subscribers',
            listeners: {
                'success': {
                    fn: function(response) {
                        if (response.success === true) {
                            var panel = Ext.getCmp('virtunewsletter-panel-dashboardsubscribers');
                            panel.form.setValues(response.object);
                        }
                    },
                    scope: this
                },
                'failure': {
                    fn: function(response) {
                        console.log('response', response);
                    },
                    scope: this
                }
            }
        });
    }
});
Ext.reg('virtunewsletter-panel-dashboard', VirtuNewsletter.panel.Dashboard);