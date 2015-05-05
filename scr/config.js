cfg.factory('pagelink', 'constants', [function(constants){
	return{
		
	};
}]);

cfg.factory('callback', function($http){
	return {
		url: function(urlresource)
		{
			return $http.get(urlresource);
		}
	}
});

cfg.factory('api', ['constants', function(constants){
	return{
		content:{
		}
	};
}]);
