<?php
/* SSL Management */
$useSSL = true;
include(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/OPgiropay.php');

if (!$cookie->isLogged(true))
    Tools::redirect('authentication.php?back=order.php');

if(empty($cart->id))
	Tools::redirect('history.php');

$OPgiropay = new OPgiropay();
echo $OPgiropay->execPayment($cart);
?>
