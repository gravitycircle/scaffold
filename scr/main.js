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
				features.run();
				sources.get(function(content){
					//post-load
					setTimeout(function(){
						scope.done();
					}, 600);
				}, function(content, event_trigger){
					//pre-load
					scope.initiate(content,event_trigger);
				}, function(progress){
					//while
				});

			},
			controller: function($scope, $element, $attrs){
				$scope.pagescope = $scope.$new();
				$scope.resizeActions = [];
				$scope.scrollActions = [];
				$scope.initiate = function(content, continueEvent){
					$scope.data = content.contents;
					if($scope.$root.$$phase != '$apply' && $scope.$root.$$phase != '$digest') {
 						$scope.$apply();
 					}
 					
 					//renderd
					continueEvent();
				};


				$scope.done = function() {
					$('#loading').transition({
						'opacity' : 0,
						'y' : '-40px',
						'x' : '-50%'
					}, 600, function(){
						$('#loading').remove();
					});

					$('#content').transition({
						'opacity' : 1
					}, 600, function(){
						$('#content').css({
							'pointer-events' : 'auto'
						});
					});
				};

				$scope.actions = {
					'resize' : {
						'queue' : function(id, action){
							//check
							if($scope.resizeActions.length) {
								for(var i in $scope.resizeActions) {
									if($scope.resizeActions[i].id == id) {
										$scope.resizeActions[i] = {
											'id' : id,
											'action' : action
										};
										return true;
									}
								}
								$scope.resizeActions.push({
									'id' : id,
									'action' : action
								});
								return false;
							}
							else{
								$scope.resizeActions.push({
									'id' : id,
									'action' : action
								});
								return false;
							}
						},
						'remove' : function(id){
							if($scope.resizeActions.length) {
								var sp = -1;
								for(var i in $scope.resizeActions) {
									if($scope.resizeActions[i].id == id) {
										sp = i;
										break;
									}
								}

								if(sp == -1) {
									return false;
								}
								else{
									array.splice(sp, 1, $scope.resizeActions);
									return true;
								}
							}
							return false;
						}
					},
					'scroll' : {
						'queue' : function(id, action){
							//check
							if($scope.scrollActions.length) {
								for(var i in $scope.scrollActions) {
									if($scope.scrollActions[i].id == id) {
										$scope.scrollActions[i] = {
											'id' : id,
											'action' : action
										};
										return true;
									}
								}
								$scope.scrollActions.push({
									'id' : id,
									'action' : action
								});
								return false;
							}
							else{
								$scope.scrollActions.push({
									'id' : id,
									'action' : action
								});
								return false;
							}
						},
						'remove' : function(id){
							if($scope.scrollActions.length) {
								var sp = -1;
								for(var i in $scope.scrollActions) {
									if($scope.scrollActions[i].id == id) {
										sp = i;
										break;
									}
								}

								if(sp == -1) {
									return false;
								}
								else{
									array.splice(sp, 1, $scope.scrollActions);
									return true;
								}
							}
							return false;
						}
					}
				};

				$scope.s = function(html) {
					return $sce.trustAsHtml(html);
				}
			}
		};
	}]);
})();
