jQuery(document).ready(function(){
    
    jQuery('#test').click(function(d){
        d.preventDefault();

        jQuery.ajax({
            type:'POST',
            url:locsData.admin_url + '/admin-ajax.php?action=add_ajax_script',  
            cache:false,
            data:{
                jsFile:'main.js'
            },
            success:function(e){
                jQuery('#testDiv').html(e);
            }
        });
    });

    jQuery('.tomengbutton').click(function(){
        alert('global');
    });
});

jQuery.fn.extend({
    alwayspogi:function(x){
        //console.log(x);
    }
});


