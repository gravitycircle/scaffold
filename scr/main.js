(function(){
	var app = angular.module("main", ['htmlcustom', 'configurator', 'siteviews']);

	app.config(['$locationProvider', function($locationProvider) {
		$locationProvider.html5Mode(true);
	}]);

	app.factory('activepage', ['$location', 'constants', function($location, constants){
		return{
			url: function(){
				return $location.path();
			},
			isactive: function(location, currentclasses, activeclass){
				if(typeof location !== undefined)
				{
					if(location == constants.canonical)
					{
						location = '';
					}

					if('/'+location == $location.path())
					{
						return currentclasses+' '+activeclass;
					}
					return currentclasses;
				}
				else
				{
					return false;
				}
			}
		};
	}]);

	app.directive('body', ['$compile', '$window', '$timeout', '$location', 'features', 'sources', 'constants', 'email', 'modal', function($compile, $window, $timeout, $location, features, sources, constants, email, modal){
		return{
			restrict: 'E',
			templateUrl: 'shadow/main.html',
			scope: {
				logo : '@'
			},
			link: function(scope, element, attrs){
				var rotator;
				sources.get(function(){
					//post-load
					setTimeout(function(){
						if(typeof(rotator) !== 'undefined')
						{
							clearTimeout(rotator);
							setTimeout(function(){
								$('body').removeClass('isLoading');

								setTimeout(function(){
									$('.handler-spinner').transition({
										'top' : 40,
										'margin-top' : 0
									}, 400, function(){
										$('body').removeClass('contentLoading');
										$('.pleasewait').remove();
									});
								}, 1000);
							}, 450);
						}
					}, 3000);
				}, function(){
					//pre-load
					$('#wrapper').scrollTop(0);
					rotator = setInterval(function(){
						$('.loading-box').transition({
							'rotate' : '90deg'
						}, 450, function(){
							$('.loading-box').css({
								'rotate' : '0deg'
							});
						});
					}, 950);
				});
			},
			controller: function($scope, $element, $attrs){
				features.run();

				$scope.submitform = function(){
					//serialize
					var prevtext = $('#submit a').html();
					$('#submit a').html('Please Wait...');
					var x = $($element).find('.form-set');
					var details = [];
					for(var i = 0; i<x.length; i++)
					{
						if($(x[i]).find('input, textarea').length)
						{
							details[details.length] = {
								'label' : $(x[i]).find('label').html(),
								'key' : $(x[i]).attr('data-id'),
								'value' : $(x[i]).find('input, textarea').val(),
								'require' : $(x[i]).attr('require'),
								'filter' : encodeURIComponent($(x[i]).find('input, textarea').attr('data-filter'))
							};
						}
					}
					email.verify(details, ['label','key', 'value', 'require', 'filter'], 'require', function(){
					
					var tableform = email.tabulate(details, ['label', 'value'], ['Field', 'Entry']);

					if(!tableform)
					{
						tableform = '';
					}

					var fname;
					var lname;
					var emailad;
					for(i=0; i<details.length; i++)
					{
						if(details[i].key == 'first-name')
						{
							fname = details[i].value;
						}

						if(details[i].key == 'last-name')
						{
							lname = details[i].value;
						}

						if(details[i].key == 'email-address')
						{
							emailad = details[i].value;
						}
					}

					

					var mailobject = email.compose('The Stanton Registration Form|no.reply@thestanton.ca', fname+' '+lname+'|'+emailad, 'The Stanton|info@thestanton.ca', 'Teaser Site Registration', '<h1 style="border-bottom: 1px solid #000; margin-bottom: 40px; padding-bottom: 10px; float: left;">Registration Entry [date]:</h1><p style="clear: both;">A registration entry has been received on [date], with details as follows:</p>'+tableform+'<p>To reply to the sender of this entry, simply reply to this email and your message will be routed to the viewer.</p>', [], []);

					email.sendmail(mailobject, function(response){
						if(response[0] == 'sent')
						{
							modal.open('<div class="option-chooser"><div class="option-container errmsg"><a class="symbol-close"></a><p><strong>Your registration entry has been sent.</strong></p><p>Thank you for registering with us. Your entry is now currently being reviewed and we will reply to you through the email address you provided shortly.</p></div></div>', function(){
								//events here.
								$('.option-container').on('click', function(e){
									e.stopPropagation();
								});

								$('.shader a').on('click', function(){
									$('.shader').click();
								});

								$('.shader').on('click', function(){
									$('input').val('');
									$('.display').html('');
									$('.selected').html('---');
									$('textarea').val('');
									$('.areatxt').html('');
									$('.invalid').removeClass('.invalid');
									$('.invalid').off('click');
								});

								//send to client email

							}, function(){
								//garbage collection
								$('.shader a').off('click');
								$('.option-container').off('click');
							});
						}
						$('#submit a').html(prevtext);
					}, function(response){
						$('#submit a').html(prevtext);
					});

					}, function(errors){
						var form_entries = $($element).find('.form-set');
						var empty = [];
						var invalid = [];
						for(var inc = 0; inc < form_entries.length; inc++)
						{
							if(errors.empty.indexOf($(form_entries[inc]).attr('data-id')) >= 0)
							{
								$(form_entries[inc]).addClass('invalid');
								empty[empty.length] = '<li>'+$(form_entries[inc]).find('label').html()+'</li>';
							}
							
							if(errors.invalid.indexOf($(form_entries[inc]).attr('data-id')) >= 0 && !$(form_entries[inc]).hasClass('invalid'))
							{
								$(form_entries[inc]).addClass('invalid');
								invalid[invalid.length] = '<li>'+$(form_entries[inc]).find('label').html()+'</li>';
							}
						}

						$('.invalid').off('click');
						$('.invalid').one('click', function(){
							$(this).removeClass('invalid');
						});

						var message = '<p><strong>There was a problem regarding your current submission:</strong></p>';

						if(empty.length)
						{
							message = message + '<p>Please fill up all fields marked with an asterisk(*):</p><ul>'+empty.join("")+'</ul>';
						}

						if(invalid.length)
						{
							message = message + '<p>Please enter a valid email address for:</p><ul>'+invalid.join("")+'</ul>';
						}

						$('#submit a').html(prevtext);

						modal.open('<div class="option-chooser"><div class="option-container errmsg"><a class="symbol-close"></a>'+message+'<p>Fields that have to be modified are all marked in red.</p></div></div>', function(){
							//events here.
							$('.option-container').on('click', function(e){
								e.stopPropagation();
							});

							$('.shader a').on('click', function(){
								$('.shader').click();
							});

						}, function(){
							//garbage collection
							$('.shader a').off('click');
							$('.option-container').off('click');
						});
					});
				};

				$scope.logo = {
					'png' : constants.canonical+'img/logo.png',
					'svg' : constants.canonical+'img/logo.svg'
				};

				$scope.$watch(function(){
					return $location.path();
				}, function(location){
					var classes = $('body').attr('class').split(' ');

					for(var i=0; i<classes.length; i++)
					{
						if(classes[i].indexOf('-page') >= 0)
						{
							$('body').removeClass(classes[i]);
						}
					}

					if(location !== '/')
					{
						$('body').addClass(location.replace('/','')+'-page');
					}
				});
			}
		};
	}]);
})();