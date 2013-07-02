VirtuNewsletter.tree.Newsletters = function(config) {
    config = config || {};

    Ext.QuickTips.init();

    var newslettersTree = new Ext.tree.TreeLoader({
        id: 'virtunewsletter-treeloader-newsletterstree',
        dataUrl: VirtuNewsletter.config.connectorUrl,
        baseParams: {
            action: 'mgr/categories/getTreeList'
        },
        listeners: {
            load: function(object, node, response) {
                if (node.attributes.id === 'newslettersRoot') {
                    var data = Ext.util.JSON.decode(response.responseText);
                    var dataArray = data.results;
                    var usersTree = Ext.getCmp('virtunewsletter-tree-newsletters');
                    var root = usersTree.getRootNode();
                    Ext.each(dataArray, function(data, i) {
                        data.qtip = data.description;
                        root.appendChild(data);
                    });
                } else {
                    var childData = Ext.util.JSON.decode(response.responseText);
                    var childDataArray = childData.results;
                    Ext.each(childDataArray, function(child, i) {
                        child.qtip = child.description;
                        node.appendChild(child);
                    });
                }
                /* overide the loader */
                this.baseParams = {
                    action: 'mgr/newsletters/getTreeList'
                };
            },
            beforeload: function(object, node, callback) {
                if (node.attributes.id !== 'newslettersRoot') {
                    this.baseParams.category_id = node.attributes.catid;
                }
            }
        }
    });

    Ext.apply(config, {
        id: 'virtunewsletter-tree-newsletters',
        xtype: 'treepanel',
        loader: newslettersTree,
        root: {
            nodeType: 'async',
            text: _('virtunewsletter.newsletters'),
            draggable: false,
            id: 'newslettersRoot',
            expanded: true
        },
        rootVisible: false,
        tbar: [
            {
                tooltip: {
                    text: _('virtunewsletter.expand_all')
                },
                icon: MODx.config.manager_url + 'templates/default/images/restyle/icons/arrow_down.png',
                handler: function() {
                    var usersTreeCmp = Ext.getCmp('virtunewsletter-tree-newsletters');
                    return usersTreeCmp.expandAll();
                }
            }, {
                tooltip: {
                    text: _('virtunewsletter.collapse_all')
                },
                icon: MODx.config.manager_url + 'templates/default/images/restyle/icons/arrow_up.png',
                handler: function() {
                    var usersTreeCmp = Ext.getCmp('virtunewsletter-tree-newsletters');
                    return usersTreeCmp.collapseAll();
                }
            }, {
                tooltip: {
                    text: _('virtunewsletter.refresh')
                },
                icon: MODx.config.manager_url + 'templates/default/images/restyle/icons/refresh.png',
                handler: this.refreshTree
            }, {
                tooltip: {
                    text: _('virtunewsletter.add_new_category')
                },
                icon: MODx.config.manager_url + 'templates/default/images/restyle/icons/folder_add.png',
                handler: function() {
                    var contentPanel = Ext.getCmp('virtunewsletter-panel-newsletter-center');
                    contentPanel.removeAll();

                    var catWindow = new VirtuNewsletter.window.Category({
                        title: _('virtunewsletter.category_create'),
                        baseParams: {
                            action: 'mgr/categories/create'
                        },
                        blankValues: true,
                        success: function() {
                            var newslettersTree = Ext.getCmp('virtunewsletter-tree-newsletters');
                            return newslettersTree.refreshTree();
                        }
                    });
                    return catWindow.show();
                }
            }, {
                tooltip: {
                    text: _('virtunewsletter.add_new_schedule')
                },
                icon: MODx.config.manager_url + 'templates/default/images/restyle/icons/new-static-resource.png',
                handler: function() {
                    var contentPanel = Ext.getCmp('virtunewsletter-panel-newsletter-center');
                    contentPanel.removeAll();

                    var schWindow = new VirtuNewsletter.window.Schedule({
                        title: _('virtunewsletter.schedule_create'),
                        baseParams: {
                            action: 'mgr/newsletters/create'
                        },
                        blankValues: true,
                        success: function() {
                            var newslettersTree = Ext.getCmp('virtunewsletter-tree-newsletters');
                            return newslettersTree.refreshTree();
                        }
                    });
                    return schWindow.show();
                }
            }
        ],
        title: _('virtunewsletter.newsletterstree.title'),
        autoScroll: true,
        enableDD: false,
        containerScroll: true,
        listeners: {
            click: function(node) {
                if (!node.attributes.content) {
                    if (node.expanded === true) {
                        // odd?
                        node.collapse();
                    } else {
                        node.expand();
                    }
                    return this.categoriesPanel(node);
                } else {
                    return this.newslettersPanel(node);
                }
            },
            render: function() {
                return this.getRootNode().expand(true);
            },
            collapse: function(panel) {
                var contentPanel = Ext.getCmp('virtunewsletter-panel-newsletter-center');
                return contentPanel.doLayout();
            },
            expand: function(panel) {
                var contentPanel = Ext.getCmp('virtunewsletter-panel-newsletter-center');
                return contentPanel.doLayout();
            }
        }
    });

    VirtuNewsletter.tree.Newsletters.superclass.constructor.call(this, config);
};

