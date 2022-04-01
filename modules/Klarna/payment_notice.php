<?php

/* SSL Management */
$useSSL = true;
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

//获取推送输入流XML
$xml_str = file_get_contents("php://input");

//判断返回的输入流是否为xml
if(xml_parser($xml_str)){
	$xml = simplexml_load_string($xml_str);

	//把推送参数赋值到$_REQUEST
	$_REQUEST['response_type']	  = (string)$xml->response_type;
	$_REQUEST['account']		  = (string)$xml->account;
	$_REQUEST['terminal'] 	      = (string)$xml->terminal;
	$_REQUEST['payment_id'] 	  = (string)$xml->payment_id;
	$_REQUEST['order_number']     = (string)$xml->order_number;
	$_REQUEST['order_currency']   = (string)$xml->order_currency;
	$_REQUEST['order_amount']     = (string)$xml->order_amount;
	$_REQUEST['payment_status']   = (string)$xml->payment_status;
	$_REQUEST['payment_details']  = (string)$xml->payment_details;
	$_REQUEST['signValue'] 	      = (string)$xml->signValue;
	$_REQUEST['order_notes']	  = (string)$xml->order_notes;
	$_REQUEST['card_number']	  = (string)$xml->card_number;
	$_REQUEST['payment_authType'] = (string)$xml->payment_authType;
	$_REQUEST['payment_risk'] 	  = (string)$xml->payment_risk;
	$_REQUEST['methods'] 	  	  = (string)$xml->methods;
	$_REQUEST['payment_country']  = (string)$xml->payment_country;
	$_REQUEST['payment_solutions']= (string)$xml->payment_solutions;
	
	//securecode
	//匹配终端号   判断是否3D交易
	if($_REQUEST['terminal'] == Configuration :: get('OP_KLARNA_TERMINAL')){
		$securecode = Configuration :: get('OP_KLARNA_SECURECODE');
	}elseif($_REQUEST['terminal'] == Configuration :: get('OP_KLARNA_SECURE_TERMINAL')){
		//3D
		$securecode = Configuration :: get('OP_KLARNA_SECURE_SECURECODE');
	}else{
		$securecode = '';
	}	
		
}


//交易推送
if($_REQUEST['response_type'] == 1){
	
	$logType = "[PUSH]";
	
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
	
	
	//签名数据
	$local_signValue = hash("sha256",$_REQUEST['account'].$_REQUEST['terminal'].$_REQUEST['order_number'].$_REQUEST['order_currency'].$_REQUEST['order_amount'].$_REQUEST['order_notes'].$_REQUEST['card_number'].
			$_REQUEST['payment_id'].$_REQUEST['payment_authType'].$_REQUEST['payment_status'].$_REQUEST['payment_details'].$_REQUEST['payment_risk'].$securecode);
		
	//响应代码
	$getErrorCode	= explode(':', $_REQUEST['payment_details']);
	$ErrorCode      = $getErrorCode[0];
	
	//数据签名对比
	if (strtoupper($local_signValue) == strtoupper($_REQUEST['signValue'])) {
		
		// So you migth have two order states
		$new_history = new OrderHistory();
		$new_history->id_order = $_REQUEST['order_number'];
		
		
		if($ErrorCode == 20061){
			//排除订单号重复(20061)的交易
		}else{
			if($_REQUEST['payment_status'] == 1 ){
				//支付成功
				$new_history->changeIdOrderState((int)Configuration :: get('OP_KLARNA_SUCCEED_STATES'), $_REQUEST['order_number']);
				$new_history->addWithemail(true);
					
			}elseif ($_REQUEST['payment_status'] == -1 ){
				//交易待处理
				//是否预授权交易
				if($_REQUEST['payment_authType'] == 1){
					$new_history->changeIdOrderState((int)Configuration :: get('OP_KLARNA_SUCCEED_STATES'), $_REQUEST['order_number']);
					$new_history->addWithemail(true);
				}else{
					$new_history->changeIdOrderState((int)Configuration :: get('OP_KLARNA_PENDING_STATES'), $_REQUEST['order_number']);
				}
			}else{
				//支付失败
				$new_history->changeIdOrderState((int)Configuration :: get('OP_KLARNA_FAIL_STATES'), $_REQUEST['order_number']);
			}
		}
		
		echo "receive-ok";
	}
	
}





//判断是否为xml
function xml_parser($str){
	$xml_parser = xml_parser_create();
	if(!xml_parse($xml_parser,$str,true)){
		xml_parser_free($xml_parser);
		return false;
	}else {
		return true;
	}
}

