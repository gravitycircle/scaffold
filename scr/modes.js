(function(){
	//differing frameworks
	var mode = angular.module("modes", []);

	mode.factory('submission', [function(){
		return{
			data : {},
			required: [],
			set : function(name, value, require, type){
				if(type != 'submit') {
					var d = this;
					var r = false;
					if(require){
						r = true;
					}

					if(r && value === ''){
						d.data[name] = false;
					}else{
						d.data[name] = value;
					}
				}
			},
			register : function(name, require, type){
				if(type != 'submit') {
					var d = this;
					var r = false;
					
					if(require){
						r = true;
					}

					if(r){
						d.data[name] = false;
						d.required.push(name);
					}else{
						d.data[name] = '';
					}
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
					$scope.field = $scope.$parent[$attrs.target][$attrs.index];
					submission.register($scope.field.id, $scope.field.require, $scope.field.type);
					var compose;
					if($scope.field.type == 'text' || $scope.field.type == 'email'){
						compose = '<text-field class="rendering"></text-field>';
					}
					else if($scope.field.type == 'email'){
						compose = '<email-field class="rendering"></email-field>';
					}
					else if($scope.field.type == 'dropdown'){
						compose = '<dropdown-field class="rendering"></dropdown-field>';
					}
					else if($scope.field.type == 'checkbox'){
						compose = '<checkbox-field class="rendering"></checkbox-field>';
					}
					else if($scope.field.type == 'paragraph'){
						compose = '<paragraph-field class="rendering"></paragraph-field>';
					}
					else if($scope.field.type == 'submit'){
						compose = '<submit-field class="rendering"></submit-field>';
					}

					$($element).append(compose);

					g = $scope.$new();

					$compile($($element).find('.rendering'))(g);
				};
			}
		};
	}]);

	mode.directive('checkboxField', ['submission', function(submission){
		return{
			restrict: 'E',
			templateUrl: 'shadow/fields/checkbox.html',
			replace: true,
			scope: {
				render: '@',
				mrk: '@'
			},
			link: function(scope, element, attrs) {

			},
			controller: function($scope, $element, $attrs) {
				$scope.render = $scope.$parent.field;
				$scope.render.idname = $scope.render.id.replace('[', '-').replace(']', '');
				if($scope.$parent.field.require){
					$scope.mrk = '*';
				}
				else{
					$scope.mrk = '';
				}

				$scope.toggle = function(){
					$('.field-handler').removeClass('red');
					if(!$($element).find('label').hasClass('check')){
						$($element).find('label').addClass('check');
						submission.set($scope.render.id, $scope.$parent.field.value, $scope.render.require, $scope.render.type);
					}
					else{
						$($element).find('label').removeClass('check');
						if($scope.$parent.require) {
							submission.set($scope.render.id, false, $scope.render.require, $scope.render.type);
						}
						else {
							submission.set($scope.render.id, '', $scope.render.require, $scope.render.type);
						}
					}
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
				$scope.render.idname = $scope.render.id.replace('[', '-').replace(']', '');
				if($scope.$parent.field.require){
					$scope.mrk = '*';
				}
				else{
					$scope.mrk = '';
				}

				$scope.typing = function(){
					$('.field-handler').removeClass('red');
					submission.set($scope.render.id, $($element).find('input').val(), $scope.render.require, $scope.render.type);
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

	mode.directive('emailField', ['submission', function(submission){
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
				$scope.render.idname = $scope.render.id.replace('[', '-').replace(']', '');
				if($scope.$parent.field.require){
					$scope.mrk = '*';
				}
				else{
					$scope.mrk = '';
				}

				$scope.typing = function(){
					$('.field-handler').removeClass('red');
					submission.set($scope.render.id, $($element).find('input').val(), $scope.render.require, $scope.render.type);
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

	mode.directive('paragraphField', ['submission', function(submission){
		return{
			restrict: 'E',
			templateUrl: 'shadow/fields/paragraph.html',
			scope: {
				render: '@',
				mrk: '@'
			},
			replace: true,
			controller: function($scope, $element, $attrs) {
				$scope.render = $scope.$parent.field;
				$scope.render.idname = $scope.render.id.replace('[', '-').replace(']', '');

				if($scope.$parent.field.require){
					$scope.mrk = '*';
				}
				else{
					$scope.mrk = '';
				}

				$scope.typing = function(){
					$('.field-handler').removeClass('red');
					submission.set($scope.render.id, $($element).find('input').val(), $scope.render.require, $scope.render.type);

					$($element).find('textarea').scrollTop(0);

					if($($element).find('.input-error').length > 0){
						$($element).find('.input-error').removeClass('input-error');
					}

					if($('body').find('.input-error').length < 1){
						$('body').find('.error-msg').removeClass('shown');
					}
				};

				$scope.focus = function(isFocused) {
					if(typeof isFocused != 'boolean') {
						isFocus = false;
					}

					if(!isFocused) {
						$($element).find('.paragraph-sizer').removeClass('focus');
					}
					else{
						$($element).find('.paragraph-sizer').addClass('focus');
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
				$scope.render.idname = $scope.render.id.replace('[', '-').replace(']', '');
				if($scope.$parent.field.require){
					$scope.mrk = '*';
				}
				else{
					$scope.mrk = '';
				}
				var open;
				$scope.toggle = function(){

					$('.options-container').css({
						'height' : 0
					});

					$('.cell').removeClass('opened');

					if($($element).find('.options-container').height() > 2){
						open = true;
					}
					else{
						open = false;
					}


					var multiplier = $($element).find('.options').height();
					if(!open){
						var newheight = 0;
						var opts = {};

						if($($element).find('.options').length > 5) {
							opts = {
								'height' : (5 * multiplier)+'px'
							};
						}
						else{
							opts = {
								'height' : (($($element).find('.options').length * multiplier) + 2)+'px'
							};
						}

						$($element).find('.cell').addClass('opened');
						$($element).find('.options-container').css(opts);
						
					}
					else{
						$($element).find('.cell').removeClass('opened');
						$($element).find('.options-container').css({
							'height' : 0
						});
					}
				};

				$scope.choose = function(choice, html){
					$('.field-handler').removeClass('red');
					$($element).find('.selected').html(html);
					submission.set($scope.render.id, encodeURIComponent(choice), $scope.render.require, $scope.render.type);

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
	
	mode.directive('submitField', ['submission', 'email', 'lasso', function(submission, email, lasso){
		return{
			restrict: 'E',
			replace: true,
			templateUrl: 'shadow/fields/submit.html',
			scope: {
				render: '@'
			},
			controller: function($scope, $element, $attrs){
				//do submission here
				$scope.render = $scope.$parent.field;

				$scope.mail = function(){};

				$scope.curlLasso = function(){
					lasso.verify(submission.data, function(){

					}, function(){

					}, function(){

					});
				};

				$scope.exec = function(how){
					if(typeof how == 'undefined'){
						how = 'mail';
					}

					if(how == 'mail'){
						$scope.mail();
					}

					else if (how == 'lasso') {
						$scope.curlLasso();
					}
				};
			}
		};
	}]);
})();