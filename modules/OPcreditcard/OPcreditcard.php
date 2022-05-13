<?php
class OPcreditcard extends PaymentModule {
	private $_html = '';
	private $_postErrors = array ();

	public function __construct() {
		$this->name = 'OPcreditcard';
		//标记模块类型
		$this->tab = 'payments_gateways';
		$this->version = '1.7.1';
        
		if (!Configuration::get('OP_CREDITCARD_ORDER_STATE'))				//If, for some reason, there are no currencies, make them
			$this->_makeOrderState();

        $this->idOrderState = Configuration::get('OP_CREDITCARD_ORDER_STATE');
		$this->currencies     = true;
		$this->currencies_mode = 'radio';

		parent::__construct();

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Oceanpayment - CreditCard');
		$this->description = $this->l('Accepts payments by Oceanpayment');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
	}

	public function getOceanpaymentUrl() {
		$OP_CREDITCARD_url = "https://secure.oceanpayment.com/gateway/service/test";
		return $OP_CREDITCARD_url;
	}

	/**
	 * 安装
	 */
	public function install() {
		//支付地址(正式)
        $this->_makeOrderState();
		$action_URL="https://secure.oceanpayment.com/gateway/service/test";
		$back_url='http://' . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__.'modules/OPcreditcard/payment_result.php';

		if (!Configuration :: updateValue('OP_CREDITCARD_SUCCEED_STATES', '2') OR
			!Configuration :: updateValue('OP_CREDITCARD_FAIL_STATES', '6') OR 
			!Configuration :: updateValue('OP_CREDITCARD_PENDING_STATES', '2') OR 	
			!Configuration :: updateValue('OP_CREDITCARD_ACCOUNT', '') OR 
			!Configuration :: updateValue('OP_CREDITCARD_TERMINAL', '') OR
			!Configuration :: updateValue('OP_CREDITCARD_SECURECODE', '') OR
			!Configuration :: updateValue('OP_CREDITCARD_PAY_MODE', '1') OR 
			!Configuration :: updateValue('OP_CREDITCARD_SECURE_MODE', '0') OR 
			!Configuration :: updateValue('OP_CREDITCARD_SECURE_TERMINAL', '') OR
			!Configuration :: updateValue('OP_CREDITCARD_SECURE_SECURECODE', '') OR
			!Configuration :: updateValue('OP_CREDITCARD_SECURE_CURRENCY', '') OR
			!Configuration :: updateValue('OP_CREDITCARD_SECURE_AMOUNT', '') OR
			!Configuration :: updateValue('OP_CREDITCARD_HANDLER', $action_URL) OR 
			!Configuration :: updateValue('OP_CREDITCARD_BACK_URL', $back_url) OR 
            		!Configuration :: updateValue('OP_CREDITCARD_WEIRE_LOG', '1') OR
			!parent :: install() OR
			!$this->registerHook('payment') OR 
			!$this->registerHook('paymentReturn'))
			return false;
		return true;
	}

