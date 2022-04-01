<?php

/* SSL Management */
$useSSL = true;
include_once(dirname(__FILE__).'/../../config/config.inc.php');
 
include_once(dirname(__FILE__).'/../../header.php');

//响应代码模式
$code_mode = Configuration :: get('OP_SOFORTBANKING_CODE');
//响应代码
$ErrorCode = $_POST["ErrorCode"];
//返回网站订单号
$order_number = $_POST["order_number"];
//交易金额
$order_amount = $_POST["order_amount"];
//交易状态
$payment_status = $_POST["payment_status"];
//返回支付详情
$payment_details = $_POST["payment_details"];



if($payment_status == '1' ){
	$success = '1';
}else{
	$success = '0';
	Tools::displayError('Hacker attacks');
}


$css_files[_THEME_CSS_DIR_.'global.css'] = 'all';

if(isset($css_files) AND !empty($css_files)) $smarty->assign('css_files', $css_files);

$smarty->assign(array(
	'success' => $success,
	'payment_details' => $payment_details,
	'order_amount' => $order_amount,
	'HOOK_ORDER_CONFIRMATION' => Hook::orderConfirmation(intval($order_number))));
$smarty->display(dirname(__FILE__).'/confirmation.tpl');
  
include_once(dirname(__FILE__).'/../../footer.php');


