<?php
class OPsofortbanking extends PaymentModule {
	private $_html = '';
	private $_postErrors = array ();

	public function __construct() {
		$this->name = 'OPsofortbanking';
		//标记模块类型
		$this->tab = 'payments_gateways';
		$this->version = '1.7.1';
        
		if (!Configuration::get('OP_SOFORTBANKING_ORDER_STATE'))				//If, for some reason, there are no currencies, make them
			$this->_makeOrderState();

        $this->idOrderState = Configuration::get('OP_SOFORTBANKING_ORDER_STATE');

		$this->currencies     = true;
		$this->currencies_mode = 'radio';

		parent :: __construct();

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Oceanpayment - Sofortbanking');
		$this->description = $this->l('Accepts payments by Oceanpayment');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
	}

	public function getOceanpaymentUrl() {
		$OP_SOFORTBANKING_url = "https://secure.oceanpayment.com/gateway/service/test";
		return $OP_SOFORTBANKING_url;
	}

	/**
	 * 安装
	 */
	public function install() {
		//支付地址(正式)
        $this->_makeOrderState();
		$action_URL="https://secure.oceanpayment.com/gateway/service/test";
		$back_url='http://' . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__.'modules/OPsofortbanking/payment_result.php';

		if (!Configuration :: updateValue('OP_SOFORTBANKING_SUCCEED_STATES', '2') OR !Configuration :: updateValue('OP_SOFORTBANKING_FAIL_STATES', '6') OR !parent :: install() OR !Configuration :: updateValue('OP_SOFORTBANKING_ACCOUNT', '') OR !Configuration :: updateValue('OP_SOFORTBANKING_SECURECODE', '') 
		 OR !Configuration :: updateValue('OP_SOFORTBANKING_HANDLER', $action_URL) OR !Configuration :: updateValue('OP_SOFORTBANKING_BACK_URL', $back_url) OR !$this->registerHook('payment') OR !$this->registerHook('paymentReturn'))
			return false;
		return true;
	}

	/**
	 * 卸载
	 */
	public function uninstall() {
		if (!Configuration :: deleteByName('OP_SOFORTBANKING_SUCCEED_STATES') OR !Configuration :: deleteByName('OP_SOFORTBANKING_FAIL_STATES') OR !Configuration :: deleteByName('OP_SOFORTBANKING_ACCOUNT') OR !Configuration :: deleteByName('OP_SOFORTBANKING_SECURECODE') OR !Configuration :: deleteByName('OP_SOFORTBANKING_TERMINAL') 
		 OR !Configuration :: deleteByName('OP_SOFORTBANKING_HANDLER') OR !Configuration :: deleteByName('OP_SOFORTBANKING_BACK_URL') OR !parent :: uninstall())
			return false;
		return true;
	}

	
	public function getContent() {
		$this->_html = '<h2>Oceanpayment</h2>';
		if (isset ($_POST['submitOceanpayment'])) {
			if (empty ($_POST['account']))
				$this->_postErrors[] = $this->l('账户不能为空!');
			elseif (empty ($_POST['securecode'])) $this->_postErrors[] = $this->l('securecode不能为空!');
			elseif (empty ($_POST['terminal'])) $this->_postErrors[] = $this->l('终端号不能为空!');
			elseif (empty ($_POST['succeed_states'])) $this->_postErrors[] = $this->l('成功状态不能为空!');
			elseif (empty ($_POST['fail_states'])) $this->_postErrors[] = $this->l('失败状态不能为空!');
			elseif (empty ($_POST['handler'])) $this->_postErrors[] = $this->l('提交地址不能为空!');
			elseif (empty ($_POST['backurl'])) $this->_postErrors[] = $this->l('返回地址不能为空!');

			if (!sizeof($this->_postErrors)) {

				//执行修改操作
				Configuration :: updateValue('OP_SOFORTBANKING_ACCOUNT', strval($_POST['account']));
				Configuration :: updateValue('OP_SOFORTBANKING_SECURECODE', strval($_POST['securecode']));
				Configuration :: updateValue('OP_SOFORTBANKING_TERMINAL', strval($_POST['terminal']));
				Configuration :: updateValue('OP_SOFORTBANKING_SUCCEED_STATES', strval($_POST['succeed_states']));
				Configuration :: updateValue('OP_SOFORTBANKING_FAIL_STATES', strval($_POST['fail_states']));
				Configuration :: updateValue('OP_SOFORTBANKING_HANDLER', strval($_POST['handler']));
				Configuration :: updateValue('OP_SOFORTBANKING_BACK_URL', strval($_POST['backurl']));
				$this->displayConf();
			} else
				$this->displayErrors();
		}

		$this->displayOceanpayment();
		$this->displayFormSettings();
		return $this->_html;
	}
    
