VirtuNewsletter.panel.Home = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        id: 'virtunewsletter-panel-home',
        baseCls: 'modx-formpanel',
        layout: 'border',
        defaults: {
            collapsible: false,
            split: true,
            bodyStyle: 'padding: 15px',
            border: false,
            autoHeight: true
        },
        bodyStyle: 'min-height: 500px; background-color: transparent;',
        preventRender: false,
        items: [
            {
                region: 'north',
                id: 'virtunewsletter-panel-home-north',
                defaults: {
                    border: false,
                    autoHeight: true
                },
                bodyStyle: 'background-color: transparent;',
                items: [
                    {
                        layout: 'hbox',
                        border: false,
                        defaults: {
                            border: false,
                            bodyStyle: 'background-color: transparent;'
                        },
                        bodyStyle: 'background-color: transparent;',
                        items: [
                            {
                                html: '<span style="margin-right: 10px; line-height: 39px;"><span style="font-weight: bold; font-size: 16px;">' + _('virtunewsletter') + '</span> ' + VirtuNewsletter.config.version + '</span>',
                                border: false,
                                cls: 'modx-page-header'
                            }, {
                                xtype: 'toolbar',
                                border: false,
                                columns: 3,
                                bodyStyle: 'background: none; background-color: transparent; border:none; ',
                                items: [
                                    {
                                        text: _('virtunewsletter.dashboard'),
                                        listeners: {
                                            'click': {
                                                fn: function() {
                                                    return this.openPage('dashboard');
                                                },
                                                scope: this
                                            }
                                        }
                                    }, {
                                        text: _('virtunewsletter.newsletters'),
                                        listeners: {
                                            'click': {
                                                fn: function() {
                                                    return this.openPage('newsletters');
                                                },
                                                scope: this
                                            }
                                        }
                                    }, {
                                        text: _('virtunewsletter.subscribers'),
                                        listeners: {
                                            'click': {
                                                fn: function() {
                                                    return this.openPage('subscribers');
                                                },
                                                scope: this
                                            }
                                        }
                                    }, {
                                        text: _('virtunewsletter.templates'),
                                        listeners: {
                                            'click': {
                                                fn: function() {
                                                    return this.openPage('templates');
                                                },
                                                scope: this
                                            }
                                        }
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }, {
                region: 'center',
                id: 'virtunewsletter-panel-home-center',
                padding: 0,
                layout: 'fit',
                bodyStyle: 'background-color: transparent;',
                items: [
                    {
                        xtype: 'virtunewsletter-panel-dashboard'
                    }
                ]
            }, {
                region: 'south',
                id: 'virtunewsletter-panel-home-south',
                html: '<a href="javascript:void(0);" style="color: #bbbbbb;" id="virtunewsletter_about">' + _('virtunewsletter_about') + '</a>',
                border: false,
                bodyStyle: 'font-size: 10px; margin: 5px; background-color: transparent',
                listeners: {
                    afterrender: function() {
                        Ext.get('virtunewsletter_about').on('click', function() {
                            var msg = '&copy; 2013, ';
                            msg += '<a href="http://www.virtudraft.com" target="_blank">';
                            msg += 'www.virtudraft.com';
                            msg += '</a><br/>';
                            msg += 'License GPL v3';
                            Ext.MessageBox.alert('virtuNewsletter', msg);
                        });
                    }
                }
            }
        ],
        listeners: {
            beforerender: {
                fn: function(panel) {
                    var modxHeaderHeight = Ext.get('modx-header').getHeight();
                    var modxContentHeight = Ext.get('modx-content').getHeight();
                    this.height = modxContentHeight - modxHeaderHeight;
                },
                scope: this
            }
        }
    });

    VirtuNewsletter.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.Home, MODx.Panel, {
    openPage: function(page) {
        var contentPanel = Ext.getCmp('virtunewsletter-panel-home-center');
        contentPanel.removeAll();
        contentPanel.update({
            layout: 'fit'
        });

        contentPanel.add({
            xtype: 'virtunewsletter-panel-' + page
        });

        var container = Ext.getCmp('modx-content');
        return container.doLayout();
    }
});
Ext.reg('virtunewsletter-panel-home', VirtuNewsletter.panel.Home);


/**
 * @author goldsky
 * For some reason, the original of this method doesn't work well for treePanel.
 * treePanel comes as an object, not an array.
 * This affects removeAll() in the openPage() method.
 * @class Array
 */
Ext.apply(Array.prototype, {
    /**
     * Removes the specified object from the array.  If the object is not found nothing happens.
     * @param {Object} o The object to remove
     * @return {Array} this array
     */
    remove : function(o){
        if (typeof(this) === 'array') {
            var index = this.indexOf(o);
            if(index !== -1){
                this.splice(index, 1);
            }
        }
        return this;
    }
});