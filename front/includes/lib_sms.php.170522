<?php

function sendsms($mobile, $content)
{
	//初始化日志
	require_once(ROOT_PATH . 'includes/modules/payment/func/log.php');
	$logHandler= new CLogFileHandler(ROOT_PATH."/logs/".date('Y-m-d').'.log');
	$log = Logger::Init($logHandler, 15);
//	if(substr($mobile,0,2)=="86"){
//		Logger::DEBUG("mobile is = :" . $mobile);
//		return sendsms_imtong($mobile, $content);
//	}
	//8.19:liuc:臨時補丁，更換了網關，必須以國家嗎開頭，86
	Logger::DEBUG("mobile is china mobile  :" . $mobile);
	return sendsms_imtong($mobile, $content);
}

function sendsms_imtong($mobile, $content)
{
	//初始化日志
	require_once(ROOT_PATH . 'includes/modules/payment/func/log.php');
	$logHandler= new CLogFileHandler(ROOT_PATH."/logs/".date('Y-m-d').'.log');
	$log = Logger::Init($logHandler, 15);

	if(empty($content)){
		//空短信不发送
		Logger::ERROR("content is null:" . $content);
		return false;
	}
	//过滤黑字典
	$content = str_replace('1989','1 9 8 9',$content);
	$content = str_replace('1259','1 2 5 9',$content);
	$content = str_replace('12590','1 2 5 9 0',$content);
	$content = str_replace('10086','1 0 0 8 6',$content);
	Logger::DEBUG("mobile send content is success");
	//配置短信信息（hechengbin--start--）
	$encode='UTF-8';  //页面编码和短信内容编码为GBK。重要说明：如提交短信后收到乱码，请将GBK改为UTF-8测试。如本程序页面为编码格式为：ASCII/GB2312/GBK则该处为GBK。如本页面编码为UTF-8或需要支持繁体，阿拉伯文等Unicode，请将此处写为：UTF-8

	$username='shankaity';  //用户名

	$password_md5='6fec4393c5b283670b75346b62e6cfaa';  //32位MD5密码加密，不区分大小写

	$apikey='6656f261eeebc97d1614f0ea3f28d2ac';  //apikey秘钥（请登录 http://m.5c.com.cn 短信平台-->账号管理-->我的信息 中复制apikey）
    if(substr($mobile,0,2) == '86'){
        $contentUrlEncode = urlencode($content.'【盛开体育】');//执行URLencode编码  ，$content = urldecode($content);解码
    }else{
        $contentUrlEncode = urlencode($content.'[shankai]');//执行URLencode编码  ，$content = urldecode($content);解码
    }


	Logger::DEBUG("send message:".$mobile.":".$contentUrlEncode);
	$result = sendSM($username,$password_md5,$apikey,$mobile,$contentUrlEncode,$encode);  //进行发送
	Logger::DEBUG("send success!!!!!!!!".'-----'.$result);
    if(empty($result)){
        Logger::DEBUG("send message again:".$mobile.":".$contentUrlEncode);
        $result = sendSM($username,$password_md5,$apikey,$mobile,$contentUrlEncode,$encode);  //进行发送
        Logger::DEBUG("send success  again!!!!!!!!".'-----'.$result);
    }
	if(strpos($result,"success")>-1) {
		//提交成功
		return true;
		//逻辑代码
	} else {
		//提交失败

		Logger::DEBUG("SMS send failed:".$result);
		return false;
		//逻辑代码
	}
	//配置短信信息（hechengbin--end--）
}

//电话号码格式校验
function ismobile($mobile)
{
	//初始化日志
	require_once(ROOT_PATH . 'includes/modules/payment/func/log.php');
	$logHandler= new CLogFileHandler(ROOT_PATH."/logs/".date('Y-m-d').'.log');
	$log = Logger::Init($logHandler, 15);

	if (strlen($mobile) != 11 && strlen($mobile) != 12) {
		Logger::ERROR("mobile number is not 11 or 12.");
		return false;
	}

	if (preg_match("/^13\d{9}$/", $mobile)) return true;
	if (preg_match("/^14\d{9}$/", $mobile)) return true;
	if (preg_match("/^15\d{9}$/", $mobile)) return true;
	if (preg_match("/^18\d{9}$/", $mobile)) return true;
	if (preg_match("/^0\d{10}$/", $mobile)) return true;
	if (preg_match("/^0\d{11}$/", $mobile)) return true;
//	if (preg_match("/^82\d{10}$/", $mobile)) return true; //8.19:yurui:韩国号码82开头校验，12位

	Logger::ERROR("mobile number is not 13/14/15/18, 10/11/12 length");
	return false;
}

function getverifycode()
{
	$verifycode = rand(100000,999999);

	$verifycode = str_replace('1989','9819',$verifycode);
	$verifycode = str_replace('1259','9521',$verifycode);
	$verifycode = str_replace('12590','09521',$verifycode);
	$verifycode = str_replace('10086','68001',$verifycode);

	return $verifycode;
}

