/**
 * Loads the create snippet page
 * 
 * @class MODx.page.CreateSnippet
 * @extends MODx.Component
 * @param {Object} config An object of config properties
 * @xtype modx-page-snippet-create
 */
MODx.page.CreateSnippet = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		formpanel: 'modx-panel-snippet'
		,actions: {
            'new': MODx.action['element/snippet/create']
            ,edit: MODx.action['element/snippet/update']
            ,cancel: MODx.action['welcome']
        }
        ,buttons: [{
            process: 'create', text: _('save'), method: 'remote'
            ,checkDirty: true
            ,keys: [{
                key: "s"
                ,alt: true
                ,ctrl: true
            }]
        },{
            process: 'cancel', text: _('cancel'), params: {a:MODx.action['welcome']}
        }]
        ,loadStay: true
        ,components: [{
            xtype: 'modx-panel-snippet'
            ,renderTo: 'modx-panel-snippet'
            ,snippet: 0
            ,category: config.category || 0
            ,name: ''
        }]
	});
	MODx.page.CreateSnippet.superclass.constructor.call(this,config);
};
Ext.extend(MODx.page.CreateSnippet,MODx.Component);
Ext.reg('modx-page-snippet-create',MODx.page.CreateSnippet);