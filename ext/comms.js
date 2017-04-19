(function(){
	var com = angular.module("communicator", []);

	com.factory('crm', ['$sce', 'constants', 'fetch', function($sce, constants, fetch){
		var guid;
		return {
			lasso : {},
			lassoTrack : function(trackcode, title, url){
				var LassoCRM = LassoCRM || {};
				this.lasso = LassoCRM;
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
			lassoGuid : function(fetch){
				if(this.guid === '')
				{
					this.guid = this.lasso.tracker.readCookie("ut");
				}

				if(typeof fetch != 'undefined')
				{
					return this.guid;
				}
			}
		};
	}]);

	com.factory('deliver', ['fetch', 'constants', 'crm', function(fetch, constants, crm){
		var compose = function(from, replyto, to, subject, body, defaultvalue, disclaimer) {
				//parse
				var emaildetails = {
					'from' : encodeURIComponent(from.join('|')),
					'replyTo' : encodeURIComponent(replyto.join('|')),
					'To' : encodeURIComponent(to.join('|')),
					'subject': encodeURIComponent(subject),
					'default' : encodeURIComponent(defaultvalue),
					'disclaimer' : encodeURIComponent(disclaimer),
					'data' : body
				};

				return emaildetails;
		};

		var locked = false;

		return{
			email: function(from, replyto, to, subject, object, defaultvalue, disclaimer, yes, no) {
				if(!locked) {
					locked = true;
					fetch.post(constants.base+'php/mailer.php', {
						'mail' : 1
					}, compose(from, replyto, to, subject, object, defaultvalue, disclaimer), function(response){
						if(typeof yes == 'function')
						{
							yes(response.data);
						}
						locked = false;
					}, function(response){
						if(typeof no == 'function')
						{
							no(response);
						}
						locked = false;
					});
				}
			},
			crm : function(which, object, yes, no, exec) {
				if(!locked) {
					locked = true;

					if(typeof exec == 'undefined') {
						exec = 1;
					}

					fetch.post(constants.base+'php/crm/'+which+'.php', {
						'send' : exec
					}, object, function(response){
						if(typeof yes == 'function')
						{
							yes(response.data);
						}
						locked = false;
					}, function(){
						if(typeof no == 'function')
						{
							no();
						}

						locked = false;
					});
				}
			}
		};
	}]);
})();