VirtuNewsletter.combo.Categories = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        url: VirtuNewsletter.config.connectorUrl,
        baseParams: {
            action: 'mgr/categories/getList'
        },
        fields: ['id', 'name'],
        name: 'category_id',
        hiddenName: 'category_id',
        displayField: 'name',
        valueField: 'id'
    });
    VirtuNewsletter.combo.Categories.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.combo.Categories, MODx.combo.ComboBox);
Ext.reg('virtunewsletter-combo-categories', VirtuNewsletter.combo.Categories);