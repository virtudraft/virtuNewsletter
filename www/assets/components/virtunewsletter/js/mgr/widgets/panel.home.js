VirtuNewsletter.panel.Home = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        id: 'virtunewsletter-panel-home',
        preventRender: false,
        items: [
            {
                xtype: 'toolbar',
                border: false,
                columns: 3,
                bodyStyle: 'background: none; background-color: transparent; border:none; ',
                items: [
                    {
                        text: _('virtunewsletter.dashboard'),
                        listeners: {
                            'click': {
                                fn: function () {
                                    return this.openPage('dashboard');
                                },
                                scope: this
                            }
                        }
                    }, {
                        text: _('virtunewsletter.newsletters'),
                        listeners: {
                            'click': {
                                fn: function () {
                                    return this.openPage('newsletters');
                                },
                                scope: this
                            }
                        }
                    }, {
                        text: _('virtunewsletter.categories'),
                        listeners: {
                            'click': {
                                fn: function () {
                                    return this.openPage('categories');
                                },
                                scope: this
                            }
                        }
                    }, {
                        text: _('virtunewsletter.subscribers'),
                        listeners: {
                            'click': {
                                fn: function () {
                                    return this.openPage('subscribers');
                                },
                                scope: this
                            }
                        }
                    }, {
                        text: _('virtunewsletter.templates'),
                        listeners: {
                            'click': {
                                fn: function () {
                                    return this.openPage('templates');
                                },
                                scope: this
                            }
                        }
                    }, {
                        xtype: 'modx-panel',
                        html: '<span style="margin-right: 10px; line-height: 39px;"><span style="font-weight: bold; font-size: 16px;">' + _('virtunewsletter') + '</span> ' + VirtuNewsletter.config.version + '</span>',
                        border: false,
                        bodyStyle: 'margin-left: 20px;'
                    }, {
                        xtype: 'modx-panel',
                        html: '<a href="javascript:void(0);" style="color: #bbbbbb;" id="virtunewsletter_about">' + _('virtunewsletter_about') + '</a>',
                        border: false,
                        bodyStyle: 'font-size: 10px; margin: 15px; background-color: transparent',
                        listeners: {
                            afterrender: function () {
                                Ext.get('virtunewsletter_about').on('click', function () {
                                    var msg = '&copy; 2013-2016, ';
                                    msg += '<a href="http://www.virtudraft.com" target="_blank">';
                                    msg += 'www.virtudraft.com';
                                    msg += '</a><br/>';
                                    msg += 'License GPL v3';
                                    Ext.MessageBox.alert('virtuNewsletter', msg);
                                });
                            }
                        }
                    }
                ]
            }, {
                id: 'virtunewsletter-panel-home-center',
                layout: 'fit',
                bodyStyle: 'background-color: transparent;',
                items: [
                    {
                        xtype: 'virtunewsletter-page-dashboard'
                    }
                ]
            }
        ]
    });

    VirtuNewsletter.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.Home, MODx.Panel, {
    openPage: function (page) {
        var contentPanel = Ext.getCmp('virtunewsletter-panel-home-center');
        contentPanel.removeAll();
        contentPanel.update({
            layout: 'fit'
        });

        contentPanel.add({
            xtype: 'virtunewsletter-page-' + page
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
    remove: function (o) {
        if (typeof (this) === 'array') {
            var index = this.indexOf(o);
            if (index !== -1) {
                this.splice(index, 1);
            }
        }
        return this;
    }
});