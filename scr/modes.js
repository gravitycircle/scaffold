(function(){
	//differing frameworks
	var mode = angular.module("modes", []);

	mode.factory('submission', ['constants', 'fetch',function(constants, fetch){
		var resetTrigger = {};
		var closeBoxes = {};
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


					if(type == 'dropdown' || type == 'multiple') {
						if(type == 'multiple') {
							resetTrigger[name] = false;
						}

						closeBoxes[name] = false;
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

				for(var g in resetTrigger) {
					resetTrigger[g] = true;
				}
			},
			close_dropdown : function(){
				for(var g in closeBoxes) {
					closeBoxes[g] = true;
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
			},
			for_reset: function(name) {

				if(!resetTrigger[name]) {
					return resetTrigger[name];
				}
				else{
					resetTrigger[name] = false;
					return true;
				}
			},
			dropdown_close: function(name) {
				if(!closeBoxes[name]) {
					return closeBoxes[name];
				}
				else{
					closeBoxes[name] = false;
					return true;
				}
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
					else if($scope.field.type == 'multiple'){
						compose = '<multiple-field class="rendering"></multiple-field>';
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
				var inc;
				$scope.$on('destroy', function(){
					clearInterval($scope.timer);
				});
				$scope.timer = null;
				
				$scope.render = $scope.$parent.field;
				$scope.render.idname = $scope.render.id.replace('[', '-').replace(']', '');
				if($scope.$parent.field.require){
					$scope.mrk = '*';
				}
				else{
					$scope.mrk = '';
				}

				$scope.timer = setInterval(function(){
					if(submission.dropdown_close($scope.render.id)) {
						$scope.toggle(true);
					}
				}, 10);
				var open;
				$scope.toggle = function(open){
					$('.options-container').css({
						'height' : 0
					});

					$('.cell').removeClass('opened');

					if(typeof open != 'boolean') {
						if($($element).find('.options-container').height() > 2){
							open = true;
						}
						else{
							open = false;
						}	
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

	mode.directive('multipleField', ['$sce', 'submission', 'constants', function($sce, submission, constants){
		return {
			restrict: 'E',
			template: constants.templates.fields.multiple,
			scope:{

			},
			replace: true,
			link: function(scope, element, attrs){

			},
			controller: function($scope, $element, $attrs){
				var inc;
				$scope.$on('destroy', function(){
					clearInterval($scope.timer);
				});
				$scope.timer = null;
				$scope.render = $scope.$parent.field;
				$scope.render.idname = $scope.render.id.replace('[', '-').replace(']', '');

				$scope.answers = {};

				for(inc in $scope.render.values) {
					$scope.answers[$scope.render.values[inc].value] = false;
				}



				if($scope.$parent.field.require){
					$scope.mrk = '*';
				}
				else{
					$scope.mrk = '';
				}


				$scope.timer = setInterval(function(){
					if(submission.for_reset($scope.render.id)) {
						$scope.toggle(true);
						for(inc in $scope.render.values) {
							$scope.answers[$scope.render.values[inc].value] = false;
						}

						if($scope.$root.$$phase != '$apply' && $scope.$root.$$phase != '$digest') {
	 						$scope.$apply();
	 					}
					}

					if(submission.dropdown_close($scope.render.id)) {
						$scope.toggle(true);
					}
				}, 10);

				var open;
				$scope.toggle = function(open){
					$('.options-container').css({
						'height' : 0
					});

					$('.cell').removeClass('opened');

					if(typeof open != 'boolean') {
						if($($element).find('.options-container').height() > 2){
							open = true;
						}
						else{
							open = false;
						}	
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

				$scope.choose = function(e, choice, html){
					e.stopPropagation();
					$('.field-handler').removeClass('red');
					
					//submission.set($scope.render.id, encodeURIComponent(choice), $scope.render.require, $scope.render.type);

					


					// form-radio-unchecked
					// form-radio-checked
					// var test = $scope.answer.indexOf(choice);
					// if(test < 0) {
					// 	$('#'+choice).find('.selector').removeClass('form-radio-unchecked');
					// 	$('#'+choice).find('.selector').addClass('form-radio-checked');	
					// }

					if(!$scope.answers[choice]) {
						$('#'+choice).find('.selector').removeClass('form-radio-unchecked');
						$('#'+choice).find('.selector').addClass('form-radio-checked');

						$scope.answers[choice] = true;
					}
					else{
						$('#'+choice).find('.selector').removeClass('form-radio-checked');
						$('#'+choice).find('.selector').addClass('form-radio-unchecked');

						$scope.answers[choice] = false;
					}

					var ready_for_submission = [];

					for(var i in $scope.answers) {
						if($scope.answers[i]) {
							ready_for_submission.push(i);
						}
					}

					if(ready_for_submission.length < 1) {
						ready_for_submission = false;
					}

					submission.set($scope.render.id, encodeURIComponent(JSON.stringify(ready_for_submission)), $scope.render.require, $scope.render.type);

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

				$scope.sanitize_number = function(chk) {
					var arr = [];
					
					for(var d in chk) {
						if(chk[d]) {
							arr.push('1');
						}
					}

					return $sce.trustAsHtml(arr.length + ' ' + (arr.length == 1 ? $scope.render.particle.singular : $scope.render.particle.plural));
				};
			}
		};
	}]);
	
	mode.directive('submitField', ['$sce', 'submission', 'deliver', 'crm', 'constants', 'modal', 'browser', function($sce, submission, deliver, crm, constants, modal, browser){
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

				$scope.exec = function(forMail){
					submission.verify(submission.data, function(){
						//passed
						$($element).find('a').text('Please Wait');
						$($element).find('a').css({
							'pointer-events' : 'none'
						});

						if(forMail) {
							var smtpData = constants.retrieve('smtp');
							deliver.email(['Registration Data', smtpData.user], ['Registration Data', smtpData.user], $scope.render.receiver, 'Registration Successful', submission.data, $scope.render.defaults.empty, $scope.render.defaults.disclaimer, function(response){
								if(response.success) {
									modal.dialogue($scope.render.prompts.success.title, $scope.render.prompts.success.message, false, function(){
										submission.reset();
										$('input').val('');
										$('textarea').val('');
										$('.sing-selected').text('');
										$('.check').removeClass('check');
										$('.mult-selected').addClass('form-radio-unchecked');
										$('.mult-selected').removeClass('form-radio-checked');
										$($element).find('a').text($scope.render.label);
										$($element).find('a').css({
											'pointer-events' : ''
										});
									});
								}
								else{
									modal.dialogue($scope.render.prompts.submit_error.title, $scope.render.prompts.submit_error.message, false, function(){
										submission.reset();
										$('input').val('');
										$('textarea').val('');
										$('.sing-selected').text('');
										$('.check').removeClass('check');
										$('.mult-selected').addClass('form-radio-unchecked');
										$('.mult-selected').removeClass('form-radio-checked');
										browser.debug.err(response.debug);
										$($element).find('a').text($scope.render.label);
										$($element).find('a').css({
											'pointer-events' : ''
										});
									});
									
								}

							}, function(){
								modal.dialogue($scope.render.prompts.submit_error.title, $scope.render.prompts.submit_error.message, false, function(){
									submission.reset();
									$('input').val('');
									$('textarea').val('');
									$('.sing-selected').text('');
									$('.check').removeClass('check');
									$('.mult-selected').addClass('form-radio-unchecked');
									$('.mult-selected').removeClass('form-radio-checked');
									browser.debug.err('Connectivity: Cannot reach server.');
									$($element).find('a').text($scope.render.label);
									$($element).find('a').css({
										'pointer-events' : ''
									});
								});
							});
						}
						else{
							deliver.crm('test', submission.data, function(response){
								// console.log(response);
								if(response.success) {
									modal.dialogue($scope.render.prompts.success.title, $scope.render.prompts.success.message, false, function(){
										submission.reset();
										$('input').val('');
										$('textarea').val('');
										$('.sing-selected').text('');
										$('.check').removeClass('check');
										$('.mult-selected').addClass('form-radio-unchecked');
										$('.mult-selected').removeClass('form-radio-checked');
										$($element).find('a').text($scope.render.label);
										$($element).find('a').css({
											'pointer-events' : ''
										});
									});
								}
								else{
									modal.dialogue($scope.render.prompts.submit_error.title, $scope.render.prompts.submit_error.message, false, function(){
										submission.reset();
										$('input').val('');
										$('textarea').val('');
										$('.sing-selected').text('');
										$('.check').removeClass('check');
										$('.mult-selected').addClass('form-radio-unchecked');
										$('.mult-selected').removeClass('form-radio-checked');
										//browser.debug.err(response.debug);
										$($element).find('a').text($scope.render.label);
										$($element).find('a').css({
											'pointer-events' : ''
										});
									});
									
								}
							}, function(){
								//fail
								modal.dialogue($scope.render.prompts.submit_error.title, $scope.render.prompts.submit_error.message, false, function(){
									submission.reset();
									$('input').val('');
									$('textarea').val('');
									$('.sing-selected').text('');
									$('.check').removeClass('check');
									$('.mult-selected').addClass('form-radio-unchecked');
									$('.mult-selected').removeClass('form-radio-checked');
									//browser.debug.err('Connectivity: Cannot reach server.');
									$($element).find('a').text($scope.render.label);
									$($element).find('a').css({
										'pointer-events' : ''
									});
								});
							}, 1);
						}

					}, function(failed){
						for(var i in failed) {
							$('#'+failed[i].id).find('label').addClass('input-error');
						}
						modal.dialogue($scope.render.prompts.verify_error.title, $scope.render.prompts.verify_error.message);
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