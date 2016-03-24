(function(){
	var mode = angular.module("modes", []);

	mode.factory('modal', [function(){
		return {
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
									$scope.hidemodal(onclose);
								}
								else{
									$scope.hidemodal();
								}
							});

							$('.modal-window').on('click', function(e){
								e.stopPropagation();
							});

							$('.modal-control a').on('click', function(e){
								e.preventDefault();
								if(typeof onclose == 'function'){
									$scope.hidemodal(onclose);
								}
								else{
									$scope.hidemodal();
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