   	/**
	*	makeOrderState()
	*	An order state is necessary for this module to function.
	*	The id number of the order state is stored in a global configuration variable for use later
	*/
	private function _makeOrderState()
	{
		if(!(Configuration::get('OP_SOFORTBANKING_ORDER_STATE') > 0))
		{
			$os = new OrderState();
			$os->name = array_fill(0,10,"Awaiting Payment");	//Fill with english language translation
			$os->send_mail = 0;
			$os->template = "";
			$os->invoice = 0;
			$os->color = "#33FF99";
			$os->unremovable = false;
			$os->logable = 0;
			$os->add();
			Configuration::updateValue('OP_SOFORTBANKING_ORDER_STATE',$os->id);
		}
	}

	public function displayConf() {
		$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'
		. $this->l('Confirmation')
		. '" />'
		. $this->l('Settings updated') . '</div>';
	}

	public function displayErrors() {
		$nbErrors = sizeof($this->_postErrors);
		$this->_html .= '<div class="alert error"><h3>'
		. ($nbErrors > 1 ? $this->l('There are') : $this->l('There is')) . ' ' . $nbErrors . ' ' . ($nbErrors > 1 ? $this->l('errors') : $this->l('error')) . '</h3><ol>';
		foreach ($this->_postErrors AS $error)
			$this->_html .= '<li>' . $error . '</li>';
		$this->_html .= '</ol></div>';
	}

	//设置显示logo及提示信息
	public function displayOceanpayment() {
		$this->_html .= '<img src="../modules/OPsofortbanking/payment.jpg" style="float:left; margin-right:15px;height:80px" /><b>'
		. $this->l('This module allows you to accept payments by Oceanpayment.')
		. '</b><br /><br />'
		. $this->l('If the client chooses this payment mode, your Oceanpayment account will be automatically credited.')
		. '<br />'
		. $this->l('You need to configure your Oceanpayment account first before using this module.') . '<div style="clear:both;">&nbsp;</div>';
	}

