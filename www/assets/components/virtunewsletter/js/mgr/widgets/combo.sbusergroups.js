VirtuNewsletter.combo.SBUsergroups = function (config) {
    config = config || {};

    var store = new Ext.data.JsonStore({
        url: VirtuNewsletter.config.connectorUrl,
        totalProperty: 'total',
        root: 'results',
        baseParams: {
            action: 'mgr/usergroups/getcombolist'
        },
        autoLoad: true,
        autoSave: false,
        dir: 'ASC',
        fields: ['id', 'name']
    });

    Ext.applyIf(config, {
        triggerAction: 'all',
        mode: 'remote',
        store: store,
        pageSize: 20,
        minChars: 1,
        allowAddNewData: false,
        addNewDataOnBlur: false,
//        value: config.record || '',
//        originalValue: config.record || '',
        valueDelimiter: ",",
        queryValuesDelimiter: ",",
        extraItemCls: 'x-tag',
//        width: 400,
        displayField: "name",
        valueField: "id",
        queryDelay: 1000,
        resizable: true,
        hideTrigger: true,
        allowBlank: true,
        listWidth: 200,
        maxHeight: 300,
        typeAhead: true,
        typeAheadDelay: 250,
        editable: true,
//        listEmptyText: _('virtunewsletter.listEmptyText'),
        autoSelect: false,
        forceSelection: false,
        stackItems: false,
        msgTarget: 'under',
        forceFormValue: false
    });
    VirtuNewsletter.combo.SBUsergroups.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.combo.SBUsergroups, Ext.ux.form.SuperBoxSelect);
Ext.reg('virtunewsletter-combo-sbusergroups', VirtuNewsletter.combo.SBUsergroups);