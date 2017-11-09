<?php

/*
 * 作者：戎青松
 * 时间：12:21:09
 * 
 */
//项目目录
define("APP_PATH", str_replace('framework/function/path.php', '', str_replace('\\', '/', __FILE__)));
//开发目录
define("FRAMEWORK_PAHT", APP_PATH . "/framework/");
//依赖包目录
define("INCLUDES_PAHT", APP_PATH . "/includes/");
//接口目录
define("SERVICE_PAHT", FRAMEWORK_PAHT . "service/");
//接口实现类
define("IMPL_PAHT", SERVICE_PAHT . "impl/");
//控制器目录
define("CON_PATH", FRAMEWORK_PAHT . "controller/");
//后台控制器目录
define("ADMIN_CON_PATH", CON_PATH . "admin/");
//代理商控制器目录
define("AGENT_CON_PATH", CON_PATH . "agent/");
//模型目录
define("MODEL_PATH", FRAMEWORK_PAHT . "model/");
//工具类目录
define("UTIL_PATH", FRAMEWORK_PAHT . "util/");
//临时文件目录
define("TEMP_PATH", APP_PATH . "temp/");
//日志目录
define("LOG_PATH", TEMP_PATH . "log/");
