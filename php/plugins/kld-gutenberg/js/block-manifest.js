var pristine = true;
var scrollActions = (function($){
	var triggers = [];
	var element = '';
	var check = function(scrollID) {
		if(triggers.length) {
			for(var i in triggers) {
				if(triggers[i].id == scrollID) {
					return i;
				}
			}
		}
		return -1;
	}
	return {
		trigger: function() {
			var sc = $(element).scrollTop();
			var ps = {
				'height' : parseFloat($(element).css('height')),
				'offset' : $(element).offset()
			};
			if(triggers.length > 0) {
				for(var i in triggers) {
					triggers[i].act(sc, ps);
				}
			}
		},
		initialize: function(e) {
			element = e;
			
			$(element).on('scroll', function(){
				var sc = $(element).scrollTop();
				var ps = {
					'height' : parseFloat($(element).css('height')),
					'offset' : $(element).offset()
				};
				if(triggers.length > 0) {
					for(var i in triggers) {
						triggers[i].act(sc, ps);
					}
				}
			});

			$(window).on('resize', function(){
				var sc = $(element).scrollTop();
				var ps = {
					'height' : parseFloat($(element).css('height')),
					'offset' : $(element).offset()
				};
				if(triggers.length > 0) {
					for(var i in triggers) {
						triggers[i].act(sc, ps);
					}
				}
			});
		},
		add: function(scrollID, callback) {
			var target = check(scrollID);

			if(target > -1) {
				triggers[target].act = function(response, position) {
					if($('#'+scrollID).length) {
						callback(response, position);
					}
				};
			}
			else{
				triggers.push({
					'id' : scrollID,
					'act' : function(response, position) {
						if($('#'+scrollID).length) {
							callback(response, position);
						}
					}
				});
			}
		},
		remove: function(scrollID) {
			var target = check(scrollID);

			if (target > -1) {
				triggers.splice(target, 1);
				return true;
			}

			return false;
		}
	};
})(jQuery);

var scrollActions = (function($){
	var triggers = [];
	var element = '';
	var check = function(scrollID) {
		if(triggers.length) {
			for(var i in triggers) {
				if(triggers[i].id == scrollID) {
					return i;
				}
			}
		}
		return -1;
	}
	return {
		trigger: function() {
			var sc = $(element).scrollTop();
			var ps = {
				'height' : parseFloat($(element).css('height')),
				'offset' : $(element).offset()
			};
			if(triggers.length > 0) {
				for(var i in triggers) {
					triggers[i].act(sc, ps);
				}
			}
		},
		initialize: function(e) {
			element = e;
			
			$(element).on('scroll', function(){
				var sc = $(element).scrollTop();
				var ps = {
					'height' : parseFloat($(element).css('height')),
					'offset' : $(element).offset()
				};
				if(triggers.length > 0) {
					for(var i in triggers) {
						triggers[i].act(sc, ps);
					}
				}
			});

			$(window).on('resize', function(){
				var sc = $(element).scrollTop();
				var ps = {
					'height' : parseFloat($(element).css('height')),
					'offset' : $(element).offset()
				};
				if(triggers.length > 0) {
					for(var i in triggers) {
						triggers[i].act(sc, ps);
					}
				}
			});
		},
		add: function(scrollID, callback) {
			var target = check(scrollID);

			if(target > -1) {
				triggers[target].act = function(response, position) {
					if($('#'+scrollID).length) {
						callback(response, position);
					}
				};
			}
			else{
				triggers.push({
					'id' : scrollID,
					'act' : function(response, position) {
						if($('#'+scrollID).length) {
							callback(response, position);
						}
					}
				});
			}
		},
		remove: function(scrollID) {
			var target = check(scrollID);

			if (target > -1) {
				triggers.splice(target, 1);
				return true;
			}

			return false;
		}
	};
})(jQuery);



(function($){
	$(window).load(function(){
		console.log('initialize trigger');
		scrollActions.initialize('.interface-interface-skeleton__content');
		pristine = false;
	});
})(jQuery);