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

{capture name=path}{l s='Order confirmation'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Order confirmation'}</h2>

{assign var='current_step' value='payment'}

{include file="$tpl_dir./errors.tpl"}
{if $success=='1'}
	<p>{l s='Your order on' mod='Sofortbanking'} <span class="bold">{$shop_name}</span> {l s='is now complete.' mod='Sofortbanking'}
		<br /><br />
		{l s='Once your card has been verified and funds have been accepted, your order will be shipped.' mod='Sofortbanking'}
		<br /><br />- {l s='Total Payment Pending:' mod='Sofortbanking'} <span class="price">{$order_amount}</span>
		<br /><br />{l s='For any questions or for further information, please contact our' mod='Sofortbanking'} <a href="{$base_dir}contact-form.php">{l s='customer support' mod='Sofortbanking'}</a>.
	</p>
{else}
	<p class="warning">
		{l s='We noticed a problem with your order. If you think this is an error, you can contact our' mod='Sofortbanking'} 
		<a href="{$base_dir}contact-form.php">{l s='customer support' mod='Sofortbanking'}</a>.
	</p>
{/if}
<br />
<a href="{$base_dir_ssl}history.php" title="{l s='Back to orders'}"><img src="{$img_dir}icon/order.gif" alt="{l s='Back to orders'}" class="icon" /></a>
<a href="{$base_dir_ssl}history.php" title="{l s='Back to orders'}">{l s='Back to orders'}</a>
