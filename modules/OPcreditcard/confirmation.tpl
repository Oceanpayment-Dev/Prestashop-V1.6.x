<script type="text/javascript">
<!--
	var baseDir = '{$base_dir_ssl}';
-->
</script>
{if isset($css_files)}
	{foreach from=$css_files key=css_uri item=media}
	<link href="{$css_uri}" rel="stylesheet" type="text/css" media="{$media}" />
	{/foreach}
{/if}

{capture name=path}
	<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
		{l s='My account'}
	</a>
	<span class="navigation-pipe">{$navigationPipe}</span>
	<span class="navigation_page">{l s='Order confirmation'}</span>
{/capture}



{include file="$tpl_dir./errors.tpl"}

<h1 class="page-heading bottom-indent">{l s='Order confirmation'}</h1>
{assign var='current_step' value='payment'}
{if $success==1}
	<p class="alert alert-success" style="color:green">{$payment_details}</p>
	<p>{l s='Your order on' mod='CreditCard'} <span class="bold">{$shop_name}</span> {l s='is now complete.' mod='CreditCard'}
		<br /><br />
		{l s='Once your card has been verified and funds have been accepted, your order will be shipped.' mod='CreditCard'}
		<br /><br />- {l s='Total Payment Pending:' mod='CreditCard'} <span class="price">{$order_amount}</span>
		<br /><br />{l s='For any questions or for further information, please contact our' mod='CreditCard'} <a href="{$base_dir}contact-form.php">{l s='customer support' mod='CreditCard'}</a>.
	</p>
{else}
	<p class="alert alert-danger">{$payment_details}</p>
	<p class="alert alert-warning" >{$payment_solutions}</p>
	<p>
		{l s='We noticed a problem with your order. If you think this is an error, you can contact our' mod='CreditCard'} 
		<a href="{$base_dir}contact-form.php">{l s='customer support' mod='CreditCard'}</a>.
	</p>
{/if}
<br />
<a href="{$base_dir_ssl}order-history" title="{l s='Back to orders'}"><i class="icon-chevron-left"></i></a>
<a href="{$base_dir_ssl}order-history" title="{l s='Back to orders'}">{l s='Back to orders'}</a>
