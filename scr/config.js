/*jslint plusplus: true */
cfg.factory('fetch', ['$http','$sce',function($http, $sce){
	return {
		url: function(urlresource)
		{
			return $http.get(urlresource);
		},
		secured: function(urlresource)
		{
			return $http.get($sce.trustAsResourceUrl(urlresource));
		},
		post: function(urlresource, data)
		{
			return $http.post($sce.trustAsResourceUrl(urlresource), data);
		}
	};
}]);

cfg.factory('preloader', ['$http', '$sce', 'constants', function($http, $sce, constants){
	return {
		run: function(imgarray, act, whilest){
			var count = 0;
			var done = 0;
			var limit = imgarray.length;
			var i;
			var lsuccess = function() {
				count++;
				done++;
			};

			var lerror = function() {
				done++;
			};

			var imgs = '';
			for(i = 0; i<limit; i++)
			{
				if(['bmp', 'png', 'tiff', 'jpg', 'jpeg', 'gif', 'svg'].indexOf(imgarray[i].split('.').pop()) >= 0){
					imgs = imgs+'<img src="'+imgarray[i]+'" alt="preloading-'+i+'" />';
				}
				else{
					$http.get($sce.trustAsResourceUrl(imgarray[i])).success(lsuccess).error(lerror);
				}
			}

			$('#preloader').append(imgs);

			$('#preloader img').on('load', function(){
				lsuccess();
			});

			$('#preloader img').on('error', function(){
				//if any gets through
				lerror();
			});

			var success = false;

			var x = setInterval(function(){
				if(typeof whilest == 'function'){
					whilest([count, limit]);
				}
				
				if(done == limit)
				{
					clearInterval(x);
					if(count == done)
					{
						success = true;
						$('#preloader').empty();
					}
					act(success);
				}
			}, 50);
		}
	};
}]);

cfg.factory('features', [function(){
	return {
		run : function(){
			this.feature['2D Transform'] = Modernizr.csstransforms;
			this.feature['3D Transform'] = Modernizr.csstransforms3d;
			this.feature.svg = Modernizr.svg;
			this.feature.touch = Modernizr.touchevents;
			this.feature.video = Modernizr.video;

			if(Modernizr.mq('only all and (max-width: 1024px)') && Modernizr.touchevents)
			{
				this.feature.mobile = true;
			}
			else
			{
				this.feature.mobile = false;
			}
		},
		detect : function(feature) {
			var detection = ['2D Transform', '3D Transform', 'svg', 'touch', 'mobile', 'video'];

			if(detection.indexOf(feature) >= 0)
			{
				return this.feature[feature];
			}
			else
			{
				return false;
			}
		},
		feature : {
		}
	};
}]);

cfg.factory('lasso', ['$sce', 'constants', function($sce, constants){
	return {
		submission : $sce.trustAsResourceUrl('http://www.mylasso.com/registrant_signup.php'),
		object : {},
		guid : '',
		track : function(url){
			var LassoCRM = LassoCRM || {};
			this.object = LassoCRM;
			(function(ns){
				ns.tracker = new LassoAnalytics('LAS-921473-16');
			})(LassoCRM);
			
			try{
				LassoCRM.tracker.setTrackingDomain(constants.protocol+'www.mylasso.com');
				LassoCRM.tracker.pageTitle = 'Henry';
				
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
		}
	};
}]);

cfg.factory('email', ['fetch', 'constants', function(fetch, constants){
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

cfg.factory('sources', ['$sce', 'fetch', 'preloader', 'constants', 'browser', 'features', function($sce, fetch, preloader, constants, browser, features){
	return{
		loading: 0,
		contents: {},
		img: [],
		get: function(action, before, whilest){
			var o = this;
			var svg = false;
			if(o.loading === 0)
			{
				
				if(features.detect('svg')){
					svg = 'true';
				}
				else{
					svg = 'false';
				}
				fetch.secured(constants.canonical+'_data/main.php?request=1&svg='+svg).then(function(response){
					o.contents = response.data.contents;
					//record images
					var pr = response.data.preload;
					var imgar = [];
					for(var i=0; i<pr.length; i++)
					{
						imgar[i] = pr[i];
						o.indexer(pr[i]);
					}

					if(typeof before == 'function')
					{
						before(response.data);
					}

					preloader.run(imgar, function(){
						action(response.data.contents);
						o.loading = 1;
					}, function(progress){
						if(typeof whilest == 'function'){
							whilest(progress);
						}
					});
				}, function(response){
					o.contents = false;
					action(false);
				});
			}
			else
			{
				var beforeloadinfo = {
					preload  : o.img,
					contents : o.contents
				};
				before(beforeloadinfo);
				action(o.contents);
			}
		},
		indexer: function(url) {
			this.img[this.img.length] = url;
		},
		image: function(name) {
			var o = this;

			for(var i = 0; i<o.img.length; i++)
			{
				if(o.img[i].name == name)
				{
					return $sce.trustAsResourceUrl(o.img[i].url);
				}
			}

			return false;
		}
	};
}]);
