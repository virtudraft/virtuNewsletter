var VirtuNewsletter = function(config) {
    config = config || {};
    VirtuNewsletter.superclass.constructor.call(this, config);
};
Ext.extend(VirtuNewsletter, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}
});
Ext.reg('virtunewsletter', VirtuNewsletter);
VirtuNewsletter = new VirtuNewsletter();