(function(){
	var app = angular.module("main", ['htmlcustom', 'configurator', 'siteviews', 'modes', 'ngMap', 'communicator']);

	app.config(['$locationProvider', function($locationProvider) {
		$locationProvider.html5Mode(true);
	}]);

	app.factory('navigator', ['$location', function($location){
		
	}]);

	app.directive('body', ['$compile', '$window', '$timeout', '$location', 'features', 'sources', 'constants', 'email', 'modal', 'fetch', function($compile, $window, $timeout, $location, features, sources, constants, email, modal, fetch){
		return{
			restrict: 'E',
			templateUrl: 'shadow/main.html',
			scope: {
				data : '@'
			},
			link: function(scope, element, attrs){
				sources.get(function(content){
					//post-load
					
				}, function(content){
					//pre-load
					scope.initiate(content.contents);
				}, function(progress){
					//while
				});

			},
			controller: function($scope, $element, $attrs){
				$scope.initiate = function(content){
					$scope.data = content;
					$scope.fields = $scope.data.test.fields;
				};
			}
		};
	}]);
})();
