(function(){
	var views = angular.module("siteviews", []);

	views.directive('vTeaser', ['$sce', '$compile', 'sources', 'constants', 'pathmgr', 'browser', function($sce, $compile, sources, constants, pathmgr, browser){
		return {
			'restrict' : 'E',
			'template' : constants.templates.views.teaser,
			'replace' : true,
			'scope' : {
				'teaser' : '@'
			},
			'link' : function(scope, element, attrs) {
				setTimeout(function(){
					scope.done();
				}, 10);
			},
			'controller' : function($scope, $element, $attrs){
				$scope.done = function(){
					// console.log($scope.teaser);
					if($scope.$root.$$phase != '$apply' && $scope.$root.$$phase != '$digest') {
 						$scope.$apply();
 					}
				};



				$scope.s = function(html) {
					return $sce.trustAsHtml(browser.nl2br(html));
				}

				$scope.sr = function(html) {
					return $sce.trustAsHtml(html);
				}
			}
		}
	}]);
})();