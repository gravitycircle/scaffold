(function() {
	var mod = angular.module("htmlcustom", []);

	mod.service('browser', ['$window', function($window) {
		return {
			detect: function(){
				var userAgent = $window.navigator.userAgent;

				var browsers = {chrome: /chrome/i, safari: /safari/i, firefox: /firefox/i, ie: /trident/i, ie2: /msie/i};

				for(var key in browsers) {
					if (browsers[key].test(userAgent)) {
						if(key == 'ie' || key == 'ie2')
						{
							return 'internet explorer';
						}
						else
						{
							return key;
						}
					}
				}

				return userAgent;
			},
			scrollbarwidth: function(){
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
		};
	}]);

	mod.factory('features', [function(){
		return {
			run : function(){
				this.feature['2D Transform'] = Modernizr.csstransforms;
				this.feature['3D Transform'] = Modernizr.csstransforms3d;
				this.feature.svg = Modernizr.svg;
				this.feature.touch = Modernizr.touchevents;
				this.feature.video = Modernizr.video;

				if(Modernizr.mq('only all and (max-width: 1024px)') && Modernizr.touchevents)
				{
					this.feature.mobile = true;
				}
				else
				{
					this.feature.mobile = false;
				}
			},
			detect : function(feature) {
				var detection = ['2D Transform', '3D Transform', 'svg', 'touch', 'mobile', 'video'];

				if(detection.indexOf(feature) >= 0)
				{
					return this.feature[feature];
				}
				else
				{
					return false;
				}
			},
			feature : {
			}
		};
	}]);

	mod.factory('modal', [function(){
		return {
			'htmlmodal' : function(html, onload){
				var o = this;
				$('body').append('<div class="modal-shader"><div class="modal-window"><div class="modal-text">'+html+'</div></div></div>');

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
							if(typeof onload == 'function'){
								onload();
							}
						});
					});
				});


			},
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
				var o = this;
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
									o.hidemodal(onclose);
								}
								else{
									o.hidemodal();
								}
							});

							$('.modal-window').on('click', function(e){
								e.stopPropagation();
							});

							$('.modal-control a').on('click', function(e){
								e.preventDefault();
								if(typeof onclose == 'function'){
									o.hidemodal(onclose);
								}
								else{
									o.hidemodal();
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
})();