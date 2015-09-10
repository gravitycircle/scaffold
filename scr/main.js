(function(){
	var app = angular.module("main", ['htmlcustom', 'configurator', 'siteviews']);

	app.config(['$locationProvider', function($locationProvider) {
		$locationProvider.html5Mode(true);
	}]);

	app.factory('activepage', ['$location', 'constants', function($location, constants){
		return{
			url: function(){
				return $location.path();
			},
			isactive: function(location, currentclasses, activeclass){
				if(typeof location !== undefined)
				{
					if(location == constants.canonical)
					{
						location = '';
					}

					if('/'+location == $location.path())
					{
						return currentclasses+' '+activeclass;
					}
					return currentclasses;
				}
				else
				{
					return false;
				}
			}
		};
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
				features.run();
			}
		};
	}]);
})();