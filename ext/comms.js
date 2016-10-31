(function(){
	var com = angular.module("communicator", []);

	com.factory('lasso', ['$sce', 'constants', 'fetch', function($sce, constants, fetch){
		return {
			object : {},
			guid : '',
			track : function(trackcode, title, url){
				var LassoCRM = LassoCRM || {};
				this.object = LassoCRM;
				(function(ns){
					ns.tracker = new LassoAnalytics(trackcode);
				})(LassoCRM);
				
				try{
					LassoCRM.tracker.setTrackingDomain(constants.protocol+'www.mylasso.com');
					LassoCRM.tracker.pageTitle = title;
					
					if(typeof url == 'undefined')
					{
						LassoCRM.tracker.pageUrl = $location.url();
					}
					else
					{
						LassoCRM.tracker.pageUrl = url;
					}

					LassoCRM.tracker.imgSrc = LassoCRM.tracker.trackingDomain + '/' + LassoCRM.tracker.namespace + '.gif';

					if($('#'+LassoCRM.tracker.divId).length)
					{
						$('#'+LassoCRM.tracker.divId).remove();
					}

					$('body').append('<div id="' + LassoCRM.tracker.divId + '" style="display:none;"></div>');
					LassoCRM.tracker.track();
					
					//return true;
				}
				catch(error){
					//return false;
				}

			},
			setGuid : function(fetch){
				if(this.guid === '')
				{
					this.guid = this.object.tracker.readCookie("ut");
				}

				if(typeof fetch != 'undefined')
				{
					return this.guid;
				}
			},
			verify: function(data, pass, fail, error) {
				fetch.post(constants.base+'php/lasso.php', {
					'verify' : 1
				}, data, function(response){
					if(typeof pass == 'function') {
						pass(response);
					}

					if(typeof fail == 'function') {
						fail(response);
					}
				}, function(response){
					if(typeof error == 'function') {
						error(response);
					}
				});
			}
		};
	}]);

	com.factory('email', ['fetch', 'constants', function(fetch, constants){
		return{
			functionlock : false,
			send: function(object, yes, no) {
				fetch.post(constants.base+'php/mailer.php', {
					'mail' : 1
				}, object, function(response){
					if(typeof yes == 'function')
					{
						yes(response.data);
					}
				}, function(response){
					if(typeof no == 'function')
					{
						no(response);
					}
				});
			},
			verify: function(data, pass, fail, error) {
				fetch.post(constants.base+'php/mailer.php', {
					'verify' : 1
				}, data, function(response){
					if(typeof pass == 'function') {
						pass(response);
					}

					if(typeof fail == 'function') {
						fail(response);
					}
				}, function(response){
					if(typeof error == 'function') {
						error(response);
					}
				});
			},
			compose: function(from, replyto, to, subject, body) {
				//parse

				var emaildetails = {
					'from' : encodeURIComponent(from.join('|')),
					'replyTo' : encodeURIComponent(replyto.join('|')),
					'To' : encodeURIComponent(to.join('|')),
					'subject': encodeURIComponent(subject),
					'body' : encodeURIComponent(body)
				};

				return emaildetails;
			},
			tabulate: function(object, defaultvalue) {
				//parse
				var out = '<table border="0" width="600" style="margin: auto;"><tbody>';
				var h = '';
				var def = '';
				for(var o in object){
					h = o.split('-').join(' ');
					
					if(decodeURIComponent(object[o]) === ''){
						def = defaultvalue;
					}
					else{
						def = decodeURIComponent(object[o]);
					}

					out = out + '<tr><td style="font-weight: bold;">'+h.charAt(0).toUpperCase()+h.slice(1)+': </td><td>'+def+'</td></tr>';
				}

				out = out+'</tbody></table>';

				return out;
			}
		};
	}]);
})();