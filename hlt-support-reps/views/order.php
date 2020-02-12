<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<?php
global $woocommerce;
$curr = get_woocommerce_currency_symbol();
switch($action){
    case 'update cart': 
    if(isset($_POST['order_id']) && $_POST['order_id']!=0){
        $order_id = $_POST['order_id'];
        $ordert = new WC_Order($order_id);
    }
     if(isset($_POST['jproduct_id'])){
        $prods = $_POST['jproduct_id'];
        
        if(isset($_POST['order_id']) && $_POST['order_id']!=0){
            $order_id = $_POST['order_id'];
            $ordert = new WC_Order($order_id);
        }else{
            $ordert = wc_create_order();
            $order_id = $order->id;
        }
        //$_SESSION['this_order_id'] = $order_id;
        //$_SESSION['this_order_page'] = 'back to cart';
        $prod_ids = array();
        $prod_quantities = array();
        foreach($prods as $key => $product_id){
            if(empty($product_id) || $product_id==0) continue;
            if(in_array($product_id,$prod_ids)){
                $keyx = array_search($product_id,$prod_ids);
                $prod_quantities[$keyx]+= (int)$_POST['jquantity'][$key];
            }
            else{
                $prod_ids[] = $product_id;
                $prod_quantities[] = (int)$_POST['jquantity'][$key];
            }
            
        }
        foreach($ordert->get_items() as $item_id => $item){
            if(!in_array($item['product_id'],$prod_ids)){
                hlt_remove_product_from_order($item_id); 
            }
            
        }
        foreach($prod_ids as $key => $product_id){
            $product_factory = new WC_Product_Factory(); 
            $product = $product_factory->get_product($product_id);
            $quantity = $prod_quantities[$key];
            $item_id = (int)is_product_in_cart($ordert, $product_id);
            if($item_id!=0){
                $ordert->update_product( $item_id, $product, array('qty'=>$quantity) );
            }
            
            else{
                $item_id = $ordert->add_product( $product, $quantity);
            }
            echo $item_id.'<';
        }
        
        $ordert->calculate_totals();
        
     }
    
        if(isset($_POST['order_id']) && !empty($_POST['coupon_codex'])){
            $order_id = $_POST['order_id'];
            $coupon_codes = $_POST['coupon_codex'];
            $orderx = new WC_Order($order_id);
            print_r($coupon_codes);
            foreach($coupon_codes as $key => $coupon_code)
                hlt_apply_coupon_code($orderx,$coupon_code);
        }
        if(isset($_POST['order_id'])){
            $orderc = new WC_Order($order_id);
            hlt_apply_coupons($order_id);
            $orderc->calculate_totals();
            
        }
    break;
    case 'proceed to billing': 
        if(isset($_POST['order_id'])){
            $order_id = $_POST['order_id'];
            
        }elseif(isset($_SESSION['this_order_id'])){
            //$order_id = $_SESSION['this_order_id'];
        }
    
    break;
     case 'back to billing': 
        if(isset($_POST['order_id'])){
            $order_id = $_POST['order_id'];
            
        }elseif(isset($_SESSION['this_order_id'])){
            //$order_id = $_SESSION['this_order_id'];
        }
    
    break; 
    case 'apply coupon': 
        if(isset($_POST['order_id']) && !empty($_POST['coupon_code'])){
            $order_id = $_POST['order_id'];
            $coupon_code = $_POST['coupon_code'];
            $order = new WC_Order($order_id);
            hlt_apply_coupon_code($order,$coupon_code);
        }
        if(isset($_POST['order_id'])){
            $order = new WC_Order($order_id);
            hlt_apply_coupons($order_id);
            $order->calculate_totals();
            
        }
    break;
    case 'back to cart':
        if(isset($_POST['order_id'])){
            $order_id = $_POST['order_id'];
            
        }elseif(isset($_SESSION['this_order_id'])){
           // $order_id = $_SESSION['this_order_id'];
        }
        
    break;
    case 'proceed to checkout':
        if(isset($_POST['order_id'])){
            $order_id = $_POST['order_id'];
            
        }elseif(isset($_SESSION['this_order_id'])){
           // $order_id = $_SESSION['this_order_id'];
        }
        $order = new WC_Order($order_id);
        $address = array(
            'first_name'=> $_POST['billing_first_name'],
            'last_name'=> $_POST['billing_last_name'],
            'phone'=> $_POST['billing_phone'],
            'email'=> $_POST['billing_email'],
            'state'=> $_POST['billing_state'],
            'country'=> $_POST['billing_country'],
            'postcode'=> $_POST['billing_zip'],
            'address_1'=> $_POST['billing_address'],
            'city'=> $_POST['billing_city'],
            'company'=> $_POST['billing_company_name']
        );
        $order->set_address($address,'billing');
        if($_POST['ship_to_a_different_address']=='YES'){
            $address2 = array(
                'first_name'=> $_POST['shipping_first_name'],
                'last_name'=> $_POST['shipping_last_name'],
                'state'=> $_POST['shipping_state'],
                'country'=> $_POST['shipping_country'],
                'postcode'=> $_POST['shipping_zip'],
                'address_1'=> $_POST['shipping_address'],
                'city'=> $_POST['shipping_city'],
                'company'=> $_POST['shipping_company_name']
            );
            $order->set_address($address2,'shipping');
        }else{
            $address = array(
                'first_name'=> $_POST['billing_first_name'],
                'last_name'=> $_POST['billing_last_name'],
                'state'=> $_POST['billing_state'],
                'country'=> $_POST['billing_country'],
                'postcode'=> $_POST['billing_zip'],
                'address_1'=> $_POST['billing_address'],
                'city'=> $_POST['billing_city'],
                'company'=> $_POST['billing_company_name']
            );
            $order->set_address($address,'shipping');
        }
        
    break;
    case 'place order':
    print_r($GLOBALS['wc_authorize_net_aim']);
    break;
    default: 
    
    break;
    
}
if(isset($_POST['order_id'])){
    $order_id = $_POST['order_id'];
    $order = new WC_Order($order_id);
    hlt_apply_coupons($order_id);
    $order->calculate_totals();
    
}
if(isset($order_id)){
    $order = new WC_Order($order_id);
    $order->calculate_totals();
}

?>