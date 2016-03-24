(function(){
	var mode = angular.module("modes", []);

	mode.factory('modal', [function(){
		return {
			'hidemodal' : function(afterclose){
				if($('.modal-shader').length > 0){
					$('.modal-window').transition({
						'opacity' : 0
					}, 300, function(){
						$('.modal-shader').transition({
							'width' : 0,
							'height' : 0,
							'opacity' : 0
						}, 500, function(){
							$('.modal-shader').off('click');
							$('.modal-window').off('click');
							$('.modal-control a').off('click');

							$('.modal-shader').remove();

							if(typeof afterclose == 'function'){
								afterclose();
							}
						});
					});
				}
			},
			'showmodal' : function(heading, message, buttontext, onload, onclose){
				$('body').append('<div class="modal-shader"><div class="modal-window"><div class="modal-text"><h2>'+heading+'</h2>'+message+'</div><div class="modal-control"><a href="#" class="action">'+buttontext+'</a></div></div></div>');

				$('.modal-shader').transition({
					'opacity' : 1,
					'width' : '100%',
					'height' : '100%'
				}, 500, function(){
					$('.modal-shader .modal-window').transition({
						'scale' : [1,1],
						'x' : '-50%',
						'y' : '-50%',
						'opacity' : 1
					}, 300, function(){
						$('.modal-text, .modal-control').transition({
							'opacity' : 1
						}, 300, function(){
							$('.modal-shader').on('click', function(){
								if(typeof onclose == 'function'){
									$scope.hidemodal(onclose);
								}
								else{
									$scope.hidemodal();
								}
							});

							$('.modal-window').on('click', function(e){
								e.stopPropagation();
							});

							$('.modal-control a').on('click', function(e){
								e.preventDefault();
								if(typeof onclose == 'function'){
									$scope.hidemodal(onclose);
								}
								else{
									$scope.hidemodal();
								}
							});
							if(typeof onload == 'function'){
								onload();
							}
						});
					});
				});
			}
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