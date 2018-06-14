(function(){
	var app = angular.module("main", ['htmlcustom', 'configurator', 'siteviews', 'modes', 'ngMap', 'communicator']);

	app.config(['$locationProvider', function($locationProvider) {
		$locationProvider.html5Mode(true);
	}]);

	app.factory('pathmgr', ['$location', 'sources', 'constants', function($location, sources, constants){
		var nav = [];
		var checker = [];
		var reference = {};
		var parents = [];
		var locate = function(path){
			path = path.replace('/debug', '');

			if(path == '/' || path === ''){
				return 'home';
			}
			else{
				path = path.replace('/', '').replace(/\/+$/, '').split('/')[0];
				if(checker.indexOf(path) >= 0) {
					return path;
				}
				else{
					return 'lost';
				}
			}
		};

		return {
			getNav : function(){
				return nav;
			},
			go : function(where, cb) {
				console.log(where);
				if(where == 'home') {
					where = '';
				}

				$location.path('/'+where);
				if(typeof cb == 'function') {
					cb();
				}
			},
			decipher: function() {
				return reference[locate($location.path())];
			},
			detect: function(check) {
				if(typeof check != 'string') {
					return locate($location.path());
				}
				else{
					return locate(check);
				}
			},
			subnav: function() {
				var sbnv = $location.path().replace('/'+locate($location.path()), '').replace('/', '');

				if(sbnv === '') {
					return false;
				}

				return sbnv;
			},
			initialize : function(navLinks){
				reference.home = navLinks[0].directive;
				var homepage = navLinks.shift();
				checker.push('home');
				for(var x in navLinks) {
					if(navLinks[x].directive != 'property-gallery'){
						nav.push({
							'name' : navLinks[x].name,
							'path' : navLinks[x].path
						});
					}
					checker.push(navLinks[x].path);
					reference[navLinks[x].path] = navLinks[x].directive;
				}
			}
		};
	}]);

	app.directive('body', ['$compile', '$window', '$timeout', '$location', '$sce', 'features', 'sources', 'constants', 'modal', 'fetch', 'pathmgr', 'browser', 'seo', function($compile, $window, $timeout, $location, $sce, features, sources, constants, modal, fetch, pathmgr, browser, seo){
		return{
			restrict: 'E',
			template: constants.templates.shadow.main,
			scope: {
				data : '@'
			},
			link: function(scope, element, attrs){
				sources.get(function(content){
					//post-load
					
				}, function(content, event_trigger){
					//pre-load
					scope.initiate(content.contents,event_trigger);
				}, function(progress){
					//while
				});

			},
			controller: function($scope, $element, $attrs){
				$scope.initiate = function(content, continueEvent){
					$scope.data = content;
					$scope.fields = content.home.content.fields;
					continueEvent();
				};

			}
		};
	}]);
})();