	/**
	 * 卸载
	 */
	public function uninstall() {
		if (!Configuration :: deleteByName('OP_CREDITCARD_SUCCEED_STATES') OR 
			!Configuration :: deleteByName('OP_CREDITCARD_FAIL_STATES') OR 
			!Configuration :: deleteByName('OP_CREDITCARD_PENDING_STATES') OR 
			!Configuration :: deleteByName('OP_CREDITCARD_ACCOUNT') OR 
			!Configuration :: deleteByName('OP_CREDITCARD_TERMINAL') OR
			!Configuration :: deleteByName('OP_CREDITCARD_SECURECODE') OR
		 	!Configuration :: deleteByName('OP_CREDITCARD_PAY_MODE') OR 
			!Configuration :: deleteByName('OP_CREDITCARD_SECURE_MODE') OR 
			!Configuration :: deleteByName('OP_CREDITCARD_SECURE_TERMINA') OR
			!Configuration :: deleteByName('OP_CREDITCARD_SECURE_SECURECODE') OR
			!Configuration :: deleteByName('OP_CREDITCARD_SECURE_CURRENCY') OR
			!Configuration :: deleteByName('OP_CREDITCARD_SECURE_AMOUNT') OR
			!Configuration :: deleteByName('OP_CREDITCARD_HANDLER') OR 
			!Configuration :: deleteByName('OP_CREDITCARD_BACK_URL') OR 
            		!Configuration :: deleteByName('OP_CREDITCARD_WEIRE_LOG') OR
			!parent :: uninstall())
			return false;
		return true;
	}

	
	/**
	 *	makeOrderState()
	 *	An order state is necessary for this module to function.
	 *	The id number of the order state is stored in a global configuration variable for use later
	 */
	private function _makeOrderState()
	{
		if(!(Configuration::get('OP_CREDITCARD_ORDER_STATE') > 0))
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
			Configuration::updateValue('OP_CREDITCARD_ORDER_STATE',$os->id);
		}
	}
	
	//成功提示文字
	public function displayConf() {
		$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'
		. $this->l('Confirmation')
		. '" />'
		. $this->l('Settings updated') . '</div>';
	}
	
	//错误提示文字
	public function displayErrors() {
		$nbErrors = sizeof($this->_postErrors);
		$this->_html .= '<div class="alert error"><h3>'
		. ($nbErrors > 1 ? $this->l('There are') : $this->l('There is')) . ' ' . $nbErrors . ' ' . ($nbErrors > 1 ? $this->l('errors') : $this->l('error')) . '</h3><ol>';
		foreach ($this->_postErrors AS $error)
			$this->_html .= '<li>' . $error . '</li>';
		$this->_html .= '</ol></div>';
	}
	
	//表单验证文字
	public function getContent() {
		$this->_html = '<h2>Oceanpayment</h2>';
		if (isset ($_POST['submitOceanpayment'])) {
			if (empty ($_POST['account']))$this->_postErrors[] = $this->l('账户不能为空!');
			elseif (empty ($_POST['securecode'])) $this->_postErrors[] = $this->l('securecode不能为空!');
			elseif (empty ($_POST['terminal'])) $this->_postErrors[] = $this->l('终端号不能为空!');
			elseif (empty ($_POST['succeed_states'])) $this->_postErrors[] = $this->l('成功状态不能为空!');
			elseif (empty ($_POST['fail_states'])) $this->_postErrors[] = $this->l('失败状态不能为空!');
			elseif (empty ($_POST['pending_states'])) $this->_postErrors[] = $this->l('待处理状态不能为空!');
			elseif (empty ($_POST['handler'])) $this->_postErrors[] = $this->l('提交地址不能为空!');
			elseif (empty ($_POST['backurl'])) $this->_postErrors[] = $this->l('返回地址不能为空!');

			if (!sizeof($this->_postErrors)) {
				//执行修改操作
				Configuration :: updateValue('OP_CREDITCARD_ACCOUNT', strval($_POST['account']));
				Configuration :: updateValue('OP_CREDITCARD_TERMINAL', strval($_POST['terminal']));
				Configuration :: updateValue('OP_CREDITCARD_SECURECODE', strval($_POST['securecode']));
				Configuration :: updateValue('OP_CREDITCARD_PAY_MODE', strval($_POST['pay_mode']));
				Configuration :: updateValue('OP_CREDITCARD_SUCCEED_STATES', strval($_POST['succeed_states']));
				Configuration :: updateValue('OP_CREDITCARD_FAIL_STATES', strval($_POST['fail_states']));
				Configuration :: updateValue('OP_CREDITCARD_PENDING_STATES', strval($_POST['pending_states']));
				Configuration :: updateValue('OP_CREDITCARD_SECURE_MODE', strval($_POST['secure_mode']));
				Configuration :: updateValue('OP_CREDITCARD_SECURE_TERMINAL', strval($_POST['secure_terminal']));
				Configuration :: updateValue('OP_CREDITCARD_SECURE_SECURECODE', strval($_POST['secure_securecode']));
				Configuration :: updateValue('OP_CREDITCARD_SECURE_CURRENCY', strval($_POST['secure_currency']));
				Configuration :: updateValue('OP_CREDITCARD_SECURE_AMOUNT', strval($_POST['secure_amount']));
				Configuration :: updateValue('OP_CREDITCARD_HANDLER', strval($_POST['handler']));
				Configuration :: updateValue('OP_CREDITCARD_BACK_URL', strval($_POST['backurl']));
				Configuration :: updateValue('OP_CREDITCARD_WEIRE_LOG', strval($_POST['logs']));
				$this->displayConf();
			} else
				$this->displayErrors();
		}

		$this->displayOceanpayment();
		$this->displayFormSettings();
		return $this->_html;
	}
    
   	

	//设置显示logo及提示信息
	public function displayOceanpayment() {
		$this->_html .= '<img src="../modules/OPcreditcard/op_creditcard.png" style="float:left; margin:10px 15px 10px 0;" /><b>'
		. $this->l('This module allows you to accept payments by Oceanpayment.')
		. '</b><br />'
		. $this->l('If the client chooses this payment mode, your Oceanpayment account will be automatically credited.')
		. '<br />'
		. $this->l('You need to configure your Oceanpayment account first before using this module.') . '<div style="clear:both;">&nbsp;</div>';
	}

	//设置后台表单项
	public function displayFormSettings() {
		global $cookie;
		$conf = Configuration :: getMultiple(array (
			'OP_CREDITCARD_ACCOUNT',
			'OP_CREDITCARD_TERMINAL',
			'OP_CREDITCARD_SECURECODE',
			'OP_CREDITCARD_PAY_MODE',
			'OP_CREDITCARD_SUCCEED_STATES',
			'OP_CREDITCARD_FAIL_STATES',
			'OP_CREDITCARD_PENDING_STATES',
			'OP_CREDITCARD_SECURE_MODE',
			'OP_CREDITCARD_SECURE_TERMINAL',
			'OP_CREDITCARD_SECURE_SECURECODE',
			'OP_CREDITCARD_SECURE_CURRENCY',
			'OP_CREDITCARD_SECURE_AMOUNT',
			'OP_CREDITCARD_HANDLER',
			'OP_CREDITCARD_BACK_URL',
            		'OP_CREDITCARD_WEIRE_LOG'
		));
		$account = array_key_exists('account', $_POST) ? $_POST['account'] : (array_key_exists('OP_CREDITCARD_ACCOUNT', $conf) ? $conf['OP_CREDITCARD_ACCOUNT'] : '');
		$securecode = array_key_exists('securecode', $_POST) ? $_POST['securecode'] : (array_key_exists('OP_CREDITCARD_SECURECODE', $conf) ? $conf['OP_CREDITCARD_SECURECODE'] : '');
		$terminal = array_key_exists('terminal', $_POST) ? $_POST['terminal'] : (array_key_exists('OP_CREDITCARD_TERMINAL', $conf) ? $conf['OP_CREDITCARD_TERMINAL'] : '');
		$pay_mode = array_key_exists('pay_mode', $_POST) ? $_POST['pay_mode'] : (array_key_exists('OP_CREDITCARD_PAY_MODE', $conf) ? $conf['OP_CREDITCARD_PAY_MODE'] : 1);
		$succeed_states = array_key_exists('succeed_states', $_POST) ? $_POST['succeed_states'] : (array_key_exists('OP_CREDITCARD_SUCCEED_STATES', $conf) ? $conf['OP_CREDITCARD_SUCCEED_STATES'] :2);
		$fail_states = array_key_exists('fail_states', $_POST) ? $_POST['fail_states'] : (array_key_exists('OP_CREDITCARD_FAIL_STATES', $conf) ? $conf['OP_CREDITCARD_FAIL_STATES'] :6);
		$pending_states = array_key_exists('pending_states', $_POST) ? $_POST['pending_states'] : (array_key_exists('OP_CREDITCARD_PENDING_STATES', $conf) ? $conf['OP_CREDITCARD_PENDING_STATES'] :2);
		$secure_mode = array_key_exists('secure_mode', $_POST) ? $_POST['secure_mode'] : (array_key_exists('OP_CREDITCARD_SECURE_MODE', $conf) ? $conf['OP_CREDITCARD_SECURE_MODE'] : 0);
		$secure_terminal = array_key_exists('secure_terminal', $_POST) ? $_POST['secure_terminal'] : (array_key_exists('OP_CREDITCARD_SECURE_TERMINAL', $conf) ? $conf['OP_CREDITCARD_SECURE_TERMINAL'] : '');
		$secure_securecode = array_key_exists('secure_securecode', $_POST) ? $_POST['secure_securecode'] : (array_key_exists('OP_CREDITCARD_SECURE_SECURECODE', $conf) ? $conf['OP_CREDITCARD_SECURE_SECURECODE'] : '');
		$secure_currency = array_key_exists('secure_currency', $_POST) ? $_POST['secure_currency'] : (array_key_exists('OP_CREDITCARD_SECURE_CURRENCY', $conf) ? $conf['OP_CREDITCARD_SECURE_CURRENCY'] : '');
		$secure_amount = array_key_exists('secure_amount', $_POST) ? $_POST['secure_amount'] : (array_key_exists('OP_CREDITCARD_SECURE_AMOUNT', $conf) ? $conf['OP_CREDITCARD_SECURE_AMOUNT'] : '');
		$handler = array_key_exists('handler', $_POST) ? $_POST['handler'] : (array_key_exists('OP_CREDITCARD_HANDLER', $conf) ? $conf['OP_CREDITCARD_HANDLER'] : '');
		$backurl = array_key_exists('backurl', $_POST) ? $_POST['backurl'] : (array_key_exists('OP_CREDITCARD_BACK_URL', $conf) ? $conf['OP_CREDITCARD_BACK_URL'] : '');
		$logs_mode = array_key_exists('logs', $_POST) ? $_POST['logs'] : (array_key_exists('OP_CREDITCARD_WEIRE_LOG', $conf) ? $conf['OP_CREDITCARD_WEIRE_LOG'] : 1);
		
		$statesArray = array();
		$states = OrderState::getOrderStates((int)($cookie->id_lang));
		$succeed_states_string = '';
		$fail_states_string = '';
		$pending_states_string = '';
		
		$pay_modes = array(1 => 'Iframe', 0 => 'Redirect');
		$secure_modes = array(0 => 'Off', 1 => 'On');
        	$logs_modes = array(1 => 'On', 0 => 'Off');
		
		foreach ($pay_modes AS $val => $mode)
			$pay_mode_string .= "<option value='".$val."' ".($pay_mode==$val?"selected='selected'":"").">".$mode."</option>";
		foreach ($states AS $state)
			$succeed_states_string .= "<option value='".$state['id_order_state']."' ".($succeed_states==$state['id_order_state']?"selected='selected'":"").">".$state['name']."</option>";
		foreach ($states AS $state)
			$fail_states_string .= "<option value='".$state['id_order_state']."' ".($fail_states==$state['id_order_state']?"selected='selected'":"").">".$state['name']."</option>";
		foreach ($states AS $state)
			$pending_states_string .= "<option value='".$state['id_order_state']."' ".($pending_states==$state['id_order_state']?"selected='selected'":"").">".$state['name']."</option>";
		foreach ($secure_modes AS $val => $mode)
			$secure_modes_string .= "<option value='".$val."' ".($secure_mode==$val?"selected='selected'":"").">".$mode."</option>";
		foreach ($logs_modes AS $val => $mode)
            		$write_logs_string .= "<option value='".$val."' ".($logs_mode==$val?"selected='selected'":"").">".$mode."</option>";
		
		$this->_html .= '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post" style="clear: both;"><fieldset><legend><img src="../img/admin/contact.gif" />'
		. $this->l('Settings') . '</legend>'
		
		. '<label>'. $this->l('Account') . '</label>'
		. '<div class="margin-form"><input type="text" size="6" name="account" value="'. htmlentities($account, ENT_COMPAT, 'UTF-8'). '" /></div>'
		
		. '<label>'. $this->l('Terminal') . '</label>'
		. '<div class="margin-form"><input type="text" size="8" name="terminal" value="'. htmlentities($terminal, ENT_COMPAT, 'UTF-8'). '" /></div>'
		
		. '<label>'. $this->l('SecureCode') . '</label>'
		. '<div class="margin-form"><input type="text" size="8" name="securecode" value="'. htmlentities($securecode, ENT_COMPAT, 'UTF-8'). '" /></div>'
		
		. '<label>'. $this->l('Pay Mode') . '</label>'
		. '<div class="margin-form"><select name="pay_mode">'. $pay_mode_string . '</select></div>'
		
		. '<label>'. $this->l('Succeed States') . '</label>'
		. '<div class="margin-form"><select name="succeed_states">'. $succeed_states_string . '</select></div>'
		
		. '<label>'. $this->l('Fail States') . '</label>'
		. '<div class="margin-form"><select name="fail_states">'. $fail_states_string. '</select></div>'
		
		. '<label>'. $this->l('Pending States') . '</label>'
		. '<div class="margin-form"><select name="pending_states">'. $pending_states_string. '</select></div>'
		
		. '<label>'. $this->l('3D Secure mode') . '</label>'
		. '<div class="margin-form"><select name="secure_mode">'. $secure_modes_string . '</select></div>'
				
		. '<label>'. $this->l('3D Secure Terminal') . '</label>'
		. '<div class="margin-form"><input type="text" size="8" name="secure_terminal" value="'. htmlentities($secure_terminal, ENT_COMPAT, 'UTF-8'). '" /></div>'
		
		. '<label>'. $this->l('3D Secure SecureCode') . '</label>'
		. '<div class="margin-form"><input type="text" size="8" name="secure_securecode" value="'. htmlentities($secure_securecode, ENT_COMPAT, 'UTF-8'). '" /></div>'

		. '<label>'. $this->l('3D Secure Currency') . '</label>'
		. '<div class="margin-form"><input type="text" name="secure_currency" value="'. htmlentities($secure_currency, ENT_COMPAT, 'UTF-8'). '" /></div>'
				
		. '<label>'. $this->l('3D Secure Amount') . '</label>'
		. '<div class="margin-form"><input type="text" name="secure_amount" value="'. htmlentities($secure_amount, ENT_COMPAT, 'UTF-8'). '" /></div>'
					
		. '<label>'. $this->l('Transaction URL') . '</label>'
		. '<div class="margin-form"><input type="text" size="82" name="handler" value="'. htmlentities($handler, ENT_COMPAT, 'UTF-8'). '" /></div>'
		
		. '<label>'. $this->l('Return URL') . '</label>'
		. '<div class="margin-form"><input type="text" size="82" name="backurl" value="'. htmlentities($backurl, ENT_COMPAT, 'UTF-8'). '" /></div>'
		
		. '<label>'. $this->l('Write The Logs') . '</label>'
		. '<div class="margin-form"><select name="logs">'. $write_logs_string . '</select></div>'
			
		. '<br /><center><input type="submit" name="submitOceanpayment" value="'. $this->l('Update settings'). '" class="btn" /></center></fieldset></form>';

	}

	//前台支付提交界面
	public function execPayment($cart) {
		if (!$this->active)
			return;
		global $smarty;
		$currency=$this->getCurrency();
		
		$this->validateOrder($cart->id, Configuration::get('OP_CREDITCARD_ORDER_STATE'), $cart->getOrderTotal(),$this->displayName);
		
		
		$billingAddress=new Address(intval($cart->id_address_invoice));
		$shippingAddress=new Address(intval($cart->id_address_delivery));
		$customer = new Customer(intval($cart->id_customer));
		$productDetails = $this->getProductItems($cart->getProducts());
		
		//交易金额
		$order_amount = $cart->getOrderTotal();
		//交易币种
		$order_currency = $currency->iso_code;
		
		//初始化是否3D交易
		$_SESSION['is_3d'] = 0;
		//判断是否启用3D功能
		if(Configuration :: get('OP_CREDITCARD_SECURE_MODE') == 1){
			//检验是否需要3D验证
			$validate_arr = $this->validate3D($order_currency, $order_amount);
		}else{
			$validate_arr['terminal'] = Configuration :: get('OP_CREDITCARD_TERMINAL');
			$validate_arr['securecode'] = Configuration :: get('OP_CREDITCARD_SECURECODE');
		}
		
		
		//提交地址
		$handler = Configuration :: get('OP_CREDITCARD_HANDLER');
		//账户
		$account = Configuration :: get('OP_CREDITCARD_ACCOUNT');
		//终端号
		$terminal = $validate_arr['terminal'];
		//securecode密匙
		$securecode = $validate_arr['securecode'];
        //商户订单号
        $order_number = $this->currentOrder;
        //交易返回地址
        $backUrl = Configuration :: get('OP_CREDITCARD_BACK_URL');
        //服务器响应地址
        $noticeUrl = 'http://' . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__.'modules/OPcreditcard/payment_notice.php';
        //备注
        $order_notes = '';
		//支付方式
		$methods = 'Credit Card';
		//客人的名
		$billing_firstName = empty ($billingAddress->firstname) ? '' : $this->OceanHtmlSpecialChars($billingAddress->firstname);
		//客人的姓
		$billing_lastName = empty ($billingAddress->lastname) ? '' : $this->OceanHtmlSpecialChars($billingAddress->lastname);
		//客人的邮件
		$billing_email = empty ($customer->email) ? '' : $this->OceanHtmlSpecialChars($customer->email);
		//客人的联系电话
		$billing_phone = empty ($billingAddress->phone_mobile) ? (empty ($billingAddress->phone) ? 999999 : $billingAddress->phone) : $billingAddress->phone_mobile;
		//客人的国家
		$billing_country = empty ($billingAddress->country) ? '' : $billingAddress->country;
		//客人的省或州
		$billing_state = empty ($address->id_state) ? '' : State::getNameById($address->id_state);
		//客人的城市
		$billing_city = empty ($billingAddress->city) ? '' : $billingAddress->city;
		//客人的地址
		$billing_address = empty ($billingAddress->address1) ? '' : $billingAddress->address1;
		//客人的邮编
		$billing_zip = empty ($billingAddress->postcode) ? 999999 : $billingAddress->postcode;
		//收货人地址信息
		//收货人名
		$ship_firstName = empty ($shippingAddress->firstname) ? '' : $this->OceanHtmlSpecialChars($shippingAddress->firstname);
		//收货人姓
		$ship_lastName = empty ($shippingAddress->lastname) ? '' : $this->OceanHtmlSpecialChars($shippingAddress->lastname);
		//收货人手机
		$ship_phone = empty ($shippingAddress->phone_mobile) ? (empty ($shippingAddress->phone) ? 999999 : $shippingAddress->phone) : $shippingAddress->phone_mobile;
		//收货人国家
		$ship_country = empty ($shippingAddress->country) ? '' : $shippingAddress->country;
		//收货人州
		$ship_state = empty ($address->id_state) ? '' : State::getNameById($address->id_state);
		//收货人城市
		$ship_city = empty ($shippingAddress->city) ? '' : $shippingAddress->city;
		//收货人地址
		$ship_addr = empty ($shippingAddress->address1) ? '' : $shippingAddress->address1;
		//收货人邮编
		$ship_zip = empty ($shippingAddress->postcode) ? 999999 : $shippingAddress->postcode;
		//产品名称
		$productName = $productDetails['productName'];
		//产品SKU
		$productSku = $productDetails['productSku'];
		//产品数量
		$productNum = $productDetails['productNum'];
		//购物车类型
		$cart_info = 'prestashop';
		//版本信息
		$cart_api = 'V1.7.1';
		//组合加密项
		$signsrc  = $account.$terminal.$backUrl.$order_number.$order_currency.$order_amount.$billing_firstName.$billing_lastName.$billing_email.$securecode;
		//sha256加密
		$signValue  = hash("sha256",$signsrc);
		//支付页面类型	
		include_once(dirname(__FILE__).'/MobileDetect.php');
		$detect = new MobileDetect();
		if($detect->isiOS()){
			$pages = 1;
		}elseif($detect->isMobile()){
			$pages = 1;
		}elseif($detect->isTablet()){
			$pages = 0;
		}else{
			$pages = 0;
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
         	        "methods = "           .$methods . "\r\n".
         	        "signValue = "         .$signValue . "\r\n".
         	        "billing_firstName = " .$billing_firstName . "\r\n".
         	        "billing_lastName = "  .$billing_lastName . "\r\n".
         	        "billing_email = "     .$billing_email . "\r\n".
         	        "billing_phone = "     .$billing_phone . "\r\n".
         	        "billing_country = "   .$billing_country . "\r\n".
         	        "billing_state = "     .$billing_state . "\r\n".
         	        "billing_city = "      .$billing_city . "\r\n".
         	        "billing_address = "   .$billing_address . "\r\n".
         	        "billing_zip = "       .$billing_zip . "\r\n".
         	        "ship_firstName = "    .$ship_firstName . "\r\n".
         	        "ship_lastName = "     .$ship_lastName . "\r\n".
         	        "ship_phone = "        .$ship_phone . "\r\n".
         	        "ship_country = "      .$ship_country . "\r\n".
         	        "ship_state = "        .$ship_state . "\r\n".
         	        "ship_city = "     	   .$ship_city . "\r\n".
         	        "ship_addr = "   	   .$ship_addr . "\r\n".
         	        "ship_zip = "     	   .$ship_zip . "\r\n".
         	        "productName = "       .$productName . "\r\n".
         	        "productSku = "        .$productSku . "\r\n".
         	        "productNum = "        .$productNum . "\r\n".
         	        "cart_info = "         .$cart_info . "\r\n".
					"cart_api = "          .$cart_api . "\r\n".
					"pages = "             .$pages . "\r\n".
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
						'ship_firstName'=>$ship_firstName,
						'ship_lastName'=>$ship_lastName,
						'ship_phone'=>$ship_phone,
						'ship_country'=>$ship_country,
						'ship_state'=>$ship_state,
						'ship_city'=>$ship_city,
						'ship_addr'=>$ship_addr,
						'ship_zip'=>$ship_zip,
						'productName'=>$productName,
						'productSku'=>$productSku,
						'productNum'=>$productNum,
						'order_notes'=>$order_notes,
						'methods'=>$methods,
						'signValue'=>$signValue,
						'cart_info'=>$cart_info,
						'cart_api'=>$cart_api,
						'pages'=>$pages,
		    ));

		
		$pay_mode = Configuration :: get('OP_CREDITCARD_PAY_MODE');
		
		if($pay_mode == 1){
			return $this->display(__FILE__, 'payment.tpl');
		}elseif($pay_mode == 0){
			return $this->display(__FILE__, 'payment_iframe.tpl');
		}
		
	}

	//前台支付方式列表界面
	public function hookPayment($params) {

		if (!$this->active)
			return;

		global $smarty;

		$this_path_ssl = 'http://' . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__ . 'modules/OPcreditcard/';

		$smarty->assign(array (
			'this_path_ssl' => $this_path_ssl
		));

		return $this->display(__FILE__, 'CreditCard.tpl');
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
	 * 检验是否需要3D验证
	 */
	public function validate3D($order_currency, $order_amount){
	
		//是否需要3D验证
		$is_3d = 0;
		 
		//获取3D功能下各个的币种
		$currencies_value_str = Configuration :: get('OP_CREDITCARD_SECURE_CURRENCY');
		$currencies_value = explode(';', $currencies_value_str);
		//获取3D功能下各个的金额
		$amount_value_str = Configuration :: get('OP_CREDITCARD_SECURE_AMOUNT');
		$amount_value = explode(';', $amount_value_str);
		 
		$amountValidate = array_combine($currencies_value, $amount_value);
		 
		if($amountValidate){
			//判断金额是否为空
			if(isset($amountValidate[$order_currency])){
				//判断3D金额不为空
				//判断订单金额是否大于3d设定值
				if($order_amount >= $amountValidate[$order_currency]){
					//需要3D
					$is_3d = 1;
				}
			}
		}
	

		if($is_3d ==  0){
			$validate_arr['terminal'] = Configuration :: get('OP_CREDITCARD_TERMINAL');
			$validate_arr['securecode'] = Configuration :: get('OP_CREDITCARD_SECURECODE');
		}elseif($is_3d == 1){
			//3D
			$validate_arr['terminal'] = Configuration :: get('OP_CREDITCARD_SECURE_TERMINAL');
			$validate_arr['securecode'] = Configuration :: get('OP_CREDITCARD_SECURE_SECURECODE');
			$_SESSION['is_3d'] = 1;
		}
	
	
		return $validate_arr;
	
	}
	
	
	
	
	
	
	/**
	 * 获取订单详情
	 */
	function getProductItems($AllItems){
	
		$productDetails = array();
		$productName = array();
		$productSku = array();
		$productNum = array();
			
		foreach ($AllItems as $item) {
			$productName[] = $item['name'];
			$productSku[] = $item['id_product'];
			$productNum[] = $item['cart_quantity'];
		}
	
		$productDetails['productName'] = implode(';', $productName);
		$productDetails['productSku'] = implode(';', $productSku);
		$productDetails['productNum'] = implode(';', $productNum);
	
		return $productDetails;
	
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
