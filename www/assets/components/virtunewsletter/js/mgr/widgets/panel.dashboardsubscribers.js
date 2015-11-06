VirtuNewsletter.panel.DashboardSubscribers = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        id: 'virtunewsletter-panel-dashboardsubscribers',
        collapsible: false,
        bodyStyle: 'padding: 10px;',
        border: true,
        header: true,
        method: 'GET',
        cls: '',
        ddGroup: '',
        allowDrop: false,
        preventRender: true,
        layout: 'form',
        labelWidth: 210,
        defaults: {
            readOnly: true
        },
        items: [
            {
                title: _('virtunewsletter.subscribers')
            }, {
                xtype: 'textfield',
                fieldLabel: _('virtunewsletter.subscribers_total'),
                name: 'subscribers_total',
                anchor: '100%'
            }, {
                xtype: 'textfield',
                fieldLabel: _('virtunewsletter.subscribers_active'),
                name: 'subscribers_active',
                anchor: '100%'
            }, {
                xtype: 'textfield',
                fieldLabel: _('virtunewsletter.subscribers_nonmember'),
                name: 'subscribers_nonmember',
                anchor: '100%'
            }, {
                xtype: 'textfield',
                fieldLabel: _('virtunewsletter.subscribers_nonmember_active'),
                name: 'subscribers_nonmember_active',
                anchor: '100%'
            }
        ]
    });

    VirtuNewsletter.panel.DashboardSubscribers.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.DashboardSubscribers, MODx.FormPanel);
Ext.reg('virtunewsletter-panel-dashboardsubscribers', VirtuNewsletter.panel.DashboardSubscribers);