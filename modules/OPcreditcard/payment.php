<?php
/* SSL Management */
$useSSL = true;
include(dirname(__FILE__).'/../../config/config.inc.php');
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
require_once(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/OPcreditcard.php');

if (!$cookie->isLogged(true))
    Tools::redirect('authentication.php?back=order.php');

if(empty($cart->id))
	Tools::redirect('history.php');


//生成form表单
$OPcreditcard = new OPcreditcard();

//支付模式
$pay_mode = Configuration :: get('OP_CREDITCARD_PAY_MODE');


if($pay_mode == 1){
	//内嵌
	$OPcreditcard->execPayment($cart);
}elseif($pay_mode == 0){
	//跳转
	echo $OPcreditcard->execPayment($cart);
	exit;
}



/* CSS */
$css_files[_THEME_CSS_DIR_.'global.css'] = 'all';

if(isset($css_files) AND !empty($css_files)) $smarty->assign('css_files', $css_files);
if(isset($js_files) AND !empty($js_files)) $smarty->assign('js_files', $js_files);

/* Hooks are volontary out the initialize array (need those variables already assigned) */
$smarty->assign(array(
		'HOOK_HEADER' => Module::hookExec('displayHeader'),
		'HOOK_TOP' => Module::hookExec('displayTop'),
		'HOOK_FOOTER' => Module::hookExec('displayFooter'),
		'logo_url' => _PS_IMG_.Configuration::get('PS_LOGO').'?'.Configuration::get('PS_IMG_UPDATE_TIME'),
		'static_token' => Tools::getToken(false),
		'token' => Tools::getToken(),
		'priceDisplayPrecision' => _PS_PRICE_DISPLAY_PRECISION_,
		'this_path_ssl' => Tools::getHttpHost(true, true).__PS_BASE_URI__.'modules/OPcreditcard/',
));


$smarty->display(dirname(__FILE__).'/payment.tpl');


?>
