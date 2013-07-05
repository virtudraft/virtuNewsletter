VirtuNewsletter.panel.Newsletters = function(config) {
    config = config || {};

    Ext.apply(config, {
        id: 'virtunewsletter-panel-newsletters',
        border: false,
        baseCls: 'modx-formpanel',
        layout: 'border',
        bodyStyle: 'min-height: 300px;',
        defaults: {
            border: false
        },
        items: [
            {
                id: 'virtunewsletter-panel-newsletters-center',
                region: 'center',
                bodyStyle: 'overflow-y: auto;'
            }, {
                region: 'west',
                bodyStyle: 'padding: 5px; overflow-y: auto;',
                collapsible: 'mini',
                split: true,
                margins: '0 0 0 0',
                cmargins: '0 0 0 5',
                width: 300,
                items: [
                    {
                        xtype: 'virtunewsletter-tree-newsletters'
                    }
                ]
            }
        ],
        listeners: {
            beforerender: {
                fn: function(panel) {
                    var homeCenter = Ext.getCmp('virtunewsletter-panel-home-center');
                    panel.height = homeCenter.lastSize.height;
                },
                scope: this
            }
        }
    });
    VirtuNewsletter.panel.Newsletters.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.Newsletters, MODx.Panel);
Ext.reg('virtunewsletter-panel-newsletters', VirtuNewsletter.panel.Newsletters);