<!-- ---CJ-91613 Original script--- 
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"> </script>-->
<script type="text/javascript" src="../../../jquery.min.js"> </script>
<script type="text/javascript">
    $(document).ready(function() { 	 	
        //ACCORDION BUTTON ACTION (ON CLICK DO THE FOLLOWING)	
        $('.accordionButton').click(function() {		
            //REMOVE THE ON CLASS FROM ALL BUTTONS		
            $('.accordionButton').removeClass('on');		  		
            ////NO MATTER WHAT WE CLOSE ALL OPEN SLIDES	 	
            $('.accordionContent').slideUp('normal');   		//IF THE NEXT SLIDE WASN'T OPEN THEN OPEN IT		
            if($(this).next().is(':hidden') == true) {						
                //ADD THE ON CLASS TO THE BUTTON			
                $(this).addClass('on');			  		
                ////OPEN THE SLIDE			
                $(this).next().slideDown('normal');		 
            } 		  	 
        });	
        /*** REMOVE IF MOUSEOVER IS NOT REQUIRED ***/	
        //ADDS THE .OVER CLASS FROM THE STYLESHEET ON MOUSEOVER 	
        $('.accordionButton').mouseover(function() {		
            $(this).addClass('over');
        	//ON MOUSEOUT REMOVE THE OVER CLASS	
        }).mouseout(function() {		
            $(this).removeClass('over');
        });	
        /*** END REMOVE IF MOUSEOVER IS NOT REQUIRED ***/	
        /********************************************************************************************************************	CLOSES ALL S ON PAGE LOAD	********************************************************************************************************************/		
        $('.accordionContent').hide();		
        $("#open").trigger('click');});
</script> 
<style type="text/css">
    #wrapper {	width: 100%;	margin-left: auto;	margin-right: auto;	}	
    .accordionButton {		
                        width: 100%px;	
                        float: left;	
                        _float: none;  
                       /* Float works in all browsers but IE6 */	
                       border:1px solid #999999;    
                       border-radius:10px;    
                       -moz-border-radius:10px;	
                       padding:5px;	
                       background-color:#e9e9e9;	
                       cursor: pointer;	
                       background-image:url(../../../../images/product_temp_arrowL.png);	
                       background-repeat:no-repeat;	}
    .accordionContent {		
        width: 100%px;	
        float: left;	
        _float: none; 
        /* Float works in all browsers but IE6 */	
        background: #FFFFFF;	
    }
    .on {	
        background: #999999;	
        background-image:url(../../../../images/product_temp_arrowR.png);	
        background-repeat:no-repeat;	
    }
    .over {	
        background: #d7d7d7;	
        background-image:url(../../../../images/product_temp_arrowR.png);	
        background-repeat:no-repeat;	
    }		
    table.productInfo{	
        background:#FFFFFF;	
        padding-left:7px;	
        text-align:left;    
        border:1px solid #990000;    
        border-radius:10px;    
        -moz-border-radius:10px;	  
    }	  	  
    td.productInfo{    
        border:1px solid #999999;    
        border-radius:10px;    
        -moz-border-radius:10px;	
        padding:10px;	  
    }	  	   
    td.productdescheader{    
        border:1px solid #999999;    
        border-radius:10px;    
        -moz-border-radius:10px;	padding:10px;	
        background-color:#e9e9e9;	  
    }	  	  
    span.productName{	  
        font-family: Verdana, Arial, sans-serif;	  
        font-size:16px;	  
        font-weight:bold;	   
        text-align:left;	  
    }	  	  
    td.productPrice{	  
        font-family: Verdana, Arial, sans-serif;	  
        font-size:18px;	  
        color:#CC0000;	  
        font-weight:bold;	  
        text-align:left;
    }	  	  
    table.ProductInfoBox{	  
        background:#FFFFFF;	  
        text-align:left;	  
        padding:7px;	 
        border-radius:10px;      
        -moz-border-radius:10px;	  
    }	  	  
    childSection 	  {}      
    td.childHeading{ 	  
        border:1px solid #999999;      
        border-radius:5px;      
        -moz-border-radius:5px;	  
        padding:5px;	  
        background-color:#000000;	  
        color:#FFFFFF;	  
        font-size:12px;	  
        font-weight:bold;
    }      
    td.childTitle{	  
        color:#000000;	  
        font-size:12px;	  
        font-weight:bold;	  
        border:1px solid #999999;      
        border-radius:5px;      
        -moz-border-radius:5px;	  
        padding:2px;	  
        background-color:#e9e9e9;	  	  
    }      
    table.childAttributes{	
        border:1px solid #999999;    
        border-radius:5px;    
        -moz-border-radius:5px;	
        padding:10px;	  
    }      
    childButton	  {} 
