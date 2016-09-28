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
				fetch.post(constants.base+'php/lasso.php?verify', data).then(function(response){
					
				}, function(response){

				});
			}
		};
	}]);

	com.factory('email', ['fetch', 'constants', function(fetch, constants){
		return{
			functionlock : false,
			sendmail: function(object, yes, no) {
				fetch.post(constants.canonical+'php/mailer.php?mail', object).then(function(response){
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
			servercheck : function(object, respond, error){
				fetch.post(constants.canonical+'php/mailer.php?verify=1', object).then(function(response){
					respond(response);
				}, function(response){
					error('There was an error in the PHP API endpoint.');
				});
			},
			verify: function(answers, email_fields, pass, fail, error) {
				var o = this;
				if(!o.functionlock){
					o.functionlock = true;
					var passed = [];
					var failed = [];
					var email_verify = [];

					for(var index in answers) {
						if(answers[index] === false){
							failed.push(index);
						}
						else{
							passed.push(index);
						}
					}

					if(failed.length > 0){
						fail(failed);
						return false;
					}


					if(typeof answers != 'object'){
						error('Invalid answers object type.');
					}
					else{
						if($.isArray(email_fields) && email_fields.length > 0){
							for(var i in email_fields){
								if(typeof answers[email_fields[i]] == 'undefined'){
									error('Email fields does not match answer structure.');
									return false;
								}
								else{
									email_verify.push({
										'name' : email_fields[i],
										'value' : answers[email_fields[i]]
									});
								}
							}

							o.servercheck(email_verify, function(response){
								if(response.data.fail.length > 0){
									fail(response.data.fail);
								}
								else{
									pass();
								}
							}, function(err){
								error(err);
							});
						}
						else{
							pass();
						}
					}

				}
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
				for(var o in object){
					h = o.split('-').join(' ');
					out = out + '<tr><td style="font-weight: bold;">'+h.charAt(0).toUpperCase()+h.slice(1)+': </td><td>'+decodeURIComponent(object[o])+'</td></tr>';
				}

				out = out+'</tbody></table>';

				return out;
			}
		};
	}]);
})();