VirtuNewsletter.combo.Status = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        baseParams: {
            action: 'mgr/status/getComboList'
        },
        fields: ['status', 'status_text'],
        name: 'status',
        hiddenName: 'status',
        displayField: 'status_text',
        valueField: 'status',
        lazyRender: true,
        editable: false,
        typeAhead: false,
        triggerAction: 'all'
    });
    VirtuNewsletter.combo.Status.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.combo.Status, MODx.combo.ComboBox);
Ext.reg('virtunewsletter-combo-status', VirtuNewsletter.combo.Status);