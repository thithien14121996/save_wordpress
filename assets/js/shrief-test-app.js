jQuery( document ).ready(function($) {
    wp.customize('shrief_cp_settings', function(control) {
            
        control.bind(function( controlValue ) {
                if (controlValue){
                    console.log(jQuery('#site-header').attr('class'));
                    // jQuery('#site-header').remove();
            jQuery('#site-header').attr('style','background-color:'+controlValue +' !important');           
            }
                
            }
        );
     }); 
 });