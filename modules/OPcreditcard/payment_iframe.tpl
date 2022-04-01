<html>
<head>
{literal}
<script language="javascript">
function oceanpayment_commit(){
    document.oceanpayment_form.submit();
}
</script>
{/literal}
</head>
<body onLoad="oceanpayment_commit();" style="text-align:center;">
<div id="loading" style="position: relative;">
    <div style="position:absolute; top:100px; left:45%; z-index:3;" >
        <img src="{$this_path_ssl}loading.gif"   />
    </div>
</div>
<form action="{$handler}" method="post" id="oceanpayment_form" name="oceanpayment_form">
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
</body>
</html>
