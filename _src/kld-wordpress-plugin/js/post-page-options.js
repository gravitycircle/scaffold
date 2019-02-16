//janky override for confirm issue
var leg_confirm = confirm;

confirm = function(arg) {
	if(arg == "Are you sure you want to do this?\nThe comment changes you made will be lost.") 
	{return true;}

	return leg_confirm(arg);
};

//---
(function($){
	$(document).ready(function() {
	});
})(jQuery);