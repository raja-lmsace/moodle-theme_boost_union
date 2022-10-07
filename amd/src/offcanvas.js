define(['jquery', 'theme_boost/drawers', 'core/modal'], function($, Drawers, Modal) {

	let modalBackdrop = null;

	const getDrawerBackdrop = function() {
		if (!modalBackdrop) {
			 modalBackdrop = Modal.prototype.getBackdrop().then(backdrop => {
	            backdrop.getAttachmentPoint().get(0).addEventListener('click', e => {
	                e.preventDefault();
	                console.log(e);
	                var currentDrawer = Drawers.getDrawerInstanceForNode(
	                	document.getElementById('theme_boost-drawers-offcanvas')
	                );
	                currentDrawer.closeDrawer(false);
	                backdrop.hide();
	            });
	            return backdrop;
	        })
        	.catch();
		}
        return modalBackdrop;
	};

	function initOffCanvasEventListeners() {
		document.addEventListener(Drawers.eventTypes.drawerShown, function(e) {
			console.log(e.target);
	        if (e.target.id != 'theme_boost-drawers-offcanvas') {
	        	return null;
	        }
	        getDrawerBackdrop().then(backdrop => {
	            backdrop.show();
	            const pageWrapper = document.getElementById('page');
	            pageWrapper.style.overflow = 'hidden';
	            return backdrop;
	        })
	        .catch();
	    });

	    document.addEventListener(Drawers.eventTypes.drawerHide, function(e) {
	    	getDrawerBackdrop().then(backdrop => {
	            backdrop.hide();
	            const pageWrapper = document.getElementById('page');
	            pageWrapper.style.overflow = 'auto';
	        })
	        .catch();
	    });
	}

	return {
		init: function() {
			initOffCanvasEventListeners();
		}
	};
 	
})