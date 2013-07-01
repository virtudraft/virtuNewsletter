VirtuNewsletter.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config, {
        id: 'virtunewsletter-panel-home',
        border: false,
        baseCls: 'modx-formpanel',
        cls: 'container',
        items: [
            {
                html: '<h2>' + _('virtunewsletter') + '</h2>',
                border: false,
                cls: 'modx-page-header'
            }, {
                xtype: 'modx-tabs',
                defaults: {
                    border: false,
                    autoHeight: true
                },
                border: true,
                items: [
                    {
                        title: _('virtunewsletter.newsletters'),
                        preventRender: true,
                        defaults: {
                            autoHeight: true
                        },
                        items: [
                            {
                                html: '<p>' + _('virtunewsletter.newsletters_desc') + '</p>',
                                border: false,
                                bodyCssClass: 'panel-desc'
                            }, {
                                xtype: 'virtunewsletter-panel-newsletter',
                                cls: 'main-wrapper',
                                preventRender: true
                            }
                        ]
                    },
                    {
                        title: _('virtunewsletter.subscribers'),
                        preventRender: true,
                        defaults: {
                            autoHeight: true
                        },
                        items: [
                            {
                                html: '<p>' + _('virtunewsletter.subscribers_desc') + '</p>',
                                border: false,
                                bodyCssClass: 'panel-desc'
                            }
                        ]
                    }
                ],
                listeners: {
                    'afterrender': function(tabPanel) {
                        tabPanel.doLayout();
                    }
                }
            }, {
                html: '<a href="javascript:void(0);" style="color: #bbbbbb;" id="virtunewsletter_about">' + _('virtunewsletter_about') + '</a>',
                border: false,
                bodyStyle: 'font-size: 10px; text-align: right; margin: 5px;',
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
        ]
    });
    VirtuNewsletter.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.panel.Home, MODx.Panel);
Ext.reg('virtunewsletter-panel-home', VirtuNewsletter.panel.Home);