VirtuNewsletter.combo.Resources = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        baseParams: {
            action: 'mgr/resources/getComboList'
        },
        pageSize: 10,
        fields: ['id', 'pagetitle'],
        name: 'resource_id',
        hiddenName: 'resource_id',
        displayField: 'pagetitle',
        valueField: 'id',
        lazyRender: true,
        editable: true,
        typeAhead: true
    });
    VirtuNewsletter.combo.Resources.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.combo.Resources, MODx.combo.ComboBox);
Ext.reg('virtunewsletter-combo-resources', VirtuNewsletter.combo.Resources);