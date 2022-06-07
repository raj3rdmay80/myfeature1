require(["jquery","prototype"], function(jQuery){
    window.groomers = function() {
        return {
            gridRowClick : function(grid, event) {
            	var element = Event.findElement(event, 'tr');
            	if(element.title && assignServiceId) {
            		var addGroomerURL = element.title+'service_id/'+assignServiceId;
            		window.location.href = addGroomerURL;
            	}
            },
        }
    }();
});