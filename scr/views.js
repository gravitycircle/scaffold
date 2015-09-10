(function(){
	var views = angular.module("siteviews", []);

	views.factory('modal', ['$window', function($window){
		return{
			open : function(html, actions, cleanup){
				$('body').append('<div class="shader">'+html+'</div>');

				setTimeout(function(){
					$('body').find('.shader').addClass('open');
					if(typeof actions == 'function'){
						actions();


						$('.shader').one('click', function(){
							$('.shader').removeClass('open');

							setTimeout(function(){
								if(typeof cleanup == 'function'){
									cleanup();
								}
								$('.shader').remove();
							}, 800);
						});
					}
				}, 100);
			},
			close : function(cleanup){
				if($('.shader')){
					$('.shader').removeClass('open');

					setTimeout(function(){
						if(typeof cleanup == 'function'){
							cleanup();
						}
						$('.shader').remove();
					}, 800);
				}
			}
		};
	}]);
	
	views.directive('textField', ['features', 'modal', function(features, modal){
		return {
			restrict: 'E',
			replace: true,
			templateUrl: 'shadow/desktop/text-field.html',
			scope: {
				id: '@',
				label: '@',
				required: '@',
				classnames: '@'
			},
			link: function(scope, element, attrs) {
				if(typeof attrs.require != 'undefined' && attrs.require == 'yes')
				{
					scope.required = '*';
					scope.classnames = 'text-input required';
				}
				else
				{
					scope.required = '';
					scope.classnames = 'text-input';
				}

				if(features.detect('mobile'))
				{
					$(element).find('input').attr('type', 'hidden');
					$(element).find('.input-sizer').append('<div class="display text-input"></div>');
				}
			},
			controller: function($scope, $element, $attrs) {
				$scope.invoke = function(){
					if(features.detect('mobile'))
					{
						modal.open('<div class="textboxer"><div class="input-holder"><div class="confirm">f</div><input type="text" class="input-mode" value="'+$($element).find('.display').html()+'"/></div></div>', function(){
							//events here.
							$('.input-mode').focus();

							$('.textboxer').on('click', function(e){
								e.stopPropagation();
							});

							$('.confirm').on('click', function(e){
								//confirm actions
								$('#'+$scope.id).val($('.input-mode').val());
								$($element).find('.display').html($('.input-mode').val());
								$('.shader').click();
							});

							$('.input-mode').on('keypress', function(e){
								if(e.which == 13) {
									//confirm actions
									$('.confirm').click();
								}
							});
						}, function(){
							//garbage collection
							$('.textboxer').off('click');
							$('.confirm').off('click');
							$('.input-mode').off('keypress');
						});
					}
				};
			}
		};
	}]);

	views.directive('areaField', ['$window', 'features', 'modal', function($window, features, modal){
		return {
			restrict: 'E',
			replace: true,
			templateUrl: 'shadow/desktop/area-field.html',
			scope: {
				id: '@',
				label: '@',
				required: '@',
				classnames: '@',
				height: '@',
				rules: '@'
			},
			link: function(scope, element, attrs) {
				scope.height = attrs.rows * 6;

				scope.rules = [];

				for(var x = 0; x<attrs.rows; x++)
				{
					scope.rules[x] = x;
				}

				if(typeof attrs.require != 'undefined' && attrs.require == 'yes')
				{
					scope.required = '*';
					scope.classnames = 'text-input required';
				}
				else
				{
					scope.required = '';
					scope.classnames = 'text-input';
				}

				if(features.detect('mobile'))
				{
					$(element).find('textarea').remove();
					$(element).find('.input-sizer').append('<input type="hidden" id="'+scope.id+'" class="'+scope.classnames+'" name="'+scope.id+'" data-filter="none" /><div class="areatxt '+scope.classnames+'" style="height: '+scope.height+'rem"></div>');
				}
			},
			controller: function($scope, $element, $attrs) {
				$scope.invoke = function(){
					if(features.detect('mobile')){

						modal.open('<div class="option-chooser"><div class="option-container"><textarea class="input-mode" style="height: '+parseInt($($window).height()/4, 10)+'px;">'+$('.areatxt').html()+'</textarea><a class="save">Save</a></div></div>', function(){
							//events here.
							$('.input-mode').focus();
							$('.option-container').on('click', function(e){
								e.stopPropagation();
							});

							$('.save').on('click', function(){
								$($element).find('.areatxt').html($('.input-mode').val());
								$('#'+$scope.id).val($('.input-mode').val());
								$('.shader').click();
							});
						}, function(){
							//garbage collection
							$('.option-container').off('click');
						});
					}
				};
			}
		};
	}]);

	views.directive('emailField', ['features', 'modal', function(features, modal){
		return {
			restrict: 'E',
			replace: true,
			templateUrl: 'shadow/desktop/email-field.html',
			scope: {
				id: '@',
				label: '@',
				required: '@',
				classnames: '@'
			},
			link: function(scope, element, attrs) {
				if(typeof attrs.require != 'undefined' && attrs.require == 'yes')
				{
					scope.required = '*';
					scope.classnames = 'text-input required';
				}
				else
				{
					scope.required = '';
					scope.classnames = 'text-input';
				}

				if(features.detect('mobile'))
				{
					$(element).find('input').attr('type', 'hidden');
					$(element).find('.input-sizer').append('<div class="display text-input"></div>');
				}
			},
			controller: function($scope, $element, $attrs) {
				$scope.invoke = function(){
					if(features.detect('mobile'))
					{
						modal.open('<div class="textboxer"><div class="input-holder"><div class="confirm">f</div><input type="email" class="input-mode" value="'+$($element).find('.display').html()+'"/></div></div>', function(){
							//events here.
							$('.input-mode').focus();

							$('.textboxer').on('click', function(e){
								e.stopPropagation();
							});

							$('.confirm').on('click', function(e){
								//confirm actions
								$('#'+$scope.id).val($('.input-mode').val());
								$($element).find('.display').html($('.input-mode').val());
								$('.shader').click();
							});

							$('.input-mode').on('keypress', function(e){
								if(e.which == 13) {
									//confirm actions
									$('.confirm').click();
								}
							});
						}, function(){
							//garbage collection
							$('.textboxer').off('click');
							$('.confirm').off('click');
							$('.input-mode').off('keypress');
						});
					}
				};
			}
		};
	}]);

	views.directive('dropdownField', ['$timeout', 'features', 'modal', function($timeout, features, modal){
		return {
			restrict: 'E',
			replace: true,
			templateUrl: 'shadow/desktop/dropdown-field.html',
			scope: {
				id: '@',
				label: '@',
				required: '@',
				classnames: '@',
				options: '@'
			},
			link: function(scope, element, attrs) {
				if(typeof attrs.require != 'undefined' && attrs.require == 'yes')
				{
					scope.required = '*';
					scope.classnames = 'dd-field text-input required';
				}
				else
				{
					scope.required = '';
					scope.classnames = 'dd-field text-input';
				}

				$timeout(function(){
					$(element).find('.input-sizer').on('click', function(){
						if(!features.detect('mobile')){
							var opened = ($(this).find('.option-selectors').children().length)*3;
							var closed = 3;

							if($(this).find('.option-selectors').hasClass('opened'))
							{
								$(this).find('.option-selectors').css({
									'height' : '',
									'border-color' : '',
									'background' : '',
									'z-index' : ''
								});

								$(this).find('.option-selectors').removeClass('opened');
							}
							else
							{
								$(this).find('.option-selectors').css({
									'height' : opened+'rem',
									'border-color' : '#ccc',
									'background' : '#fff',
									'z-index' : 5
								});
								$(this).find('.option-selectors').addClass('opened');
							}
						}
					});
				});
			},
			controller: function($scope, $element, $attrs) {
				$scope.options = $attrs.values.split('|');

				$scope.invoke = function(){
					if(features.detect('mobile'))
					{
						var choices = '';
						for(var i=0; i<$scope.options.length; i++)
						{
							choices = choices + '<div class="choice">'+$scope.options[i]+'</div>';
						}
						modal.open('<div class="option-chooser"><div class="option-container">'+choices+'</div></div>', function(){
							//events here.
							$('.option-container').on('click', function(e){
								e.stopPropagation();
							});

							$('.choice').on('click', function(e){
								$($element).find('.selected').html($(this).html());
								$('#'+$scope.id).val($(this).html());

								$('.shader').click();
							});
						}, function(){
							//garbage collection
							$('.choice').off('click');
							$('.option-container').off('click');
						});
					}
				};

				$scope.setvalue = function(val){
					if(val != '---')
					{
						$($element).find('#'+$scope.id).val(val);
					}
					else
					{
						$($element).find('#'+$scope.id).val('');
					}

					$($element).find('.selected').html(val);
				};
			}
		};
	}]);
})();