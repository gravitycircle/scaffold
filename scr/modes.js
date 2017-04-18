(function(){
	//differing frameworks
	var mode = angular.module("modes", []);

	mode.factory('submission', ['constants', 'fetch',function(constants, fetch){
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
			},
			verify: function(data, pass, fail, error) {
				fetch.post(constants.base+'php/verify.php', {
					'verify' : 1
				}, data, function(response){
					if(response.data.length < 1) {
						if(typeof pass == 'function') {
							pass();
						}
					}
					else{
						if(typeof fail == 'function') {
							fail(response.data);
						}
					}

				}, function(response){
					if(typeof error == 'function') {
						error(response);
					}
				});
			}
		};
	}]);

	mode.directive('field', ['$compile', 'submission', 'constants', function($compile, submission, constants){
		return{
			restrict: 'E',
			template: constants.templates['modified-elements'].field,
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

	mode.directive('checkboxField', ['$sce', 'submission', 'constants', function($sce, submission, constants){
		return{
			restrict: 'E',
			template: constants.templates.fields.checkbox,
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
					$($element).find('label').removeClass('input-error');
					if(!$($element).hasClass('check')){
						$($element).addClass('check');
						submission.set($scope.render.id, $scope.$parent.field.value, $scope.render.require, $scope.render.type);
					}
					else{
						$($element).removeClass('check');
						if($scope.$parent.require) {
							submission.set($scope.render.id, false, $scope.render.require, $scope.render.type);
						}
						else {
							submission.set($scope.render.id, '', $scope.render.require, $scope.render.type);
						}
					}
				};

				$scope.sanitize = function(html) {
					return $sce.trustAsHtml(html);
				};
			}
		};
	}]);

	mode.directive('textField', ['$sce', 'submission', 'constants', function($sce, submission, constants){
		return {
			restrict: 'E',
			template: constants.templates.fields.text,
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

				$scope.sanitize = function(html) {
					return $sce.trustAsHtml(html);
				};
			}
		};
	}]);

	mode.directive('emailField', ['$sce', 'submission', 'constants', function($sce, submission, constants){
		return {
			restrict: 'E',
			template: constants.templates.fields.text,
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

				$scope.sanitize = function(html) {
					return $sce.trustAsHtml(html);
				};
			}
		};
	}]);

	mode.directive('paragraphField', ['$sce', 'submission', 'constants', function($sce, submission, constants){
		return {
			restrict: 'E',
			template: constants.templates.fields.paragraph,
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
					submission.set($scope.render.id, $($element).find('textarea').val(), $scope.render.require, $scope.render.type);

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

				$scope.sanitize = function(html) {
					return $sce.trustAsHtml(html);
				};
			}
		};
	}]);

	mode.directive('dropdownField', ['$sce', 'submission', 'constants', function($sce, submission, constants){
		return {
			restrict: 'E',
			template: constants.templates.fields.dropdown,
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

				$scope.sanitize = function(html) {
					return $sce.trustAsHtml(html);
				};
			}
		};
	}]);
	
	mode.directive('submitField', ['$sce', 'submission', 'deliver', 'crm', 'constants', 'modal', function($sce, submission, deliver, crm, constants, modal){
		return{
			restrict: 'E',
			replace: true,
			template: constants.templates.fields.submit,
			scope: {
				render: '@'
			},
			controller: function($scope, $element, $attrs){
				//do submission here
				$scope.render = $scope.$parent.field;

				$scope.exec = function(){
					submission.verify(submission.data, function(){
						//passed
					}, function(failed){
						for(var i in failed) {
							$('#'+failed[i]).find('label').addClass('input-error');
						}
						modal.dialogue('Submission Failed', 'Unfortunately, your submission was not completed due to some missing or incorrect information. Please fill in all fields that are marked with an asterisk (*) correctly. The fields that need editing are highlighted in red.');
					}, function(resp){

					});
				};

				$scope.sanitize = function(html) {
					return $sce.trustAsHtml(html);
				};
			}
		};
	}]);
})();