	//设置后台表单项
	public function displayFormSettings() {
		global $cookie;
		$conf = Configuration :: getMultiple(array (
			'OP_SOFORTBANKING_ACCOUNT',
			'OP_SOFORTBANKING_SECURECODE',
			'OP_SOFORTBANKING_TERMINAL',
			'OP_SOFORTBANKING_SUCCEED_STATES',
			'OP_SOFORTBANKING_FAIL_STATES',
			'OP_SOFORTBANKING_HANDLER',
			'OP_SOFORTBANKING_BACK_URL'
		));
		$account = array_key_exists('account', $_POST) ? $_POST['account'] : (array_key_exists('OP_SOFORTBANKING_ACCOUNT', $conf) ? $conf['OP_SOFORTBANKING_ACCOUNT'] : '');
		$securecode = array_key_exists('securecode', $_POST) ? $_POST['securecode'] : (array_key_exists('OP_SOFORTBANKING_SECURECODE', $conf) ? $conf['OP_SOFORTBANKING_SECURECODE'] : '');
		$terminal = array_key_exists('terminal', $_POST) ? $_POST['terminal'] : (array_key_exists('OP_SOFORTBANKING_TERMINAL', $conf) ? $conf['OP_SOFORTBANKING_TERMINAL'] : '');
		$succeed_states = array_key_exists('succeed_states', $_POST) ? $_POST['succeed_states'] : (array_key_exists('OP_SOFORTBANKING_SUCCEED_STATES', $conf) ? $conf['OP_SOFORTBANKING_SUCCEED_STATES'] :2);
		$fail_states = array_key_exists('fail_states', $_POST) ? $_POST['fail_states'] : (array_key_exists('OP_SOFORTBANKING_FAIL_STATES', $conf) ? $conf['OP_SOFORTBANKING_FAIL_STATES'] :6);
		$handler = array_key_exists('handler', $_POST) ? $_POST['handler'] : (array_key_exists('OP_SOFORTBANKING_HANDLER', $conf) ? $conf['OP_SOFORTBANKING_HANDLER'] : '');
		$backurl = array_key_exists('backurl', $_POST) ? $_POST['backurl'] : (array_key_exists('OP_SOFORTBANKING_BACK_URL', $conf) ? $conf['OP_SOFORTBANKING_BACK_URL'] : '');

		$statesArray = array();
		$states = OrderState::getOrderStates((int)($cookie->id_lang));
		$succeed_states_string = '';
		$fail_states_string = '';
		
		foreach ($states AS $state)
			$succeed_states_string .= "<option value='".$state['id_order_state']."' ".($succeed_states==$state['id_order_state']?"selected='selected'":"").">".$state['name']."</option>";
		foreach ($states AS $state)
			$fail_states_string .= "<option value='".$state['id_order_state']."' ".($fail_states==$state['id_order_state']?"selected='selected'":"").">".$state['name']."</option>";
		
		$this->_html .= '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" style="clear: both;"><fieldset><legend><img src="../img/admin/contact.gif" />'
		. $this->l('Settings')
		. '</legend><label>'
		. $this->l('Account')
		. '</label><div class="margin-form"><input type="text" size="33" name="account" value="'
		. htmlentities($account, ENT_COMPAT, 'UTF-8')
		. '" /></div><label>'
		. $this->l('Terminal')
		. '</label><div class="margin-form"><input type="text" size="33" name="terminal" value="'
		. htmlentities($terminal, ENT_COMPAT, 'UTF-8')
		. '" /></div><label>'
		. $this->l('SecureCode')
		. '</label><div class="margin-form"><input type="text" size="33" name="securecode" value="'
		. htmlentities($securecode, ENT_COMPAT, 'UTF-8')
		. '" /></div><label>'
		. $this->l('Succeed States')
		. '</label><div class="margin-form"><select name="succeed_states">'
		. $succeed_states_string
		. '</select></div><label>'
		. $this->l('Fail States')
		. '</label><div class="margin-form"><select name="fail_states">'
		. $fail_states_string
		. '</select></div><label>'
		. $this->l('Transaction URL')
		. '</label><div class="margin-form"><input type="text" size="82" name="handler" value="'
		. htmlentities($handler, ENT_COMPAT, 'UTF-8')
		. '" /></div><label>'
		. $this->l('Return URL')
		. '</label><div class="margin-form"><input type="text" size="82" name="backurl" value="'
		. htmlentities($backurl, ENT_COMPAT, 'UTF-8')
		. '" /></div>'
		. '<br /><center><input type="submit" name="submitOceanpayment" value="'
		. $this->l('Update settings')
		. '" class="btn" /></center></fieldset></form><br /><br />';

	}

