<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-us" lang="en-us" dir="ltr">
<head>
	<title>Page not found</title>
    <style type="text/css">
		* {
		color:#333;
		font-family: veranda,arial,sans-serif;
		font-size:11px; }
		html {
		height:100%;
		margin-bottom:1px; }
		body {
		background:none repeat scroll 0 0 ;
		font-family:helvetica,arial,sans-serif;
		font-weight:normal;
		height:100%;
		margin:0 0 1px;
		padding:0; }
		table, td, th, div, pre, blockquote, ul, ol, dl, address, .componentheading, .contentheading, .contentpagetitle, .sectiontableheader, .newsfeedheading {
		font-family:helvetica,arial,sans-serif;
		font-weight:normal; }
		table {margin-left: 10px;}
		#outline {
		background:none repeat scroll 0 0 #FFFFFF;
		margin:0;
		padding:30px 0;
		width:814px; }
		#boxoutline {
		border:1px solid #000000;
		margin:0;
		padding:0;
		width:800px; }
		#boxbody {
		margin:0;
		padding:10px;
		text-align:left;
		width: 800px; }

	</style>
</head>
<body>

<?php
//get index page for website

// check for https 
$protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
$site_index = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$site_index = explode("/" ,"$site_index");

$site_index = "$site_index[0]//$site_index[2]";

?>
    <table cellpadding="0" cellspacing="0" border="0" width="800px" height="111px" align="center" background="http://67.227.172.29/images/header1.jpg" style="margin: 0 auto;">
        <tr valign="top">
            <td width="56%">
                <p style="margin: 5px 0px 0px 15px; text-align: left; letter-spacing: 2px;">
                Page Not Found
                </p>
                <p style="margin: 50px 0px 0px 15px; text-align: left;">
                <a href="<?php echo $site_index ?>" style="font-size: 24px;">Click Here To Return to Home Page</a>
                </p>
            </td>
            <td width="*">
                <p style="margin: 5px 15px 0px 0px; text-align: right; font-weight: bold; letter-spacing: 2px;">
                	<? echo date('M d, Y'); ?>
                </p>
                <p style="margin: 39px 0px 2px 80px; text-align: left;">
                	Not what you were looking for?
                </p>
                <p style="margin: 2px 0px 2px 8px; text-align: left;">
                    <form action="http://www.google.com/cse" id="cse-search-box" target="_blank">
                    <div>
                        <input type="hidden" name="cx" value="partner-pub-9244942203861462:n1b0duabdsp" />
                        <input type="hidden" name="ie" value="ISO-8859-1" />
                        <input type="text" name="q" size="50" />
                        <input type="submit" name="sa" value="Search" />
                    </div>
                    </form>
                </p>
            </td>
        </tr>
    </table>
	<div align="center">
		<div id="outline">
			<div id="boxoutline">
   				<div style="margin-bottom: 0px;">
                <br />
                <br />
                    <table cellpadding="0" cellspacing="0" border="0" width="780px" align="center">
                        <tr valign="top">
                            <td width="50%" align="center">
								<script type="text/javascript"><!--
                                google_ad_client = "pub-9244942203861462";
                                /* 336x280, created 5/4/10 */
                                google_ad_slot = "5199799530";
                                google_ad_width = 336;
                                google_ad_height = 280;
                                //-->
                                </script>
                                <script type="text/javascript"
                                src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                                </script>
                            </td>
                            <td width="50%" align="center">
								<script type="text/javascript"><!--
                                google_ad_client = "pub-9244942203861462";
                                /* 336x280, created 5/4/10 */
                                google_ad_slot = "4966824049";
                                google_ad_width = 336;
                                google_ad_height = 280;
                                //-->
                                </script>
                                <script type="text/javascript"
                                src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                                </script>
                            </td>
                        </tr>
                    </table>
                <br />
                    <table cellpadding="0" cellspacing="0" border="0" width="780px" align="center">
                        <tr valign="top">
                            <td align="center">
								<script type="text/javascript" src="http://www.google.com/cse/brand?form=cse-search-box&amp;lang=en"></script>
                                <br />
                                <br />
                                <script type="text/javascript"><!--
                                    google_ad_client = "pub-9244942203861462";
                                    /* 728x90, created 5/4/10 */
                                    google_ad_slot = "9945575305";
                                    google_ad_width = 728;
                                    google_ad_height = 90;
                                    //-->
                                </script>
                                <script type="text/javascript"
                                    src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                                </script>
                            </td>
                        </tr>
                    </table>
				<br />
                </div>
			</div>
		</div>
        <br />

    </div>
    
</body>
</html>