//Ext.extend(VirtuNewsletter.tree.Newsletters, Ext.tree.TreePanel, {
Ext.extend(VirtuNewsletter.tree.Newsletters, MODx.tree.Tree, {
    _saveState: function() {
    }, // override MODX's
    getMenu: function(node, e) {
        var menu = [];
        if (node.attributes.catid) {
            menu.push({
                text: _('virtunewsletter.update'),
                scope: this,
                handler: this.updateCategory
            });
            menu.push({
                text: _('virtunewsletter.remove'),
                scope: this,
                handler: this.removeCategory
            });
        } else {
            menu.push({
                text: _('virtunewsletter.update'),
                scope: this,
                handler: this.updateNewsletter
            });
            menu.push({
                text: _('virtunewsletter.remove'),
                scope: this,
                handler: this.removeNewsletter
            });
        }


        return menu;
    },
    removeCategory: function() {
        var node = this.cm.activeNode;

        MODx.msg.confirm({
            title: _('virtunewsletter.remove'),
            text: _('virtunewsletter.remove_confirm'),
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/categories/remove',
                id: node.attributes.catid
            },
            listeners: {
                'success': {
                    fn: this.refreshTree,
                    scope: this
                }
            }
        });
    },
    updateCategory: function() {
        var node = this.cm.activeNode;

        var catWindow = new VirtuNewsletter.window.Category({
            title: _('virtunewsletter.category_update'),
            baseParams: {
                action: 'mgr/categories/update'
            },
            blankValues: true,
            success: function() {
                var newslettersTree = Ext.getCmp('virtunewsletter-tree-newsletters');
                return newslettersTree.refreshTree();
            },
            node: node
        });

        node.attributes.id = node.attributes.catid;
        catWindow.setValues(node.attributes);
        return catWindow.show();
    },
    updateNewsletter: function() {
        var node = this.cm.activeNode;

        var schWindow = new VirtuNewsletter.window.Schedule({
            title: _('virtunewsletter.schedule_update'),
            baseParams: {
                action: 'mgr/newsletters/update'
            },
            blankValues: true,
            success: function() {
                var newslettersTree = Ext.getCmp('virtunewsletter-tree-newsletters');
                return newslettersTree.refreshTree();
            },
            node: node
        });
        node.attributes.id = node.attributes.newsid;
        schWindow.setValues(node.attributes);
        return schWindow.show();
    },
    removeNewsletter: function() {
        var node = this.cm.activeNode;

        MODx.msg.confirm({
            title: _('virtunewsletter.remove'),
            text: _('virtunewsletter.remove_confirm'),
            url: VirtuNewsletter.config.connectorUrl,
            params: {
                action: 'mgr/newsletters/remove',
                id: node.attributes.newsid
            },
            listeners: {
                'success': {
                    fn: function() {
                        this.refreshNode(node.attributes.id);
                        var contentPanel = Ext.getCmp('virtunewsletter-panel-newsletter-center');
                        contentPanel.removeAll();
                        var container = Ext.getCmp('modx-content');
                        return container.doLayout();
                    },
                    scope: this
                }
            }
        });
    },
    refreshTree: function(parentId) {
        parentId = Number(parentId) ? Number(parentId) : 0;

        var categoriesTree = Ext.getCmp('virtunewsletter-tree-newsletters');
        categoriesTree.getLoader().baseParams = {
            action: 'mgr/categories/getTreeList'
        };
        return categoriesTree.getLoader().load(categoriesTree.root);
    },
    newslettersPanel: function(node) {
        var contentPanel = Ext.getCmp('virtunewsletter-panel-newsletter-center');
        contentPanel.removeAll();
        contentPanel.update({
            layout: 'fit'
        });

        contentPanel.add({
            xtype: 'virtunewsletter-panel-newsletter-content',
            node: node,
            preventRender: true,
            region: 'center',
            autoScroll: true
        });

        var container = Ext.getCmp('modx-content');

        return container.doLayout();
    },
    categoriesPanel: function(node) {
        var contentPanel = Ext.getCmp('virtunewsletter-panel-newsletter-center');
        contentPanel.removeAll();
        contentPanel.update({
            layout: 'fit'
        });

        contentPanel.add({
            xtype: 'virtunewsletter-panel-category',
            node: node,
            preventRender: true
        });
        var container = Ext.getCmp('modx-content');

        return container.doLayout();
    }
});
Ext.reg('virtunewsletter-tree-newsletters', VirtuNewsletter.tree.Newsletters);