	//前台支付提交界面
	public function execPayment($cart) {
		if (!$this->active)
			return;
		global $smarty;
		$currency=$this->getCurrency();
// 		$Amount = floatval(Tools::convertPrice(floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', '')), $currency));
// 		$currency = new Currency($cart->id_currency);
		$this->validateOrder($cart->id, Configuration::get('OP_SOFORTBANKING_ORDER_STATE'), $cart->getOrderTotal(),$this->displayName);
		//securecode密匙
		$securecode = Configuration :: get('OP_SOFORTBANKING_SECURECODE');
		//账户
		$account = Configuration :: get('OP_SOFORTBANKING_ACCOUNT');
		//终端号
		$terminal = Configuration :: get('OP_SOFORTBANKING_TERMINAL');
		//交易金额
		$order_amount = $cart->getOrderTotal();
        //商户订单号
		$order_number = $this->currentOrder;
		//交易币种
        $order_currency = $currency->iso_code;
        //交易返回地址
        $backUrl = Configuration :: get('OP_SOFORTBANKING_BACK_URL');
        //服务器响应地址
        $noticeUrl = 'http://' . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__.'modules/OPsofortbanking/payment_notice.php';
        //备注
        $order_notes = '';
        //提交地址
		$handler = Configuration :: get('OP_SOFORTBANKING_HANDLER');

		$shippingAddress=new Address(intval($cart->id_address_delivery));
		$customer = new Customer(intval($cart->id_customer));
		//支付方式
		$methods = 'Directpay';
		//客人的名
		$billing_firstName = empty ($shippingAddress->firstname) ? '' : $this->OceanHtmlSpecialChars($shippingAddress->firstname);
		//客人的姓
		$billing_lastName = empty ($shippingAddress->lastname) ? '' : $this->OceanHtmlSpecialChars($shippingAddress->lastname);
		//客人的邮件
		$billing_email = empty ($customer->email) ? '' : $customer->email;
		//客人的联系电话
		$billing_phone = empty ($shippingAddress->phone_mobile) ? $shippingAddress->phone : $shippingAddress->phone_mobile;
		//客人的邮编
		$billing_zip = empty ($shippingAddress->postcode) ? 999999 : $shippingAddress->postcode;
		//客人的地址
		$billing_address = empty ($shippingAddress->address1) ? '' : $shippingAddress->address1;
		//客人的城市
		$billing_city = empty ($shippingAddress->city) ? '' : $shippingAddress->city;
		//客人的省或州
		$billing_state = empty ($address->id_state) ? '' : State::getNameById($address->id_state);
		//客人的国家
		$billing_country = empty ($shippingAddress->country) ? '' : $shippingAddress->country;
		//购物车类型
		$cart_info='prestashop';
		//版本信息
		$cart_api='V1.7.1';

		//组合加密项
		$signsrc  = $account.$terminal.$backUrl.$order_number.$order_currency.$order_amount.$billing_firstName.$billing_lastName.$billing_email.$securecode;
		//sha256加密
		$signValue  = hash("sha256",$signsrc);
		
		//手机号码特殊处理
		$phone_mobile = empty ($shippingAddress->phone_mobile) ? '' : $shippingAddress->phone_mobile;
		$phone = empty ($shippingAddress->phone) ? '' : $shippingAddress->phone;
		
		if ($phone_mobile == '' || $phone == ''){
			$order_notes = '';
		}else{
			$order_notes = 'Home phone:'.$phone;
		}

		//记录发送到oceanpayment的post log
	    $filedate = date('Y-m-d');
	    
	    $postdate = date('Y-m-d H:i:s');
	    
	    $newfile  = fopen( "oceanpayment_log/" . $filedate . ".log", "a+" );
	    
	    $post_log = $postdate."[POST to Oceanpayment]\r\n" . 
	 	            "account = "           .$account . "\r\n".
	                "terminal = "          .$terminal . "\r\n".
         	        "backUrl = "           .$backUrl . "\r\n".
         	        "noticeUrl = "         .$noticeUrl . "\r\n".
         	        "order_number = "      .$order_number . "\r\n".
         	        "order_currency = "    .$order_currency . "\r\n".
         	        "order_amount = "      .$order_amount . "\r\n".
         	        "billing_firstName = " .$billing_firstName . "\r\n".
         	        "billing_lastName = "  .$billing_lastName . "\r\n".
         	        "billing_email = "     .$billing_email . "\r\n".
         	        "billing_phone = "     .$billing_phone . "\r\n".
         	        "billing_country = "   .$billing_country . "\r\n".
         	        "billing_state = "     .$billing_state . "\r\n".
         	        "billing_city = "      .$billing_city . "\r\n".
         	        "billing_address = "   .$billing_address . "\r\n".
         	        "billing_zip = "       .$billing_zip . "\r\n".
         	        "methods = "           .$methods . "\r\n".
         	        "signValue = "         .$signValue . "\r\n".
         	        "cart_info = "         .$cart_info . "\r\n".
					"cart_api = "          .$cart_api . "\r\n".
					"order_notes = "       .$order_notes . "\r\n";
	    
	    $post_log = $post_log . "*************************************\r\n";
	    
	    $post_log = $post_log.file_get_contents( "oceanpayment_log/" . $filedate . ".log");
	    
	    $filename = fopen( "oceanpayment_log/" . $filedate . ".log", "r+" );
	    
	    fwrite($filename,$post_log);
	    
	    fclose($filename);
	    
	    fclose($newfile);
		
		
		$smarty->assign(array (
						'handler' => $handler,
						'account' => $account,
						'terminal' => $terminal,
						'order_number'=>$order_number,
						'order_currency'=>$order_currency,
						'order_amount'=>$order_amount,
						'backUrl'=>$backUrl,
						'noticeUrl'=>$noticeUrl,
						'billing_firstName'=>$billing_firstName,
						'billing_lastName'=>$billing_lastName,
						'billing_email'=>$billing_email,
						'billing_phone'=>$billing_phone,
						'billing_country'=>$billing_country,
						'billing_state'=>$billing_state,
						'billing_city'=>$billing_city,
						'billing_address'=>$billing_address,
						'billing_zip'=>$billing_zip,
						'order_notes'=>$order_notes,
						'methods'=>$methods,
						'signValue'=>$signValue,
						'cart_info'=>$cart_info,
						'cart_api'=>$cart_api
		    ));

		return $this->display(__FILE__, 'payment_commit.tpl');
	}

