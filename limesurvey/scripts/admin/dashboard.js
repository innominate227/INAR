$(document).ready(function(){
    if(showTextInline==1) {
        /* Enable all the browse divs, and fill with data */
        $('.statisticsbrowsebutton').each( function (){
            if (!$(this).hasClass('numericalbrowse')) {
                loadBrowse(this.id,'');
            }
        });
    }
	
     function loadBrowse(id,extra) {
         var destinationdiv=$('#columnlist_'+id);
         if(extra=='') {
             destinationdiv.parents("td:first").toggle();
         } else {
             destinationdiv.parents("td:first").show();
         }
         if(destinationdiv.parents("td:first").css("display") != "none") {
             $.post(listColumnUrl+'/'+id+'/'+extra, function(data) {
                 destinationdiv.html(data);
             });
         }
     }
	 
	 
    $('#hidereports').click( function(){
        $('#reports').hide(1000);
    });
    $('#showreports').click( function(){
        $('#reports').show(1000);
    });
	
    $('#hidesurveys').click( function(){
        $('#surveys').hide(1000);
    });
    $('#showsurveys').click( function(){
        $('#surveys').show(1000);
    });
});
