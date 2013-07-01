VirtuNewsletter.panel.Newsletter = function(config) {
    config = config || {};
    Ext.apply(config, {
        id: 'virtunewsletter-panel-newsletter',
        border: false,
        baseCls: 'modx-formpanel',
        cls: 'container',
        layout: 'border',
        bodyStyle: 'min-height: 500px;',
        defaults: {
            collapsible: false,
            bodyStyle: 'padding: 15px',
            border: false,
            autoHeight: true
        },
        items: [
            {
                id: 'virtunewsletter-panel-newsletter-center',
                region: 'center',
                html: 'center'
            }, {
                region: 'west',
                xtype: 'virtunewsletter-tree-newsletters',
                bodyStyle: 'padding: 5px;',
                collapsible: true,
                preventRender: true,
                margins: '0 0 0 0',
                cmargins: '0 0 0 5',
                width: 300
            }
        ]
    });
    VirtuNewsletter.panel.Newsletter.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.Newsletter, MODx.Panel);
Ext.reg('virtunewsletter-panel-newsletter', VirtuNewsletter.panel.Newsletter);