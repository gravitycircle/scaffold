(function(){
	var app = angular.module("main", ['htmlcustom', 'configurator', 'siteviews', 'modes']);

	app.config(['$locationProvider', function($locationProvider) {
		$locationProvider.html5Mode(true);
	}]);

	app.factory('navigator', ['$location', function($location){
		
	}]);

	app.directive('body', ['$compile', '$window', '$timeout', '$location', 'features', 'sources', 'constants', 'email', 'modal', function($compile, $window, $timeout, $location, features, sources, constants, email, modal){
		return{
			restrict: 'E',
			templateUrl: 'shadow/main.html',
			scope: {
				logo : '@'
			},
			link: function(scope, element, attrs){
				var rotator;
				sources.get(function(){
					//post-load
					
				}, function(){
					//pre-load
					
				});
			},
			controller: function($scope, $element, $attrs){
				//features.run();
			}
		};
	}]);
})();