jQuery(document).ready(function(){
	jQuery('.datepicker').datepicker({ dateFormat: 'dd-mm-yy' });
    jQuery('#regular_add').click(function(){
        var xcount = jQuery("#xcount").val();
        xcount=parseInt(xcount)+1;
        jQuery("#xcount").val(xcount);
        jQuery("#regular_row .hlt_no").html(xcount+'.');
        jQuery("#special_row .hlt_no").html(xcount+'.');
        var regular_tr = jQuery("#regular_row").html();
        jQuery( regular_tr ).appendTo( "#orderitmz" );
        jQuery('.xremove').on('click',function(){
            jQuery(this).closest('tr').html('');
        });
        
        //populate_price($(".items_tr").find(".xproduct_id"));
        jQuery('.xproduct_id').on('change',function(){
            populate_svr_prices(jQuery(this));
        });
        
        
        
        
    });
    jQuery('#special_add').click(function(){
        var xcount = jQuery("#xcount").val();
        xcount=parseInt(xcount)+1;
        jQuery("#xcount").val(xcount);
        jQuery("#regular_row .hlt_no").html(xcount+'.');
        jQuery("#special_row .hlt_no").html(xcount+'.');
        var special_tr = jQuery("#special_row").html();
        jQuery( special_tr ).appendTo( "#orderitmz" );
        jQuery('.xremove').on('click',function(){
            jQuery(this).closest('tr').html('');
        });
        //populate_price(jQuery('.items_tr .xproduct_id').last());
        jQuery('.xproduct_id').on('change',function(){
            populate_svr_prices(jQuery(this));
        });
    });
    function populate_svr_prices(objx){
            var product_id = objx.val();
            var qty = objx.closest('tr').find('.hlt_quantity input').val();
            var cost = 0;
            var total = 0;
            var this_obj = objx;
            jQuery.post(ajaxurl, {'action': 'hlt_reps_populate_product_price_action','qty':qty, 'product_id': product_id }, function(response) {
                obj = JSON.parse(response);
		          cost=obj.cost;
                    total = obj.total;
                    this_obj.closest('tr').find('.hlt_cost').html('<span>'+cost+'</span>');
                    this_obj.closest('tr').find('.hlt_total').html('<span>'+total+'</span>');                  
    		});
            
    }
     function populate_price(objx){
        var product_id =objx.val();
        var qty = objx.closest('tr').find('.hlt_quantity input').val();
        var cost = 0;
        var total = 0;
        var this_obj = objx;
        jQuery.post(ajaxurl, {'action': 'hlt_reps_populate_product_price_action','qty':qty, 'product_id': product_id }, function(response) {
            obj = JSON.parse(response);
              cost=obj.cost;
                total = obj.total;
                this_obj.closest('tr').find('.hlt_cost').html('<span>'+cost+'</span>');
                this_obj.closest('tr').find('.hlt_total').html('<span>'+total+'</span>');                  
    	});
     }
    
    jQuery('.xremove').on('click',function(){
        jQuery(this).closest('tr').html('');
    });
    
    jQuery('.xproduct_id').change(function(){
        var product_id = jQuery(this).val();
		jQuery.post(ajaxurl, {'action': 'hlt_reps_populate_product_action', 'product_id': product_id }, function(response) {
		      if(response==0){
		          jQuery('#xbilling_state').replaceWith('<input type="text" name="billing_state" id="xbilling_state" style="width: 190px;">');
		      }else{
		          jQuery('#xbilling_state').replaceWith('<select name="billing_state" id="xbilling_state" style="width: 190px;">');
		          jQuery('#xbilling_state').html(response);
		      }
			
		});
    });
    
    
});