(function(){
	var app = angular.module("main", ['htmlcustom', 'configurator', 'siteviews']);

	app.config(['$locationProvider', function($locationProvider) {
		$locationProvider.html5Mode(true);
	}]);

	app.factory('seo', [function(){
		return {
			meta : function(what) {
				if(what == 'title')
				{
					return this.title;
				}
				else if (what == 'description')
				{
					return this.description;
				}
			},
			send: function(what, value) {
				var check = ['home'];
				
				if(check.indexOf(value) >= 0)
				{
					if(what == 'title')
					{
						this.title = this.metas.title[value];
					}
					else if (what == 'description')
					{
						this.description = this.metas.description[value];
					}
				}
			},
			title : 'Loading...',
			description : '',
			metas : {
				title : {
					'home' : 'AngularJS - Website Structure Scaffolding'
				},
				description : {
					'home' : 'A set of files grouped together for bootstrapping an AngularJS based website.'
				}
			}
		};
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

	app.factory('navigate', [function(){
		return {

		};
	}]);

	app.directive('animateView', ['$location', function($location){
		return {
			restrict: 'A',
			link: function(scope, element, attrs){
				scope.$on('$routeChangeStart', function(event, newUrl) {
					$(element).addClass("isLoading");
				});
			}
		};
	}]);

	app.directive('title', ['$location', 'seo', function($location, seo){
		return {
			restrict: 'E',
			controller: function($scope, $element, $attrs){
				$scope.$watch(function(){
					return $location.path();
				}, function(location){
					if(location == '/')
					{
						location = 'home';
					}

					seo.send('title', location.replace('/', ''));
					$element.text(seo.meta('title'));
				});
			}
		};
	}]);

	app.directive('body', ['$compile', '$window', '$location', 'navigate', function($compile, $window, $location, navigate){
		return{
			restrict: 'E',
			scope: {

			},
			link: function(scope, element, attrs){

			},
			controller: function($scope, $element, $attrs){
				$scope.$watch(function(){
					return $location.path();
				}, function(location){
					if(location == '/')
					{
						location = 'home';
					}

					//onlocation change

				});
			}
		};
	}]);
})();