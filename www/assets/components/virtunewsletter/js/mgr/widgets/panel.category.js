VirtuNewsletter.panel.Category = function(config) {
    config = config || {};

    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        cls: 'container',
        layout: 'anchor',
        items: [
            {
                xtype: 'hidden',
                fieldLabel: _('id'),
                name: 'id',
                value: config.node &&
                        config.node.attributes &&
                        config.node.attributes.catid ? config.node.attributes.catid : 0
            }, {
                anchor: '100%',
                xtype: 'textfield',
                fieldLabel: _('virtunewsletter.category'),
                name: 'name',
                value: config.node &&
                        config.node.attributes &&
                        config.node.attributes.name ? config.node.attributes.name : ''
            }, {
                anchor: '100%',
                xtype: 'textarea',
                fieldLabel: _('virtunewsletter.description'),
                name: 'description',
                value: config.node &&
                        config.node.attributes &&
                        config.node.attributes.description ? config.node.attributes.description : ''
            }
        ],
        bbar: [
            {
                text: _('virtunewsletter.category_update'),
                handler: this.updateCategory,
                scope: this
            }, {
                text: _('virtunewsletter.close'),
                handler: this.cleanCenter,
                scope: this
            }
        ]
    });
    VirtuNewsletter.panel.Category.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.Category, MODx.FormPanel, {
    updateCategory: function(btn, evt) {
        var values = this.form.getValues();

        MODx.Ajax.request({
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/categories/update',
                id: this.config.node.attributes.catid,
                name: values.name,
                description: values.description
            },
            listeners: {
                'success': {
                    fn: function() {
                        var newslettersTree = Ext.getCmp('virtunewsletter-tree-newsletters');
                        return newslettersTree.refreshTree();
                    },
                    scope: this
                }
            }
        });
    },
    cleanCenter: function(){
        var contentPanel = Ext.getCmp('virtunewsletter-panel-newsletter-center');
        contentPanel.removeAll();
        var container = Ext.getCmp('modx-content');
        return container.doLayout();
    }
});
Ext.reg('virtunewsletter-panel-category', VirtuNewsletter.panel.Category);