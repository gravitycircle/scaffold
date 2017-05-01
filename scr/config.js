/*jslint plusplus: true */
cfg.factory('fetch', ['$http','$sce', 'constants',function($http, $sce, constants){
	return {
		get: function(urlresource)
		{
			return $http.get(urlresource);
		},
		secureget: function(url, params, action, fail)
		{
			$http.get(constants.base+'php/request.php?token='+constants.api).then(function(resp){
				//pass
				var parameters = $.param(params);
				var token = resp.data.key;

				$http.get($sce.trustAsResourceUrl(url)+'?request='+token+'&'+parameters).then(function(response){
					if(typeof action == 'function') {
						action(response);
					}
				}, function(){
					if(typeof fail == 'function') {
						fail('Invalid API Key');
					}
				});

			}, function(){
				//fail
				if(typeof fail == 'function') {
					fail('Invalid Request Key');
				}
			});
		},
		post: function(url, params, data, action, fail)
		{
			$http.get(constants.base+'php/request.php?token='+constants.api).then(function(resp){
				//pass
				var parameters = $.param(params);
				var token = resp.data.key;

				$http.post($sce.trustAsResourceUrl(url)+'?key='+token+'&'+parameters, data).then(function(response){
					if(typeof action == 'function') {
						action(response);
					}
				}, function(){
					if(typeof fail == 'function') {
						fail('Invalid API Key');
					}
				});

			}, function(){
				//fail
				if(typeof fail == 'function') {
					fail('Invalid Request Key');
				}
			});
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


cfg.factory('sources', ['$sce', 'fetch', 'preloader', 'constants', 'browser', 'features', function($sce, fetch, preloader, constants, browser, features){
	var fetched = {};
	return{
		loading: 0,
		get: function(action, before, whilest, specifics){
			var o = this;
			var svg = false;
			var returndata;
			if(o.loading === 0)
			{
				if(features.detect('svg')){
					svg = 'true';
				}
				else{
					svg = 'false';
				}
				fetch.secureget(constants.canonical+'_data/main.php', {
					'svg' : svg
				}, function(response){
					fetched = response.data;
					
					if(typeof specifics == 'string') {
						if(specifics == 'navigation') {
							returndata = response.data.nav;
						}
						else{
							if(typeof response.data[specifics] != 'undefined') {
								returndata = response.data.contents[specifics];
							}
							else{
								returndata = response.data;
							}
						}
					}
					else{
						returndata = response.data;
					}

					var init = response.data['on_init'];

					preloader.run(init, function(){
						if(typeof before == 'function') {
							before(response.data, function(){
								var pr = response.data.preload;
								var imgar = [];
								for(var i=0; i<pr.length; i++)
								{
									imgar[i] = pr[i];
								}

								preloader.run(imgar, function(){
									action(response.data.contents);
									o.loading = 1;
								}, function(progress){
									if(typeof whilest == 'function'){
										whilest(progress);
									}
								});
							});
						}
						else{
							var pr = response.data.preload;
							var imgar = [];
							for(var i=0; i<pr.length; i++)
							{
								imgar[i] = pr[i];
							}

							preloader.run(imgar, function(){
								action(response.data.contents);
								o.loading = 1;
							}, function(progress){
								if(typeof whilest == 'function'){
									whilest(progress);
								}
							});
						}
					});

					
				}, function(response){
					fetched = false;
					action(false);
				});
			}
			else
			{
				if(typeof specifics == 'string') {
					if(specifics == 'navigation') {
						returndata = fetched.nav;
					}
					else{
						if(typeof fetched[specifics] != 'undefined') {
							returndata = fetched.contents[specifics];
						}
						else{
							returndata = fetched;
						}
					}
				}
				else{
					returndata = fetched;
				}

				if(typeof before == 'function') {
					before(returndata);
				}

				if(typeof action == 'function') {
					action(returndata);
				}
			}
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
