<?php

/* SSL Management */
$useSSL = true;
include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../header.php');
include_once(dirname(__FILE__).'/define.php');


//响应代码
$ErrorCode = $_REQUEST["ErrorCode"];
//返回网站订单号
$order_number = $_REQUEST["order_number"];
//交易金额
$order_amount = $_REQUEST["order_amount"];
//交易状态
$payment_status = $_REQUEST["payment_status"];
//返回支付详情
$payment_details = $_REQUEST["payment_details"];
//返回解决办法
$payment_solutions = $_REQUEST["payment_solutions"];



if($payment_status == 1 ){
	$success = 1;
}elseif ($payment_status == -1 ){
	$success = 1;
}else{
	$success = 0;
	Tools::displayError('Hacker attacks');
}


$css_files[_THEME_CSS_DIR_.'global.css'] = 'all';

if(isset($css_files) AND !empty($css_files)) $smarty->assign('css_files', $css_files);


$smarty->assign(array(
	'success' => $success,
	'payment_details' => $payment_details,
	'payment_solutions' => $payment_solutions,
	'order_amount' => $order_amount,
	'HOOK_ORDER_CONFIRMATION' => Hook::orderConfirmation(intval($order_number))));
$smarty->display(dirname(__FILE__).'/confirmation.tpl');
  
include_once(dirname(__FILE__).'/../../footer.php');





