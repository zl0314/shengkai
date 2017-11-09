<?php

define("SDK_CVN2_ENC",0);// cvn2加密 1：加密 0:不加密
define("SDK_DATE_ENC",0);// 有效期加密 1:加密 0:不加密
define("SDK_PAN_ENC",0);// 卡号加密 1：加密 0:不加密
// ######(以下配置为PM环境：入网测试环境用，生产环境配置见文档说明)#######
define("SDK_SIGN_CERT_PATH",'/home/wwwroot/certs/700000000000001_16_acp.pfx');// 签名证书路径
define("SDK_SIGN_CERT_PWD",'000000');// 签名证书密码
define("SDK_ENCRYPT_CERT_PATH",'/home/wwwroot/certs/verify_sign_acp.cer');// 密码加密证书（这条用不到的请随便配）
define("SDK_VERIFY_CERT_DIR",'/home/wwwroot/certs/');// 验签证书路径（请配到文件夹，不要配到具体文件）
define("SDK_FRONT_TRANS_URL",'https://gateway.95516.com/gateway/api/frontTransReq.do');// 前台请求地址
define("SDK_BACK_TRANS_URL",'https://gateway.95516.com/gateway/api/backTransReq.do');// 后台请求地址
define("SDK_BATCH_TRANS_URL",'https://gateway.95516.com/gateway/api/batchTrans.do');// 批量交易
define("SDK_SINGLE_QUERY_URL",'https://gateway.95516.com/gateway/api/queryTrans.do');//单笔查询请求地址
define("SDK_FILE_QUERY_URL",'https://filedownload.95516.com/');//文件传输请求地址
define("SDK_Card_Request_Url",'https://gateway.95516.com/gateway/api/cardTransReq.do');//有卡交易地址
define("SDK_App_Request_Url",'https://gateway.95516.com/gateway/api/appTransReq.do');//App交易地址
define("SDK_FRONT_NOTIFY_URL",'http://webshop.shankaisports.com/check_balance.php?act=pay_done');// 前台通知地址 (商户自行配置通知地址)
define("SDK_BACK_NOTIFY_URL",'http://webshop.shankaisports.com/respond.php');// 后台通知地址 (商户自行配置通知地址)
define("SDK_FILE_DOWN_PATH",'/home/wwwroot/paylog/');//文件下载目录
define("SDK_LOG_FILE_PATH",'/home/wwwroot/paylog/');//日志 目录
define("SDK_LOG_LEVEL",'INFO');//日志级别


	
?>