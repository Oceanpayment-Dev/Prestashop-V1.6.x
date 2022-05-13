<?php

/* SSL Management */
$useSSL = true;
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');



//账户
$account = $_POST['account'];
//终端号
$terminal = $_POST['terminal'];
//交易流水订单号
$payment_id = $_POST['payment_id'];
//返回网站订单号
$order_number = $_POST["order_number"];
//交易币种
$order_currency = $_POST["order_currency"];
//交易金额
$order_amount = $_POST["order_amount"];
//交易状态
$payment_status = $_POST["payment_status"];
//返回支付详情
$payment_details = $_POST["payment_details"];
//未通过的风控规则
$payment_risk = $_POST['payment_risk'];
//返回支付信用卡卡号
$card_number = $_POST['card_number'];
//返回交易类型
$payment_authType = $_POST['payment_authType'];
//备注
$order_notes = $_POST["order_notes"];
//数据签名
$back_signValue = $_POST["signValue"];

//securecode
$securecode = Configuration :: get('OP_GIROPAY_SECURECODE');
//校验源字符串
$local_signValue = hash("sha256",$account.$terminal.$order_number.$order_currency.$order_amount.$order_notes.$card_number.
		$payment_id.$payment_authType.$payment_status.$payment_details.$payment_risk.$securecode);

//是否推送
$response_type = $_REQUEST['response_type'];


//用于支付结果页面显示响应代码
$getErrorCode = explode(':', $payment_details);
$ErrorCode = $getErrorCode[0];

if(Configuration :: get('OP_GIROPAY_WRITE_LOG')) {
	$logType = '[Browser Return]';


	//记录日志
	$filedate   = date('Y-m-d');

	$returndate = date('Y-m-d H:i:s');

	$newfile    = fopen( "oceanpayment_log/" . $filedate . ".log", "a+" );

	$return_log = $returndate . $logType . "\r\n".
			"response_type = "       . $_REQUEST['response_type'] . "\r\n".
			"account = "             . $_REQUEST['account'] . "\r\n".
			"terminal = "            . $_REQUEST['terminal'] . "\r\n".
			"payment_id = "          . $_REQUEST['payment_id'] . "\r\n".
			"order_number = "        . $_REQUEST['order_number'] . "\r\n".
			"order_currency = "      . $_REQUEST['order_currency'] . "\r\n".
			"order_amount = "        . $_REQUEST['order_amount'] . "\r\n".
			"payment_status = "      . $_REQUEST['payment_status'] . "\r\n".
			"payment_details = "     . $_REQUEST['payment_details'] . "\r\n".
			"signValue = "           . $_REQUEST['signValue'] . "\r\n".
			"order_notes = "         . $_REQUEST['order_notes'] . "\r\n".
			"card_number = "         . $_REQUEST['card_number'] . "\r\n".
			"payment_authType = "    . $_REQUEST['payment_authType'] . "\r\n".
			"payment_risk = "        . $_REQUEST['payment_risk'] . "\r\n";

	$return_log = $return_log . "*************************************\r\n";

	$return_log = $return_log.file_get_contents( "oceanpayment_log/" . $filedate . ".log");

	$filename   = fopen( "oceanpayment_log/" . $filedate . ".log", "r+" );

	fwrite($filename,$return_log);

	fclose($filename);

	fclose($newfile);
}

// So you migth have two order states
$new_history = new OrderHistory();
$new_history->id_order = $order_number;

if(strtoupper($local_signValue) == strtoupper($back_signValue)){
	if(substr($payment_details,0,5) == '20061'){	 //排除订单号重复(20061)的交易
		
		
	}else{	
		if($payment_status == 1 ){
			//支付成功
			$new_history->changeIdOrderState((int)Configuration :: get('OP_GIROPAY_SUCCEED_STATES'), $order_number);	
			$new_history->addWithemail(true);
		}else{
			//支付失败
			$new_history->changeIdOrderState((int)Configuration :: get('OP_GIROPAY_FAIL_STATES'), $order_number);
		}		
	}
	
}else{  //数据签名对比失败
	$new_history->changeIdOrderState((int)Configuration :: get('OP_GIROPAY_FAIL_STATES'), $order_number);
}



$smarty->assign(array(
		'ErrorCode' => $ErrorCode,
		'order_number' => $order_number,
		'payment_status'=>$payment_status,
		'payment_details'=>$payment_details,
		'this_path_ssl' => Tools::getHttpHost(true, true).__PS_BASE_URI__.'modules/OPgiropay/'));
$smarty->display(dirname(__FILE__).'/payment_result.tpl');
