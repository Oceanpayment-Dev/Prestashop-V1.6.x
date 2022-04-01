{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!DOCTYPE HTML>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7 " lang="{$lang_iso}"><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7" lang="{$lang_iso}"><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9 ie8" lang="{$lang_iso}"><![endif]-->
<!--[if gt IE 8]> <html class="no-js ie9" lang="{$lang_iso}"><![endif]-->
<html lang="{$lang_iso}">
	<head>
		<meta charset="utf-8" />
		<title>{$meta_title|escape:'html':'UTF-8'}</title>
{if isset($meta_description) AND $meta_description}
		<meta name="description" content="{$meta_description|escape:'html':'UTF-8'}" />
{/if}
{if isset($meta_keywords) AND $meta_keywords}
		<meta name="keywords" content="{$meta_keywords|escape:'html':'UTF-8'}" />
{/if}
		<meta name="generator" content="PrestaShop" />
		<meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
		<meta name="viewport" content="width=device-width, minimum-scale=0.25, maximum-scale=1.6, initial-scale=1.0" /> 
		<meta name="apple-mobile-web-app-capable" content="yes" /> 
		<link rel="icon" type="image/vnd.microsoft.icon" href="{$favicon_url}?{$img_update_time}" />
		<link rel="shortcut icon" type="image/x-icon" href="{$favicon_url}?{$img_update_time}" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/global.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/autoload/highdpi.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/autoload/responsive-tables.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/autoload/uniform.default.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/js/jquery/plugins/fancybox/jquery.fancybox.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/modules/blockbanner/blockbanner.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/product_list.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/modules/blockcart/blockcart.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/modules/blockcategories/blockcategories.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/modules/blockcurrencies/blockcurrencies.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/modules/blockfacebook/css/blockfacebook.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/modules/blocklanguages/blocklanguages.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/modules/blockcontact/blockcontact.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/modules/blocknewsletter/blocknewsletter.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/js/jquery/plugins/autocomplete/jquery.autocomplete.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/modules/blocksearch/blocksearch.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/modules/blocktags/blocktags.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/modules/blockuserinfo/blockuserinfo.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/modules/blockviewed/blockviewed.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/modules/homeslider/homeslider.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/modules/themeconfigurator/css/hooks.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/modules/blockwishlist/blockwishlist.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/modules/productcomments/productcomments.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/modules/blocktopmenu/css/blocktopmenu.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/prestashop1609/themes/default-bootstrap/css/modules/blocktopmenu/css/superfish-modified.css" type="text/css" media="all" />
{if isset($css_files)}
	{foreach from=$css_files key=css_uri item=media}
		<link rel="stylesheet" href="{$css_uri|escape:'html':'UTF-8'}" type="text/css" media="{$media|escape:'html':'UTF-8'}" />
	{/foreach}
{/if}
{if isset($js_defer) && !$js_defer && isset($js_files) && isset($js_def)}
	{$js_def}
	{foreach from=$js_files item=js_uri}
	<script type="text/javascript" src="{$js_uri|escape:'html':'UTF-8'}"></script>
	{/foreach}
{/if}
		{$HOOK_HEADER}
		<link rel="stylesheet" href="http{if Tools::usingSecureMode()}s{/if}://fonts.googleapis.com/css?family=Open+Sans:300,600&amp;subset=latin,latin-ext" type="text/css" media="all" />
		<!--[if IE 8]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
	</head>
	<body{if isset($page_name)} id="{$page_name|escape:'html':'UTF-8'}"{/if} class="{if isset($page_name)}{$page_name|escape:'html':'UTF-8'}{/if}{if isset($body_classes) && $body_classes|@count} {implode value=$body_classes separator=' '}{/if}{if $hide_left_column} hide-left-column{/if}{if $hide_right_column} hide-right-column{/if}{if isset($content_only) && $content_only} content_only{/if} lang_{$lang_iso}">
	{if !isset($content_only) || !$content_only}
		{if isset($restricted_country_mode) && $restricted_country_mode}
			<div id="restricted-country">
				<p>{l s='You cannot place a new order from your country.'} <span class="bold">{$geolocation_country|escape:'html':'UTF-8'}</span></p>
			</div>
		{/if}
		<div id="page">
			<div class="header-container">
				<header id="header">
					<div class="banner">
						<div class="container">
							<div class="row">
								{hook h="displayBanner"}
							</div>
						</div>
					</div>
					<div class="nav">
						<div class="container">
							<div class="row">
								<nav>{hook h="displayNav"}</nav>
							</div>
						</div>
					</div>
					<div>
						<div class="container">
							<div class="row">
								<div id="header_logo">
									<a href="{$base_dir}" title="{$shop_name|escape:'html':'UTF-8'}">
										<img class="logo img-responsive" src="{$logo_url}" alt="{$shop_name|escape:'html':'UTF-8'}"{if isset($logo_image_width) && $logo_image_width} width="{$logo_image_width}"{/if}{if isset($logo_image_height) && $logo_image_height} height="{$logo_image_height}"{/if}/>
									</a>
								</div>
								{if isset($HOOK_TOP)}{$HOOK_TOP}{/if}
							</div>
						</div>
					</div>
				</header>
			</div>
			
			<div class="columns-container">
				<div id="columns" style="margin: 0px auto;" class="container">
				    <div id="loading" style="position: relative;">
					    <div style="position:absolute; top:100px; left:45%; z-index:3;" >
					        <img src="{$this_path_ssl}loading.gif"   />
					    </div>
					</div>
					<form action="{$handler}" method="post" id="creditcard_payment_checkout" name="creditcard_payment_checkout" target="ifrm_creditcard_checkout">
						<input type="hidden" name="account" value="{$account}" />
						<input type="hidden" name="terminal" value="{$terminal}" />
						<input type="hidden" name="order_number" value="{$order_number}" />
						<input type="hidden" name="order_currency" value="{$order_currency}" />
						<input type="hidden" name="order_amount" value="{$order_amount}" />
						<input type="hidden" name="backUrl" value="{$backUrl}" />
						<input type="hidden" name="noticeUrl" value="{$noticeUrl}" />
						<input type="hidden" name="signValue" value="{$signValue}" />
						<input type="hidden" name="methods" value="{$methods}" />
						<input type="hidden" name="billing_firstName" value="{$billing_firstName}" />
						<input type="hidden" name="billing_lastName" value="{$billing_lastName}" />
						<input type="hidden" name="billing_email" value="{$billing_email}" />
						<input type="hidden" name="billing_phone" value="{$billing_phone}" />
						<input type="hidden" name="billing_country" value="{$billing_country}" />
						<input type="hidden" name="billing_state" value="{$billing_state}" />
						<input type="hidden" name="billing_city" value="{$billing_city}" />
						<input type="hidden" name="billing_address" value="{$billing_address}" />
						<input type="hidden" name="billing_zip" value="{$billing_zip}" />
						<input type="hidden" name="ship_firstName" value="{$ship_firstName}" />
						<input type="hidden" name="ship_lastName" value="{$ship_lastName}" />
						<input type="hidden" name="ship_phone" value="{$ship_phone}" />
						<input type="hidden" name="ship_country" value="{$ship_country}" />
						<input type="hidden" name="ship_state" value="{$ship_state}" />
						<input type="hidden" name="ship_city" value="{$ship_city}" />
						<input type="hidden" name="ship_address" value="{$ship_address}" />
						<input type="hidden" name="ship_zip" value="{$ship_zip}" />
						<input type="hidden" name="order_notes" value="{$order_notes}" />
						<input type="hidden" name="productName" value="{$productName}" />
						<input type="hidden" name="productSku" value="{$productSku}" />
						<input type="hidden" name="productNum" value="{$productNum}" />
						<input type="hidden" name="cart_info" value="{$cart_info}" />
						<input type="hidden" name="cart_api" value="{$cart_api}" />
						<input type="hidden" name="pages" value="{$pages}" />
					</form>
					<iframe scrolling="auto" id="ifrm_creditcard_checkout" name="ifrm_creditcard_checkout" frameborder="no"  width="100%" {if $pages==1} height="540px" {else} height="350px" {/if} ></iframe>	
					<script type="text/javascript">
						document.creditcard_payment_checkout.submit();
						var ifrm_cc  = document.getElementById("ifrm_creditcard_checkout");
						var loading  = document.getElementById("loading");
						if (ifrm_cc.attachEvent){
						    ifrm_cc.attachEvent("onload", function(){
						        loading.style.display = 'none';
						    });
						} else {
						    ifrm_cc.onload = function(){
						        loading.style.display = 'none';
						    };
						}
					</script>
				</div>
			</div>
			{if isset($HOOK_FOOTER)}
				<!-- Footer -->
				<div class="footer-container">
					<footer id="footer"  class="container">
						<div class="row">{$HOOK_FOOTER}</div>
					</footer>
				</div><!-- #footer -->
			{/if}
		</div><!-- #page -->
{/if}
{include file="$tpl_dir./global.tpl"}
	</body>
</html>
