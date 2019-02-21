(function(){
	var com = angular.module("communicator", []);
	com.factory('seo', ['$window', 'pathmgr', 'crm', 'constants', function($window, pathmgr, crm, constants){
		var seovars = {};
		var seotrackQueue = {};
		return {
			initialize: function(v) {
				seovars = v;
			},
			view: function(pagetitle, pageurl){
				if(typeof constants.debug_mode == 'boolean' && constants.debug_mode) {
					console.warn('SEO DISABLED: debug mode is on.');
					console.log('Function Trace:', 'seo.view, comms.js');
				}
				else{
					crm.lassoTrack(seovars.lasso, pagetitle, pageurl);
					window.fbq('init', seovars.fbq);
					window.fbq.disablePushState = true;
					window.fbq('track', 'PageView');
					window.ga('set', 'page', pageurl);
					window.ga('send', 'pageview');
				}
			},
			event: function(fbq_args, ga_args) {
				/*
				fbq_args = {
					'init' : {},
					'type' : '',
					'eventname' : ''
				},
				ga_args = {
					'eventname' : '',
					'values' : {}
				}
				*/

				if(typeof constants.debug_mode == 'boolean' && constants.debug_mode){
					console.warn('SEO DISABLED: debug mode detected.');
					console.log('Function Trace:', 'seo.event, comms.js');
				}
				else{
					window.fbq('init', seovars.fbq, fbq_args.init);

					window.fbq(fbq_args.type, fbq_args.eventname);

					if(!ga_args.values) {
						window.ga('send', ga_args.eventname);
					}
					else{
						window.ga('send', ga_args.eventname, ga_args.values);
					}
				}
			},
			fetch: function(what) {
				if(what == 'lassoGuid') {
					return crm.lassoGuid(true);
				}
				else {
					return false;
				}
			}
		};
	}]);

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