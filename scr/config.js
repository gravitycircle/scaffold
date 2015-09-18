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

cfg.factory('preloader', ['$http', '$sce', function($http, $sce){
	return {
		run: function(imgarray, act){
			var count = 0;
			var done = 0;
			var limit = imgarray.length;

			var lsuccess = function(data) {
				count++;
				done++;
			};

			var lerror = function(data) {
				done++;
			};

			for(i = 0; i<limit; i++)
			{
				$http.get($sce.trustAsResourceUrl(imgarray[i])).success(lsuccess).error(lerror);
			}

			var success = false;

			var x = setInterval(function(){
				if(done == limit)
				{
					clearInterval(x);
					if(count == done)
					{
						success = true;
					}
					act(success);
				}
			}, 300);
		}
	};
}]);

cfg.factory('features', [function(){
	return {
		run : function(){
			this.feature['2D Transform'] = Modernizr.csstransforms;
			this.feature['3D Transform'] = Modernizr.csstransforms3d;
			this.feature['svg'] = Modernizr.svg;
			this.feature['touch'] = Modernizr.touch;

			if(Modernizr.mq('only all and (max-width: 1024px)') && Modernizr.touch)
			{
				this.feature['mobile'] = true;
			}
			else
			{
				this.feature['mobile'] = false;
			}
		},
		detect : function(feature) {
			var detection = ['2D Transform', '3D Transform', 'svg', 'touch', 'mobile'];

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

cfg.factory('email', ['fetch', 'constants', function(fetch, constants){
	return{
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
		verify: function(object, structure, requiredkey, success, failure) {
			//verify submission structure
			if(structure.constructor !== Array)
			{
				return false;
			}

			var indices = [];

			for(var key in object[0]){
				indices.push(key);
			}

			if(indices.length != structure.length)
			{
				return false;
			}

			for(var i = 0; i < indices.length; i++)
			{
				if(structure.indexOf(indices[i]) < 0)
				{
					return false;
				}
			}

			if(structure.indexOf(requiredkey) < 0)
			{
				return false;
			}

			//structure verified

			//entry verification

			var pass = [];
			var fail = [];
			var email = [];
			for(i = 0; i<object.length; i++)
			{
				if(typeof object[i][requiredkey] != 'undefined' && !(!object[i][requiredkey]) && object[i][requiredkey] == 'yes')
				{
					if(object[i]['value'] !== '')
					{
						if(object[i]['filter'] == 'none')
						{
							pass[pass.length] = object[i]['key'];
						}
						else if(object[i]['filter'] == 'email')
						{
							email[email.length] = object[i];
						}
					}
					else
					{
						fail[fail.length] = object[i]['key'];
					}
				}
				else
				{
					pass[pass.length] = object[i]['key'];
				}
			}

			//structure verification done. some pass some fail. starting email verification.

			if(email.length < 1)
			{
				if(fail.length && typeof failure == 'function')
				{
					failure({
						'empty' : fail,
						'invalid' : []
					});
				}
				else
				{
					if(typeof success == 'function')
					{
						success();
					}
				}
				return true;
			}

			var batch = [];
			var emailpass = [];
			var emailfail = [];
			for(i = 0; i<email.length; i++)
			{
				batch[batch.length] = email[i]['value'];
			}

			var passed = false;

			fetch.secured(constants.canonical+'php/mailer.php?verify='+batch.join('|')).then(function(response){
				if(response.data.verified == 1)
				{
					emailpass = batch;

					if(fail.length === 0 && emailfail.length === 0)
					{
						passed = true;
					}
				}
				else if (response.data.verified === 0)
				{
					for(i=0; i<object.length; i++)
					{
						if(object[i]['value'] == batch[0])
						{
							emailfail[emailfail.length] = object[i].key;
						}
					}
					passed = false;
				}
				else
				{
					for(i = 0; i<batch.length; i++)
					{
						if(response.data.verified.indexOf(batch[i]) >= 0)
						{
							emailpass[emailpass.length] = batch[i];
						}
						else
						{
							for(g=0; g<object.length; g++)
							{
								if(object[g]['value'] == batch[i])
								{
									emailfail[emailfail.length] = object[g].key;
								}
							}
						}
					}

					if(fail.length === 0 && emailfail.length === 0)
					{
						passed = true;
					}
				}

				if(passed) {
					if(typeof success == 'function'){
						success();
					}
				}
				else
				{
					if(typeof failure == 'function'){
						failure({
							'empty' : fail,
							'invalid' : emailfail
						});
					}
				}

			}, function(response){
				emailfail = batch;

				//endpoint fail
				if(typeof failed == 'function'){
					failure({
						'empty' : fail,
						'invalid' : emailfail
					});
				}
			});

			return true;
		},
		compose: function(from, replyto, to, subject, body, cc, bcc) {
			//parse
			if(cc.constructor === Array)
			{
				cc = cc.join(',');
			}

			if(bcc.constructor === Array)
			{
				bcc = bcc.join(',');
			}

			var emaildetails = {
				'from' : encodeURIComponent(from),
				'replyTo' : encodeURIComponent(replyto),
				'To' : encodeURIComponent(to),
				'subject': encodeURIComponent(subject),
				'body' : encodeURIComponent(body),
				'cc': encodeURIComponent(cc),
				'bcc': encodeURIComponent(bcc)
			};

			return emaildetails;
		},
		tabulate: function(object, keys, alias, prototype) {
			//parse
			if(keys.constructor === Array) {
				var indices = [];

				if(typeof prototype != 'object') {
					prototype = object[0];
				}

				for(var key in prototype){
					indices.push(key);
				}

				//verify structure (again)
				var pass = true;
				for(var i = 0; i<keys.length; i++)
				{
					if(indices.indexOf(keys[i]) < 0)
					{
						pass = false;
					}
				}

				if(!pass){
					return false;
				}

				var body = '';
				var headfoot = '';
				var k;
				if(!(alias.constructor === Array && alias.length == keys.length))
				{
					alias = [];
					for(k = 0; k < keys.length; k++)
					{
						alias[k] = keys[k];
					}
				}

				for(i=0; i<alias.length; i++)
				{
					headfoot = headfoot + '<th style="font-weight: bold; text-align: left; padding: 5px; border: 1px solid #000; text-transform: uppercase;">'+alias[i]+'</th>';
				}

				var info;
				for(i=0; i<object.length; i++)
				{
					info = '<tr>';
					for(k = 0; k < keys.length; k++)
					{
						var sanitized = object[i][keys[k]];

						if(sanitized === '')
						{
							sanitized = '<i>Not specified</i>';
						}

						info = info + '<td style="border: 1px solid #000; padding: 5px; text-align: left;">'+sanitized+'</td>';
					}
					info = info+'</tr>';

					body = body + info;
				}

				return '<table style="border-collapse: collapse; width: 100%;"><thead>'+headfoot+'</thead><tbody>'+body+'</tbody></table>';
			}
			else
			{
				return false;
			}
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

cfg.factory('sources', ['$sce', 'fetch', 'preloader', 'constants', function($sce, fetch, preloader, constants){
	return{
		loading: 0,
		contents: {},
		img: [],
		get: function(action, before){
			var o = this;
			if(o.loading === 0)
			{
				fetch.secured(constants.canonical+'_data/main.php').then(function(response){
					o.contents = response.data.contents;
					//record images
					var pr = response.data.preload;
					var imgar = [];
					for(var i=0; i<pr.length; i++)
					{
						imgar[i] = pr[i].url;
						o.indexer(pr[i].name, pr[i].url);
					}

					if(typeof before == 'function')
					{
						before(response.data);
					}

					preloader.run(imgar, function(){
						action(response.data.contents);
						o.loading = 1;
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
		indexer: function(name, url) {
			this.img[this.img.length] = {
				'name' : name,
				'url' : url
			};
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
