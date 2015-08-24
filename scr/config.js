cfg.factory('fetch', ['$http','$sce',function($http, $sce){
	return {
		url: function(urlresource)
		{
			return $http.get(urlresource);
		},
		secure: function(urlresource)
		{
			return $http.get($sce.trustAsResourceUrl(urlresource));
		}
	};
}]);

cfg.factory('preloader', ['$http',function($http){
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
				$http.get(imgarray[i]).success(lsuccess).error(lerror);
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

cfg.factory('source', ['fetch', 'preloader', function(){
	
}]);