</style>
<!--left_td-->           
<td align="center" width="60%" valign="top">
    <table border="0" cellspacing="0" cellpadding="0" align="center"> 
        <tr>                            
            <td class="main" valign="top" align="center">         
                <b><span class="productName">DISPLAY_PRODUCT_NAME</span>$upc</b>    
            </td>                          
        </tr>                          
        <tr>                            
            <td align="center" class="productInfo">  
                DISPLAY_PRODUCT_IMAGE                         
            </td>                         
        </tr>                  
        <tr>                      
            <td align="center">DISPLAY_PRODUCT_RATINGS | DISPLAY_PRODUCT_WRITE_RATINGS</td>     
        </tr>                        
        <tr>                        
            <td width="100%" align="left">DISPLAY_PRODUCT_EXTRA_IMAGE</td>             
        </tr>                 
        <tr>                   
            <td style="padding-right:10px">     
                <div id="wrapper">		
                    <div id="open" style="width:100%" class="accordionButton"> 
                        <b><span style="font-size: 14px; padding-left:10px">Product Description</span></b>
                    </div>		
                    <div style="width:100%" class="accordionContent">DISPLAY_PRODUCT_DESCRIPTION</div>        
                    <div> </div>        
                    <div style="width:100%" class="accordionButton"><b><span style="font-size: 14px; padding-left:10px">Product Selections</span></b></div>		
                    <div style="width:100%" class="accordionContent" align="center"><br />DISPLAY_CHILD_PRODUCTS</div>        
                    <div> </div>		
                    <div style="width:100%" class="accordionButton"><b><span style="font-size: 14px; padding-left:10px">Product Specifications</span></b></div>		
                    <div style="width:100%" class="accordionContent" >DISPLAY_PRODUCT_SPECIFICATIONS<br />DISPLAY_PRODUCT_ATTRIBUTES</div>               
                    <div> </div>        
                    <div style="width:100%" class="accordionButton"><b><span style="font-size: 14px; padding-left:10px">Product Reviews</span></b></div>		
                    <div style="width:100%" class="accordionContent">DISPLAY_PRODUCT_REVIEWS</div> 
                    <div> </div>        
                    <div style="width:100%" class="accordionButton"><b><span style="font-size: 14px; padding-left:10px">Product Package</span></b></div>
                    <div style="width:100%" class="accordionContent">DISPLAY_PACKAGE</div>
                </div>                          
            </td>                          
        </tr>                        
    </table>                
</td>             
<!--left_td_end-->			
<!--right_td_start-->				
<td valign="top" align="center" width="40%">	
    <table cellpadding="0" cellspacing="0" width="100%" border="0" class="productInfo">	
        <tr>				
            <td valign="top">		
                <table width="100%" cellpadding="0" cellspacing="0">       
                    <tr>                         
                        <td class="main" valign="top">DISPLAY_PRODUCT_MANUFACTURER</td>     
                    </tr>						
                    DISPLAY_AVAILABILITY_N_PRICE               
                    <tr>                  
                        <td align="center">            
                            DISPLAY_PRODUCT_SHARE_LINK          
                        </td>                     
                    </tr>                    
                </table>                	
            </td>                
        </tr>                
        <tr>                  
            <td valign="top">                           
                <table border="0" cellspacing="0" cellpadding="2">     
                    <tr>                 
                        <td class="main" colspan="2"><b>DISPLAY_PRODUCT_OPTIONS_TITLE</b></td> 
                    </tr>                      
                    <tr>                    
                        <td class="main">     
                            DISPLAY_PRODUCT_ATTRIBUTE        
                        </td>                      
                    </tr>                   
                </table>			
            </td>                   
        </tr>					
        <tr>                      
            <td class="main">               
                DISPLAY_PRODUCT_QUANTITY    
            </td>                   
        </tr>                  
        <tr>                     
            <td valign="top">        
                <table border="0" width="100%" cellspacing="1" cellpadding="2" class="ProductInfoBox infoBox" >     
                    <tr>                           
                        <td valign="top" align="left">          
                            <table border="0"  cellspacing="0" cellpadding="2">                  
                                <tr>                            
                                    <td align="left" class="smallText" colspan="3">	       
                                        DISPLAY_PRODUCT_DISCLAIMER			
                                    </td>                      
                                </tr>                           
                                <tr>                             
                                    <td width="10"></td>         
                                    <td class="main" align="center" colspan="2">        
                                        DISPLAY_PRODUCT_ADD_TO_CART            
                                    </td>                   
                                    <td width="10"></td>     
                                </tr>                       
         <?// #7 12jan2014 (MA) BOF?>           
                                <tr>               
                                    <td width="10"></td>  
                                    <td class="main" align="right" colspan="2">     
                                        DISPLAY_PRODUCT_ADD_TO_WISHLIST            
                                    </td>                               
                                    <td width="10"></td>            
                                </tr>                              
  <?// #7 12jan2014 (MA) EOF?>                             
                            </table>                      
                        </td>                         
                    </tr> 
                </table>                   
            </td>             
        </tr>      
    </table>                
    <table  width="100%" border="0">     
        <tr>                  
            <td>             
                DISPLAY_PRODUCT_RELATED_ITEMS 
                DISPLAY_RECOMMENDED_PRODUCTS  
                DISPLAY_ALSO_PURCHASED_PRODUCTS 
                DISPLAY_NEW_PRODUCTS
                DISPLAY_HOW_PRODUCTS
                DISPLAY_POPULAR_PRODUCT
                DISPLAY_FEATURED_PRODUCTS_1
                DISPLAY_FEATURED_PRODUCTS_2
                DISPLAY_FEATURED_PRODUCTS_3
                DISPLAY_FEATURED_MANUFACTUERERS
                DISPLAY_FEATURED_CATEGORY 
            </td>
        </tr>
    </table>
    