function httprequest($url, $data=array(), $abort=false) {
	if ( !function_exists('curl_init') ) { return empty($data) ? doget($url) : dopost($url, $data); }
	$timeout = $abort ? 1 : 2;
	$ch = curl_init();
	if (is_array($data) && $data) {
		$formdata = http_build_query($data);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $formdata);
	}
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	$result = curl_exec($ch);
	return (false===$result && false==$abort)? ( empty($data) ?  doget($url) : dopost($url, $data) ) : $result;
}

function doget($url){
	$url2 = parse_url($url);
	$url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
	$url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
	$host_ip = @gethostbyname($url2["host"]);
	$fsock_timeout = 2;  //2 second
	if(($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $fsock_timeout)) < 0){
		return false;
	}
	$request =  $url2["path"] .($url2["query"] ? "?".$url2["query"] : "");
	$in  = "GET " . $request . " HTTP/1.0\r\n";
	$in .= "Accept: */*\r\n";
	$in .= "User-Agent: Payb-Agent\r\n";
	$in .= "Host: " . $url2["host"] . "\r\n";
	$in .= "Connection: Close\r\n\r\n";
	if(!@fwrite($fsock, $in, strlen($in))){
		fclose($fsock);
		return false;
	}
	return gethttpcontent($fsock);
}

function dopost($url,$post_data=array()){
	$url2 = parse_url($url);
	$url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
	$url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
	$host_ip = @gethostbyname($url2["host"]);
	$fsock_timeout = 2; //2 second
	if(($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $fsock_timeout)) < 0){
		return false;
	}
	$request =  $url2["path"].($url2["query"] ? "?" . $url2["query"] : "");
	$post_data2 = http_build_query($post_data);
	$in  = "POST " . $request . " HTTP/1.0\r\n";
	$in .= "Accept: */*\r\n";
	$in .= "Host: " . $url2["host"] . "\r\n";
	$in .= "User-Agent: Lowell-Agent\r\n";
	$in .= "Content-type: application/x-www-form-urlencoded\r\n";
	$in .= "Content-Length: " . strlen($post_data2) . "\r\n";
	$in .= "Connection: Close\r\n\r\n";
	$in .= $post_data2 . "\r\n\r\n";
	unset($post_data2);
	if(!@fwrite($fsock, $in, strlen($in))){
		fclose($fsock);
		return false;
	}
	return gethttpcontent($fsock);
}

function gethttpcontent($fsock=null) {
	$out = null;
	while($buff = @fgets($fsock, 2048)){
		$out .= $buff;
	}
	fclose($fsock);
	$pos = strpos($out, "\r\n\r\n");
	$head = substr($out, 0, $pos);    //http head
	$status = substr($head, 0, strpos($head, "\r\n"));    //http status line
	$body = substr($out, $pos + 4, strlen($out) - ($pos + 4));//page body
	if(preg_match("/^HTTP\/\d\.\d\s([\d]+)\s.*$/", $status, $matches)){
		if(intval($matches[1]) / 100 == 2){
			return $body;  
		}else{
			return false;
		}
	}else{
		return false;
	}
}

//发送接口
function sendSM($username,$password_md5,$apikey,$mobile,$contentUrlEncode,$encode)
{
	//发送链接（用户名，密码，apikey，手机号，内容）
	$url = "http://m.5c.com.cn/api/send/index.php?";  //如连接超时，可能是您服务器不支持域名解析，请将下面连接中的：【m.5c.com.cn】修改为IP：【115.28.23.78】
	$data=array
	(
		'username'=>$username,
		'password_md5'=>$password_md5,
		'apikey'=>$apikey,
		'mobile'=>$mobile,
		'content'=>$contentUrlEncode,
		'encode'=>$encode,
	);
	$result = curlSMS($url,$data);
    Logger::DEBUG("return array:".'----'.$result);
	//print_r($data); //测试

	return $result;
}
function curlSMS($url,$post_fields=array())
{
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);//用PHP取回的URL地址（值将被作为字符串）
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//使用curl_setopt获取页面内容或提交数据，有时候希望返回的内容作为变量存储，而不是直接输出，这时候希望返回的内容作为变量
	curl_setopt($ch,CURLOPT_TIMEOUT,30);//30秒超时限制
	curl_setopt($ch,CURLOPT_HEADER,1);//将文件头输出直接可见。
	curl_setopt($ch,CURLOPT_POST,1);//设置这个选项为一个零非值，这个post是普通的application/x-www-from-urlencoded类型，多数被HTTP表调用。
	curl_setopt($ch,CURLOPT_POSTFIELDS,$post_fields);//post操作的所有数据的字符串。
	$data = curl_exec($ch);//抓取URL并把他传递给浏览器
	curl_close($ch);//释放资源
	$res = explode("\r\n\r\n",$data);//explode把他打散成为数组
    Logger::DEBUG("curl return :".$res);
	return $res[2]; //然后在这里返回数组。
}
?>