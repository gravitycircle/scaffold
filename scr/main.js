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

	app.directive('body', ['$compile', '$window', '$timeout', '$location', 'features', 'sources', 'constants', 'email', 'modal', 'fetch', 'pathmgr', function($compile, $window, $timeout, $location, features, sources, constants, email, modal, fetch, pathmgr){
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
				$scope.openM = function(){
					modal.dialogue('Test Dialogue', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laudantium ullam cum, recusandae sapiente aut et pariatur excepturi nam ipsum modi eligendi, libero perferendis, soluta molestias sunt saepe debitis labore ut.', [
						{
							'text' : 'Other Test',
							'class' : 'unique-identifier',
							'other-classes' : ['button'],
							'events' : {
								'click' : function(e, closeWindow){
									e.preventDefault();
									closeWindow();
								}
							}
						}
					]);
				};

				$scope.initiate = function(content){
					$scope.data = content;
					$scope.fields = $scope.data.test.fields;
				};

			}
		};
	}]);
})();
