<?php
if (isset($snippet)){
    $style =   "width:"  . $width . $width_type  . ";"  . "height:"  . $height . $height_type;
    if($status == 'readyToPay'){
        echo $header; 
        echo $column_left; 
        echo $column_right; 
        //Show the snippet by readyToPay or as reci...
        ?>
         <div class="container"><?php echo $content_top; ?>
            <iframe id='checkoutIframe' name='checkoutIframe' style=<?php echo $style ?> src='<?php echo $snippet ?>' frameborder='0'  scrolling='no'> </iframe>
         </div>  
        
        <?php 
        echo $footer;
    }else{ 
        if(!$customerIsLogged){
        ?>
            <!-- <div class="well well-sm"> -->
            <div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo $info_checkout; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php 
        } 
        ?>
        <!--<div id="embeddedIframeBlocker" style="background-color: red; display: nones">-->
            <div style=<?php  echo $style; ?>>

            <?php 
                //show the snippet in checkout side.
                echo $snippet;
            ?>
            </div>
        
        <div id="paysonTracker" style="display: none"></div>
        <!-- Beräknar frakten </div> -->
        <?php 
    }
}else{ ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_checkout_id; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
	  <div id="paysonTracker" style="display: none"><?php echo rand(); ?></div>
<?php
}
?>
<script type="text/javascript"><!--  

   // QUICH AJAX CHECKOUT OPEN CART FIX -> START
    document.paysonQuickCheckoutTracker = function() {
        try {
			// Check payment method
			var isPayson = document.getElementById("paysonTracker");
			
			var paymentForm = document.getElementById('payment_method_form');
			if(paymentForm) {
				var paysonPaymentMethod = document.getElementById('paysonCheckout2');
				if(paysonPaymentMethod) {
					isPayson = paysonPaymentMethod.checked;
				}
			}
			
            document.getElementById("confirm_view").style.display = (isPayson?"none":"block");
            document.getElementById("payment_address").parentElement.style.display = (isPayson?"none":"block");
            document.getElementById("payment_view").parentElement.parentElement.parentElement.className = (isPayson?"col-md-12":"col-md-8");
            
        } catch(ex) {
            // Nothing to do
        }
        setTimeout(document.paysonQuickCheckoutTracker, 500);
    };
    document.paysonQuickCheckoutTracker();
    // QUICH AJAX CHECKOUT OPEN CART FIX -> END
    
    
    document.addEventListener("PaysonEmbeddedAddressChanged",function(evt) {
        var address = evt.detail;
        
      	//adress.City
        //adress.CountryCode
        //adress.FirstName
        //adress.LastName
        //adress.PostalCode
        //adress.Street
        //alert(address.City);
        
        console.log(address);

            
        var country_code = '<?php echo isset($country_code)?strtoupper($country_code):0; ?>';
        var customerIsLogged = '<?php echo isset($customerIsLogged)?$customerIsLogged:0; ?>';

        if(!customerIsLogged && (country_code !== address.CountryCode.toUpperCase())){
          // document.location='index.php?route=payment/paysonCheckout2/returnFromPayson&address_data='+JSON.stringify(address);
        }
    });
 
//--></script>