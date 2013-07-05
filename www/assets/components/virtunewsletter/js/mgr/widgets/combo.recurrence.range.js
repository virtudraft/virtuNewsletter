VirtuNewsletter.combo.RecurrenceRange = function(config) {
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
        name: 'recurrence_range',
        hiddenName: 'recurrence_range'
    });
    VirtuNewsletter.combo.RecurrenceRange.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.combo.RecurrenceRange, MODx.combo.ComboBox);
Ext.reg('virtunewsletter-combo-recurrence-range', VirtuNewsletter.combo.RecurrenceRange);