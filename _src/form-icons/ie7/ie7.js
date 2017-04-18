/* To avoid CSS expressions while still supporting IE 7 and IE 6, use this script */
/* The script tag referencing this file must be placed before the ending body tag. */

/* Use conditional comments in order to target IE 7 and older:
	<!--[if lt IE 8]><!-->
	<script src="ie7/ie7.js"></script>
	<!--<![endif]-->
*/

(function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'form-icons\'">' + entity + '</span>' + html;
	}
	var icons = {
		'form-right': '&#xe90d;',
		'form-left': '&#xe90e;',
		'form-down-light': '&#xe901;',
		'form-menu-light': '&#xe902;',
		'form-search': '&#xe900;',
		'form-down-solid': '&#xe906;',
		'form-close': '&#xe904;',
		'form-down': '&#xe907;',
		'form-close-light': '&#xe905;',
		'form-menu': '&#xe903;',
		'form-checked': '&#xe908;',
		'0': 0
		},
		els = document.getElementsByTagName('*'),
		i, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		c = el.className;
		c = c.match(/form-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());
