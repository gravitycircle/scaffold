(function(){
	var app = angular.module("main", ['ngRoute', 'htmlcustom', 'configurator', 'siteviews']);

	app.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider){
		$locationProvider.html5Mode({
			enabled: true,
    		requireBase: false
		});

		$routeProvider.when('/', {
			templateUrl: 'shadow/pages/home.html',
			controller: 'homeCtrl'
		}).when('/about', {
			templateUrl: 'shadow/pages/about.html',
			controller: 'aboutCtrl'
		}).when('/how-it-works', {
			templateUrl: 'shadow/pages/faq.html'
		}).when('/login', {
			templateUrl: 'shadow/pages/sign-in.html'
		}).when('/search', {
			templateUrl: 'shadow/pages/search.html'
		}).otherwise({
			templateUrl: 'shadow/pages/404.html'
		});
	}]);

	app.factory('activepage', ['$location', 'constants', function($location, constants){
		return{
			url: function(){
				return $location.path();
			},
			isactive: function(location, classdefaults, currentclass){
				if(typeof location != undefined)
				{
					if(location == constants.canonical)
					{
						location = '';
					}

					

					if('/'+location == $location.path())
					{
						return classdefaults+' '+currentclass;
					}
					return classdefaults;
				}
			}
		}
	}]);

	app.directive('animateView', ['$location', function($location){
		return {
			restrict: 'A',
			link: function(scope, element, attrs){
				scope.$on('$routeChangeStart', function(event, newUrl) {
				 	$(element).addClass("isLoading");
				});
			}
		}
	}]);

	
})();