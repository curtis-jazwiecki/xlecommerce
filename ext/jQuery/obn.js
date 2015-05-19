 var jQuery = jQuery.noConflict();

jQuery(document).ready(function() {

            jQuery('input:checkbox[name^="columns_"]').click(function(){

                var product_id = jQuery(this).val();

                var action = (jQuery(this).is(':checked') ? 'add' : 'remove'); 

                jQuery.ajax({

                   url: 'register_product_for_comparison.php', 

                   method: 'post', 

                   data: {

                        action: action, 

                        id: product_id

                   }, 

                   success: function(response){

                        if (response=='added'){

                            alert('Product added for comparison');

                        } else if (response=='removed'){

                            alert('Product removed from comparison');

                        }

                   }

                });

            });



            });

  function estimatorpopupWindow(URL) 
  {
    window.open(URL,'shippingestimator','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=800,height=600');
    }
         
