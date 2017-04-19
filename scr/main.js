(function(){
	var app = angular.module("main", ['htmlcustom', 'configurator', 'siteviews', 'modes', 'ngMap', 'communicator']);

	app.config(['$locationProvider', function($locationProvider) {
		$locationProvider.html5Mode(true);
	}]);

	app.factory('pathmgr', ['$location', function($location){
		return {
			locate : function(path){
				path = path.replace('/debug', '');

				if(path == '/' || path === ''){
					return 'home';
				}
				else{
					path = path.replace('/', '');
					return path;
				}
			}
		};
	}]);

	app.directive('body', ['$compile', '$window', '$timeout', '$location', '$sce', 'features', 'sources', 'constants', 'modal', 'fetch', 'pathmgr', function($compile, $window, $timeout, $location, $sce, features, sources, constants, modal, fetch, pathmgr){
		return{
			restrict: 'E',
			template: constants.templates.shadow.main,
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
				};

			}
		};
	}]);
})();
