VirtuNewsletter.combo.RecurrenceUnit = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        typeAhead: true,
        triggerAction: 'all',
        lazyRender: true,
        mode: 'local',
        store: new Ext.data.ArrayStore({
            id: 0,
            fields: ['key','value'],
            data: [['weekly', 'weekly'], ['monthly', 'monthly'], ['yearly', 'yearly']]
        }),
        valueField: 'key',
        displayField: 'value',
        fields: ['key', 'value'],
        name: 'recurrence_unit',
        hiddenName: 'recurrence_unit'
    });
    VirtuNewsletter.combo.RecurrenceUnit.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.combo.RecurrenceUnit, MODx.combo.ComboBox);
Ext.reg('virtunewsletter-combo-recurrenceunit', VirtuNewsletter.combo.RecurrenceUnit);