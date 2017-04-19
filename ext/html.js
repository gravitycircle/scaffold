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
		var template = '<div class="shader cncl"><div class="sizer"><div class="modal-window%CLASS%"><a href="#" class="form-close close cncl"></a><div class="modal-title">%TITLE%</div><div class="modal-text">%HTML%</div><div class="modal-footer">%FOOT%</div></div></div></div>';
		
		var closeModal = function(callback){
			$('.shader').css({
				'pointer-events' : 'none'
			});

			$('.shader').removeClass('act');
			$('.modal-window').transition({
				'opacity' : 0,
				'x' : 0,
				'y' : -60
			}, 600, function(){
				$('.shader').transition({
					'opacity' : 1,
					'width' : 0,
					'height' : 0
				}, 300, function(){
					$('.shader').remove();
					if(typeof callback == 'function') {
						callback();
					}
				});
			});
		};

		var eventActions = function(cls, evt, fn, create){
			if(create) {
				$('.'+cls).on(evt, function(ev){
					fn(ev, closeModal);
				});
			}
			else{
				$('.'+cls).off(evt);
			}
		};

		return {
			dialogue: function(title, msg, cta, onload, afterclose) {
				//cta:
				/*
				{
					{
						'text' : 'Link Text',
						'class' : 'unique-identifier',
						'other-classes' : ['other-classes-in-array'],
						'events' : {
							'click' : function(e){
	
							},
							'hover' : function(e){
								
							}
						}
					},
					{
						'text' : 'Link Text',
						'class' : 'unique-identifier',
						'other-classes' : ['other-classes-in-array'],
						'events' : {
							'click' : function(e){
	
							},
							'hover' : function(e){
								
							}
						}
					}
				}
	
				*/

				var t = template+'';
				t = t.replace('%TITLE%', title);
				t = t.replace('%CLASS%', ' modal-dialogue');
				t = t.replace('%HTML%', '<p>'+msg+'</p>');
				var actions = {};
				var buttons = [];
				if(typeof cta == 'object' && cta !== null) {
					for(var i in cta) {
						buttons.push('<a href="#" class="'+cta[i]['class']+' '+cta[i]['other-classes'].join(' ')+'">'+cta[i].text+'</a>');
						if(typeof actions[cta[i]['class']] != 'undefined') {
							console.error('Modal Error: Cannot launch modal, duplicates found.');
							return false;
						}
						else{
							actions[cta[i]['class']] = cta[i].events;
						}
					}
					t = t.replace('%FOOT%', buttons.join(''));
				}
				else{
					t = t.replace('%FOOT%', '<a href="#" class="button cncl">Close</a>');
				}


				$('body').append(t);

				$('.shader').transition({
					'opacity' : 1,
					'width' : '100%',
					'height' : '100%'
				}, 600, function(){
					$('.modal-window').transition({
						'opacity' : 1,
						'x' : 0,
						'y' : 0
					}, 600, function(){
						//events
						if(typeof onload == 'function') {
							onload();
						}

						$('.shader').addClass('act');
						$('.cncl').on('click', function(e){
							e.preventDefault();
							closeModal(afterclose);
							for(var cls in actions){
								//eventActions(cls, evt, fn, create)
								for(var evt in actions[cls]) {
									eventActions(cls, evt, actions[cls][evt], false);
								}
							}
						});

						for(var cls in actions){
							//eventActions(cls, evt, fn, create)
							for(var evt in actions[cls]) {
								eventActions(cls, evt, actions[cls][evt], true);
							}
						}

						$('.modal-window').on('click', function(e){
							e.stopPropagation();
						});
					});
				});
			}
		};
	}]);
})();