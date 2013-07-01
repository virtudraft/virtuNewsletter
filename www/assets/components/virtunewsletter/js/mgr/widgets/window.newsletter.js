VirtuNewsletter.window.Schedule = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        fields: [
            {
                xtype: 'hidden',
                name: 'id'
            }, {
                xtype: 'textfield',
                fieldLabel: _('virtunewsletter.subject'),
                name: 'subject',
                anchor: '100%'
            }, {
                layout: 'column',
                columns: 2,
                defaults: {
                    layout: 'form'
                },
                items: [
                    {
                        columnWidth: .5,
                        items: [
                            {
                                xtype: 'virtunewsletter-combo-categories',
                                fieldLabel: _('virtunewsletter.category'),
                                name: 'category_id'
                            }, {
                                xtype: 'numberfield',
                                fieldLabel: _('virtunewsletter.resource_id'),
                                name: 'resource_id'
                            }
                        ]
                    }, {
                        columnWidth: .5,
                        items: [
                            {
                                xtype: 'datefield',
                                fieldLabel: _('virtunewsletter.scheduled_for'),
                                name: 'scheduled_for'
                            }, {
                                xtype: 'xcheckbox',
                                boxLabel: _('virtunewsletter.is_recurring'),
                                name: 'is_recurring'
                            }
                        ]
                    }
                ]
            }
        ]
    });
    VirtuNewsletter.window.Schedule.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.window.Schedule, MODx.Window);
Ext.reg('virtunewsletter-window-schedule', VirtuNewsletter.window.Schedule);