	//前台支付方式列表界面
	public function hookPayment($params) {

		if (!$this->active)
			return;

		global $smarty;

		$this_path_ssl = 'http://' . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__ . 'modules/OPsofortbanking/';

		$smarty->assign(array (
			'this_path_ssl' => $this_path_ssl
		));

		return $this->display(__FILE__, 'Sofortbanking.tpl');
	}

	public function hookPaymentReturn($params) {
		if (!$this->active)
			return;

		return $this->display(__FILE__, 'confirmation.tpl');
	}

	public function getL($key) {
		$translations = array (
			'mc_gross' => $this->l('Oceanpayment key \'mc_gross\' not specified, can\'t control amount paid.'
		), 'payment_status' => $this->l('Oceanpayment key \'payment_status\' not specified, can\'t control payment validity'), 'payment' => $this->l('Payment: '), 'custom' => $this->l('Oceanpayment key \'custom\' not specified, can\'t rely to cart'), 'txn_id' => $this->l('Oceanpayment key \'txn_id\' not specified, transaction unknown'), 'mc_currency' => $this->l('Oceanpayment key \'mc_currency\' not specified, currency unknown'), 'cart' => $this->l('Cart not found'), 'order' => $this->l('Order has already been placed'), 'transaction' => $this->l('Oceanpayment Transaction ID: '), 'verified' => $this->l('The Oceanpayment transaction could not be VERIFIED.'), 'connect' => $this->l('Problem connecting to the Oceanpayment server.'), 'nomethod' => $this->l('No communications transport available.'), 'socketmethod' => $this->l('Verification failure (using fsockopen). Returned: '), 'curlmethod' => $this->l('Verification failure (using cURL). Returned: '), 'curlmethodfailed' => $this->l('Connection using cURL failed'),);
		return $translations[$key];
	}

	function validateOrder($id_cart, $id_order_state, $amountPaid, $paymentMethod = 'Unknown', $message = NULL, $extraVars = array (), $currency_special = NULL, $dont_touch_amount = false) {
		if (!$this->active)
			return;

		$currency = $this->getCurrency();
		$cart = new Cart(intval($id_cart));
		$currency = new Currency($cart->id_currency);
		$cart->save();
		parent :: validateOrder($id_cart, $id_order_state, $amountPaid, $paymentMethod, $message, $extraVars, $currency_special, true);
	}
	
	private function _getPriceET()
	{
		$row2 = Db::getInstance()->getRow('
				SELECT MAX(`price`) AS `max_price`, MIN(`price`) AS `min_price`
				FROM `'._DB_PREFIX_.'product`');
		return $row2;
	}
	
	/**
	 * 钱海支付Html特殊字符转义
	 */
	function OceanHtmlSpecialChars($parameter){
	
		//去除前后空格
		$parameter = trim($parameter);
	
		//转义"双引号,<小于号,>大于号,'单引号
		$parameter = str_replace(array("<",">","'","\""),array("&lt;","&gt;","&#039;","&quot;"),$parameter);
	
		return $parameter;
	
	}
	
}
