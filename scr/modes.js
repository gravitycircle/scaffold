(function(){
	var mode = angular.module("modes", []);

	mode.factory('modal', [function(){
		return {

		};
	}]);

	mode.factory('submission', [function(){
		return{
			data : {},
			required: [],
			set : function(name, value, filter, type){
				var d = this;
				var r = false;
				if(type == 'dropdown'){
					r = true;
				}
				else
				{
					if(filter[0] == 'require'){
						r = true;
					}
				}

				if(r && value === ''){
					d.data[name] = false;
				}else{
					d.data[name] = value;
				}
			},
			register : function(name, filter, type){
				var d = this;
				var r = false;
				if(type == 'dropdown'){
					r = true;
				}
				else
				{
					if(filter[0] == 'require'){
						r = true;
					}
				}

				if(r){
					d.data[name] = false;
					d.required.push(name);
				}else{
					d.data[name] = '';
				}
			},
			reset : function(){
				var d = this;
				for(var i in d.data){

					if(d.required.indexOf(i) >= 0){
						d.data[i] = false;
					}
					else{
						d.data[i] = '';
					}
				}
			}
		};
	}]);

	mode.directive('field', ['$compile', 'submission', function($compile, submission){
		return{
			restrict: 'E',
			templateUrl: 'shadow/modified-elements/field.html',
			replace: true,
			scope : {
				field : '@'
			},
			link: function(scope, element, attrs){
				setTimeout(function(){
					scope.initialize();
				}, 1);
			},
			controller: function($scope, $element, $attrs){
				$scope.$on('$destroy', function(){
					if(g !== null){
						g.$destroy();
					}
				});
				var g = null;
				$scope.initialize = function(){
					$scope.field = $scope.$parent.regform[$attrs.side][$attrs.index];
					submission.register($scope.field.id, $scope.field.filter, $scope.field.type);
					var compose;
					if($scope.field.type == 'text' || $scope.field.type == 'email'){
						compose = '<text-field class="rendering"></text-field>';
					}
					else if($scope.field.type == 'dropdown'){
						compose = '<dropdown-field class="rendering"></dropdown-field>';
					}
					else if($scope.field.type == 'bool'){
						compose = '<bool-field class="rendering"></bool-field>';
					}
					else if($scope.field.type == 'check'){
						compose = '<check-field class="rendering"></check-field>';
					}

					$($element).append(compose);

					g = $scope.$new();

					$compile($($element).find('.rendering'))(g);
				};
			}
		};
	}]);

	mode.directive('textField', ['submission', function(submission){
		return {
			restrict: 'E',
			templateUrl: 'shadow/fields/text.html',
			replace: true,
			scope : {
				render: '@',
				mrk: '@'
			},
			link: function(scope, element, attrs){

			},
			controller: function($scope, $element, $attrs){
				$scope.render = $scope.$parent.field;

				if($scope.$parent.field.filter[0] == 'require'){
					$scope.mrk = '*';
				}
				else{
					$scope.mrk = '';
				}

				$scope.typing = function(){
					submission.set($scope.render.id, $($element).find('input').val(), $scope.render.filter, $scope.render.type);
					if($($element).find('.input-error').length > 0){
						$($element).find('.input-error').removeClass('input-error');
					}

					if($('body').find('.input-error').length < 1){
						$('body').find('.error-msg').removeClass('shown');
					}
				};
			}
		};
	}]);

	mode.directive('checkField', ['submission', function(submission){
		return{
			restrict: 'E',
			templateUrl: 'shadow/fields/check.html',
			scope: {
				render: '@',
				mrk: '@'
			},
			replace: true,
			link: function(scope, element, attrs){

			},
			controller: function($scope, $element, $attrs){
				var checked = false;
				$scope.render = $scope.$parent.field;
				if($scope.$parent.field.filter[0] == 'require'){
					$scope.mrk = '*';
				}
				else{
					$scope.mrk = '';
				}

				$scope.toggle = function(){
					if(checked){
						submission.set($scope.render.id, false, $scope.render.filter, $scope.render.type);
						checked = false;
						$($element).find('.ticker').removeClass('icon-checked');
						$($element).find('.ticker').addClass('icon-unchecked');
					}
					else{
						submission.set($scope.render.id, 'YES', $scope.render.filter, $scope.render.type);
						checked = true;
						$($element).find('.ticker').addClass('icon-checked');
						$($element).find('.ticker').removeClass('icon-unchecked');
					}

					if($($element).find('.input-error').length > 0){
						$($element).find('.input-error').removeClass('input-error');
					}

					if($('body').find('.input-error').length < 1){
						$('body').find('.error-msg').removeClass('shown');
					}
				};
			}
		};
	}]);

	mode.directive('boolField', ['submission', function(submission){
		return{
			restrict: 'E',
			templateUrl: 'shadow/fields/bool.html',
			scope: {
				render: '@',
				mrk: '@'
			},
			replace: true,
			link: function(scope, element, attrs){

			},
			controller: function($scope, $element, $attrs){
				var checked = false;
				$scope.render = $scope.$parent.field;
				if($scope.$parent.field.filter[0] == 'require'){
					$scope.mrk = '*';
				}
				else{
					$scope.mrk = '';
				}

				$scope.toggle = function(yn){
					submission.set($scope.render.id, yn.toUpperCase(), $scope.render.filter, $scope.render.type);
					$($element).find('.ticker').addClass('icon-unchecked');
					$($element).find('.ticker').removeClass('icon-checked');

					$($element).find('.answer-'+yn).removeClass('icon-unchecked');
					$($element).find('.answer-'+yn).addClass('icon-checked');

					if($($element).find('.input-error').length > 0){
						$($element).find('.input-error').removeClass('input-error');
					}

					if($('body').find('.input-error').length < 1){
						$('body').find('.error-msg').removeClass('shown');
					}
				};
			}
		};
	}]);

	mode.directive('dropdownField', ['submission', function(submission){
		return{
			restrict: 'E',
			templateUrl: 'shadow/fields/dropdown.html',
			scope:{

			},
			replace: true,
			link: function(scope, element, attrs){

			},
			controller: function($scope, $element, $attrs){
				$scope.render = $scope.$parent.field;
				var open;
				$scope.toggle = function(){
					$('.options-container').css({
						'height' : 0
					});

					if($($element).find('.options-container').height() > 1){
						open = true;
					}
					else{
						open = false;
					}

					var multiplier = $('.options').height();
					if(!open){
						$($element).find('.cell').addClass('opened');
						$($element).find('.options-container').css({
							'height' : ($($element).find('.options').length * multiplier)
						});
						
					}
					else{
						$($element).find('.cell').removeClass('opened');
						$($element).find('.options-container').css({
							'height' : 0
						});
					}
				};

				$scope.choose = function(choice){
					$($element).find('.selected').html(choice);
					submission.set($scope.render.id, encodeURIComponent(choice), $scope.render.filter, $scope.render.type);

					if($($element).find('.input-error').length > 0){
						$($element).find('.input-error').removeClass('input-error');
					}

					if($('body').find('.input-error').length < 1){
						$('body').find('.error-msg').removeClass('shown');
					}
				};
			}
		};
	}]);
})();