VirtuNewsletter.combo.Usergroups = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        baseParams: {
            action: 'mgr/usergroups/getComboList'
        },
        pageSize: 10,
        fields: ['id', 'name'],
        name: 'usergroup_id',
        hiddenName: 'usergroup_id',
        displayField: 'name',
        valueField: 'id',
        lazyRender: true
    });
    VirtuNewsletter.combo.Usergroups.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.combo.Usergroups, MODx.combo.ComboBox);
Ext.reg('virtunewsletter-combo-usergroups', VirtuNewsletter.combo.Usergroups);