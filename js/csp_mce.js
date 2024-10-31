(function() {
	tinymce.create('tinymce.plugins.cspassword', {
		init : function(ed, url) {
			ed.addButton('cspassword', {
				title : 'Content Password',
				image : url+'/ico.png',
				onclick : function() {
					var csp 		= prompt("Password", "");
					var csp_content = ed.selection.getContent();
					ed.execCommand('mceInsertContent', false, '[cspasswordcode password="' + csp + '"]'+ csp_content +'[/cspasswordcode]');
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : "Password Content Shortcode",
				author : 'ZetRider',
				authorurl : 'http://zetrider.ru/',
				infourl : 'http://zetrider.ru/',
				version : "3.5"
			};
		}
	});
	tinymce.PluginManager.add('cspassword', tinymce.plugins.cspassword);
})();