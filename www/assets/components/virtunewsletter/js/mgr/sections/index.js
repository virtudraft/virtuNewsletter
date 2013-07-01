Ext.onReady(function() {
    MODx.load({xtype: 'virtunewsletter-page-home'});
});

VirtuNewsletter.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
                xtype: 'virtunewsletter-panel-home',
                renderTo: 'virtunewsletter-panel-home-div'
            }]
    });
    VirtuNewsletter.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter.page.Home, MODx.Component);
Ext.reg('virtunewsletter-page-home', VirtuNewsletter.page.Home);