<style type="text/css">
.product_font > table{
  width:100%;
}

.rate_color td{
  color:#ffffff;
  font-size: 13px;
  padding: 5px 0px;
}

table.productInfo{	
  background:#000000;	
  padding-left:7px;	
  text-align:left;    
  border:1px solid #990000;    
  border-radius:10px;    
  -moz-border-radius:10px;	  
}

.product_font{
  color:#ffffff;
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
  background:#000000;	  
  text-align:left;	  
  padding:7px;	 
  border-radius:10px;      
  -moz-border-radius:10px;	  
}	  	  

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
/*sku bof*/
.ui-widget-header {
    background: #929292;
}
.ui-widget {
    font-family: Verdana,Arial,sans-serif;
    font-size: 11px;
}
div#tabs ul.ui-tabs.ui-tabs-nav {
    margin: 0;
    padding: 0.2em 0.2em 4px;
}
.ui-tabs .ui-tabs-nav{
  padding-bottom:5px;
}
.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
    background: none repeat scroll 0 0 #b9b9b9;
    border: 1px solid #777777;
    color: #e3e3e3;
    font-weight: normal;
}
.ui-state-default.ui-corner-top:hover {
    background: none repeat scroll 0 0 #cccccc;
 color: #333333;
}
div#tabs {
    background: none repeat scroll 0 0 #111111;
   border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;	
}
.ui-tabs .ui-tabs-nav .ui-tabs-anchor {
     padding: 0.5em;
}
.ui-widget-content,.ui-widget-content p , .ui-widget-content span , .ui-widget-content a , .ui-widget-content li {
	color : #BBBBBB;
}
li.ui-tabs-active.ui-state-active , li.ui-tabs-active.ui-state-active a {
    background: none repeat scroll 0 0 #121212;
    color: #fff;
}
.ui-state-default a, .ui-state-default a:link, .ui-state-default a:visited {
    color: #333333;
    text-decoration: none;
}
.ui-tabs .ui-tabs-nav .ui-tabs-anchor {
    font-size: 11px;
    padding: 0.5em;
}
.footer{
 float:left;
}
/*sku eof*/
</style>
<td align="center" width="70%" valign="top">
    <table border="0" cellspacing="0" cellpadding="0" align="center" width="100%" class="rate_this"> 
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
        <tr class="rate_color">                      
            <td align="center">DISPLAY_PRODUCT_RATINGS | DISPLAY_PRODUCT_WRITE_RATINGS</td>     
        </tr>                        
        <tr>                        
            <td width="100%" align="left">DISPLAY_PRODUCT_EXTRA_IMAGE</td>             
        </tr>                 
        <tr>                   
            <td style="padding-right:10px">     
                <div id="tabs">
					<ul>
						<li><a href="#tabs-1">Description</a></li>
						<li><a href="#tabs-2">Product Selections</a></li>
						<li><a href="#tabs-3">Specifications</a></li>
						<li><a href="#tabs-4">Reviews</a</li>
						<li><a href="#tabs-5">Product Package</a></li>
					</ul>
					<div id="tabs-1">DISPLAY_PRODUCT_DESCRIPTION</div>
					<div id="tabs-2">DISPLAY_CHILD_PRODUCTS</div>
					<div id="tabs-3">DISPLAY_PRODUCT_ATTRIBUTES</div>
					<div id="tabs-4">DISPLAY_PRODUCT_REVIEWS</div>
					<div id="tabs-5">DISPLAY_PACKAGE</div>
                </div>                          
            </td>                          
        </tr>                        
    </table>                
</td>             
<!--left_td_end-->			
<!--right_td_start-->				
<td valign="top" align="center" width="30%">	
    <table cellpadding="0" cellspacing="0" width="86%" align="left" border="0" class="productInfo">	
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
                <table border="0" cellspacing="0" cellpadding="2" width="100%">     
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
                <table border="0" width="86%" cellspacing="1" cellpadding="2"align="left" class="ProductInfoBox infoBox" >     
                    <tr>                           
                        <td valign="top" align="left" width="100%">          
                            <table border="0"  cellspacing="0" cellpadding="2" width="100%">                  
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
    <table  width="86%" border="0" align="left">     
        <tr>                  
            <td width="100%"  class="product_font">             
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