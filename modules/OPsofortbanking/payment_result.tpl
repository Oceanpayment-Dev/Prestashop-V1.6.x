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
<body onload="oceanpayment_commit();" style="text-align:center;">
		<form action="{$this_path_ssl}confirmation.php" method="post" target="_top" id="oceanpayment_form" name="oceanpayment_form">
			<input type="hidden" name="ErrorCode" value="{$ErrorCode}" />
			<input type="hidden" name="order_number" value="{$order_number}" />
			<input type="hidden" name="payment_status" value="{$payment_status}" />
			<input type="hidden" name="payment_details" value="{$payment_details}" />
		</form>
	</body>
</html>
