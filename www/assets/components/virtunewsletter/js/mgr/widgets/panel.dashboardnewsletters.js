VirtuNewsletter.panel.DashboardNewsletters = function(config) {
    config = config || {};

    Ext.apply(config, {
        id: 'virtunewsletter-panel-dashboardnewsletter',
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
        labelWidth: 150,
        defaults: {
            readOnly: true
        },
        items: [
            {
                xtype: 'textfield',
                fieldLabel: _('virtunewsletter.upcoming_schedules'),
                name: 'upcoming_schedules',
                anchor: '100%'
            }, {
                xtype: 'textfield',
                fieldLabel: _('virtunewsletter.emails_queueing'),
                name: 'emails_queueing',
                anchor: '100%'
            }
        ]
    });

    VirtuNewsletter.panel.DashboardNewsletters.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.DashboardNewsletters, MODx.FormPanel);
Ext.reg('virtunewsletter-panel-dashboardnewsletter', VirtuNewsletter.panel.DashboardNewsletters);