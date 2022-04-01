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
<form action="{$handler}" method="post" id="oceanpayment_form" name="oceanpayment_form">
	<input type="hidden" name="account" value="{$account}" />
	<input type="hidden" name="terminal" value="{$terminal}" />
	<input type="hidden" name="order_number" value="{$order_number}" />
	<input type="hidden" name="order_currency" value="{$order_currency}" />
    <input type="hidden" name="order_amount" value="{$order_amount}" />
	<input type="hidden" name="backUrl" value="{$backUrl}" />
	<input type="hidden" name="noticeUrl" value="{$noticeUrl}" />
	<input type="hidden" name="signValue" value="{$signValue}" />
	<input type="hidden" name="billing_firstName" value="{$billing_firstName}" />
	<input type="hidden" name="billing_lastName" value="{$billing_lastName}" />
	<input type="hidden" name="billing_email" value="{$billing_email}" />
	<input type="hidden" name="billing_phone" value="{$billing_phone}" />
	<input type="hidden" name="methods" value="{$methods}" />
	<input type="hidden" name="billing_country" value="{$billing_country}" />
	<input type="hidden" name="billing_state" value="{$billing_state}" />
	<input type="hidden" name="billing_city" value="{$billing_city}" />
	<input type="hidden" name="billing_address" value="{$billing_address}" />
	<input type="hidden" name="billing_zip" value="{$billing_zip}" />
	<input type="hidden" name="order_notes" value="{$order_notes}" />
	<input type="hidden" name="cart_info" value="{$cart_info}" />
	<input type="hidden" name="cart_api" value="{$cart_api}" />
	</form>
</body>
</html>
