(function(){
	var app = angular.module("main", ['htmlcustom', 'configurator', 'siteviews', 'modes', 'ngMap']);

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
				sources.get(function(){
					//post-load
					
				}, function(){
					//pre-load
					
				});
			},
			controller: function($scope, $element, $attrs){
				$scope.testfields =[
					{
						'label' : 'Text field',
						'type' : 'text',
						'id' : 'test-text',
						'require' : true
					},
					{
						'label' : 'Dropdown field',
						'type' : 'dropdown',
						'id' : 'test-drop',
						'require' : true,
						'values' : [
							{
								'value' : 'test',
								'label' : 'Test Value 1'
							},
							{
								'value' : 'test2',
								'label' : 'Test Value 2'
							}
						]
					},
					{
						'label' : 'Comments',
						'type' : 'paragraph',
						'id' : 'comments',
						'require' : false
					},
					{
						'label' : 'Submit',
						'type': 'submit',
						'id' : 'submit',
						'require' : false
					}
				];
			}
		};
	}]);
})();
