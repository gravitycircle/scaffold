(function() {
	var mod = angular.module("htmlcustom", ["configurator"]);

	mod.factory('clipboard', ['$rootScope', function($rootScope){
		return {
			set: function(what, value) {
				if(typeof $rootScope['clipboard'] == 'undefined')
				{
					$rootScope['clipboard'] = {};
				}

				$rootScope['clipboard'][what] = value;
			},
			get: function(what) {
				if(typeof $rootScope['clipboard'][what] == 'undefined')
				{
					return false;
				}
				return $rootScope['clipboard'][what];
			}
		}
	}]);

	mod.factory('lightbox', [function(){
		return {
			show: function(html){
				$('body').append('<div class="shader"><div class="lightbox"><a href="#" class="close"><i class="icon-cancel"></i></a>'+html+'</div></div>');

				$('.shader').css('width', '100%');
				$('.shader').css('height', '100%');

				$('.shader').animate({
					'opacity' : 1
				}, 800);

				setTimeout(function(){
					var w = $('.shader .lightbox').width();
					var h = $('.shader .lightbox').height();

					$('.shader .lightbox').css('width', 0);
					$('.shader .lightbox').css('height', 0);

					$('.shader .lightbox').animate({
						'opacity' : 1,
						'width' : w,
						'height' : h
					}, 400, function(){
						$('.shader .lightbox').css('width', '');
						$('.shader .lightbox').css('height', '');
						$('.shader').css('overflow', 'auto');

						//close animation
						$('.lightbox').on('click', function(e){
							e.stopPropagation();
						});
						$('.shader, .shader .lightbox .close').on('click', function(e){
							e.preventDefault();
							$('.shader, .shader .lightbox .close').off('click');
							$('.shader .lightbox').animate({
								'opacity' : 0,
								'width' : 0,
								'height' : 0
							}, 400);

							setTimeout(function(){
								$('.shader').animate({
									'opacity' : 0
								}, 400, function(){
									$('.shader').remove();
								})
							}, 200);
						});
					});
				}, 200);
			}
		}
	}]);

	mod.factory('preload', ['$q', function($q) {
	  return function(url) {
	    var deffered = $q.defer(),
	    image = new Image();

	    image.src = url;

	    if (image.complete) {
	  
	      deffered.resolve();
	  
	    } else {
	  
	      image.addEventListener('load', function() {
	        deffered.resolve();
	      });
	  
	      image.addEventListener('error', function() {
	        deffered.reject();
	      });
	    }

	    return deffered.promise;
	  }
	}]);
	
	mod.factory('scrollbar', [function(){
		return {
			getWidth: function(){
				var inner = document.createElement('p');
				inner.style.width = "100%";
				inner.style.height = "200px";

				var outer = document.createElement('div');
				outer.style.position = "absolute";
				outer.style.top = "0px";
				outer.style.left = "0px";
				outer.style.visibility = "hidden";
				outer.style.width = "200px";
				outer.style.height = "150px";
				outer.style.overflow = "hidden";
				outer.appendChild (inner);

				document.body.appendChild (outer);
				var w1 = inner.offsetWidth;
				outer.style.overflow = 'scroll';
				var w2 = inner.offsetWidth;
				if (w1 == w2) w2 = outer.clientWidth;

				document.body.removeChild (outer);

				return (w1 - w2);
			}
		}
	}]);

	mod.factory('detectDPI', ['$window', function($window){
		return{
			find: function(){
				$('body').append($('<div id="dpi-computer"></div>'));
				var dpi = $('body').find('#dpi-computer').width();
				$('body').find('#dpi-computer').remove();
				return dpi;
			},
			dpiscale: function(){
				$('body').append($('<div id="dpi-computer"></div>'));
				var dpi = Math.floor(($('body').find('#dpi-computer').width() / 96) * 100) / 100;
				$('body').find('#dpi-computer').remove();
				//peg standard @ 96dpi
				if(dpi < 1)
				{
					return 96;
				}
				else
				{
					return dpi;
				}
			}
		};
	}]);

	mod.factory('windowsize', ['$window', function($window){
		return {
			getWindow: angular.element($window),
			getWidth: function(){
				var w = angular.element($window);
				return w.width();
			},
			getHeight: function(){
				var w = angular.element($window);
				return w.height();
			},
			getBreakpoint: function(winWidth){
				if(isNaN(winWidth))
				{
					return 'xs';
				}
				else
				{
					if(winWidth < 768)
					{
						return 'xs';
					}
					else if(winWidth >= 768 && winWidth < 992)
					{
						return 'sm';
					}
					else if(winWidth >= 992 && winWidth < 1200)
					{
						return 'md';
					}
					else if(winWidth >= 1200 && winWidth < 2000)
					{
						return 'lg';
					}
					else
					{
						return 'hd';
					}
				}
			}
		};
	}]);

	mod.factory('imgscaler', ['constants', 'windowsize', 'detectDPI', function(constants, windowsize, detectDPI){
		return {
			smartscale: function(w, s){
				w = w * detectDPI.dpiscale();
				return constants.canonical+'php/image/processor.php?w='+w+'&h=&q=100&src='+s;
			},
			cssscale: function(w, h, s){
				w = w * detectDPI.dpiscale();
				h = h * detectDPI.dpiscale();
				return constants.canonical+'php/image/processor.php?w='+w+'&h='+h+'&q=100&src='+s;
			},
			imageResize: function(w, q, s, scale, f){
				if(typeof scale !== "undefined")
				{
					if(scale)
					{
						w = w * detectDPI.dpiscale();
					}
				}

				if(typeof f !== "undefined")
				{
					return constants.canonical+'php/image/processor.php?w='+w+'&h=&q='+q+'&src='+s+'&f='+f;
				}
				else
				{
					return constants.canonical+'php/image/processor.php?w='+w+'&h=&q='+q+'&src='+s;
				}
			},
			imageResizeH: function(h, q, s, f){
				h = h * detectDPI.dpiscale();
				if(typeof f !== "undefined")
				{
					return constants.canonical+'php/image/processor.php?w=&h='+h+'&q='+q+'&src='+s+'&f='+f;
				}
				else
				{
					return constants.canonical+'php/image/processor.php?w=&h='+h+'&q='+q+'&src='+s;
				}
			},
			imageCrop: function(w, h, q, s, scale, f){
				if(typeof scale !== "undefined")
				{
					if(scale)
					{
						w = w * detectDPI.dpiscale();
						h = h * detectDPI.dpiscale();
					}
				}

				if(typeof f !== "undefined")
				{
					return constants.canonical+'php/image/processor.php?w='+w+'&h='+h+'&q='+q+'&src='+s+'&f='+f;
				}
				else
				{
					return constants.canonical+'php/image/processor.php?w='+w+'&h='+h+'&q='+q+'&src='+s;
				}
			}
		};
	}]);

	mod.directive('vector', [function(){
		return {
			restrict: 'E',
			templateUrl: 'shadow/modified-elements/svg.html',
			scope: {
				svg: '@',
				src: '@',
				imgsrc: '@'
			},
			replace: true,
			link: function postLink(scope, element, attrs) {
				element.bind('error', function(){
					attrs.$set('src', attrs.img);		
				});
			}
		}
	}]);

	mod.directive('smartScale', ['windowsize', 'imgscaler', function(windowsize, imgscaler){
		return {
			restrict: 'C',
			templateUrl: 'shadow/modified-elements/img.html',
			scope: {
				src: '@',
				imgsrc: '@',
				output: '@'
			},
			replace: true,
			link: function(scope, element, attrs){
				var statsrc = scope.src;
				//assign defaults + fallbacks
				//pass in attributes
				scope.$watch(function(){
					return {
						fi: imgscaler.smartscale(element.width(), statsrc)
					};
				}, function(newVal, oldVal){
					scope.output = newVal.fi;
				}, true);

				windowsize.getWindow.bind('resize', function(){
					scope.$apply();
				});
			}
		}
	}]);

	mod.directive('cssCrop', ['imgscaler', 'windowsize', function(imgscaler, windowsize){
		return{
			restrict: 'A',
			templateUrl: 'shadow/modified-elements/img.html',
			scope: {
				output: '@',
				src: '@'
			},
			replace: true,
			link: function(scope, element, attrs)
			{
				var statsrc = scope.src;

				scope.$watch(function(){
					return {
						fi: imgscaler.cssscale(element.width(), element.height(), statsrc)
					};
				}, function(newVal, oldVal){
					scope.output = newVal.fi;
				}, true);

				windowsize.getWindow.bind('resize', function(){
					scope.$apply();
				});
			}
		};
	}]);

	mod.directive('responsive', ['windowsize', 'imgscaler', function(windowsize, imgscaler){
		return {
			restrict: 'A',
			templateUrl: 'shadow/modified-elements/img.html',
			scope: {
				srcXS: '@',
				srcSM: '@',
				srcMD: '@',
				srcLG: '@',
				imgsrc: '@',
				output: '@',
			},
			replace: true,
			link: function(scope, element, attrs){
				scope.imgsrc = attrs.src !== undefined ? attrs.src : '';
				scope.srcLG = attrs.lg !== undefined ? attrs.lg : scope.imgsrc;
				scope.srcMD = attrs.md !== undefined ? attrs.md : scope.srcLG;
				scope.srcSM = attrs.sm !== undefined ? attrs.sm : scope.srcMD;
				scope.srcXS = attrs.xs !== undefined ? attrs.xs : scope.srcSM;

				scope.$watch(function(){
					return {
						wi: windowsize.getBreakpoint(windowsize.getWidth())
					};
				}, function(newVal, oldVal){
					var bp = newVal.wi;

					if(bp == 'xs')
					{
						scope.output = scope.srcXS;
					}
					else if(bp == 'sm')
					{
						scope.output = scope.srcSM;
					}
					else if(bp == 'md')
					{
						scope.output = scope.srcMD;
					}
					else if(bp == 'lg')
					{
						scope.output = scope.srcLG;
					}
					else
					{
						scope.output = scope.imgsrc;
					}

				}, true);

				windowsize.getWindow.bind('resize', function(){
					scope.$apply();
				});
			}
		}
	}]);
})();