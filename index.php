<?php
header("content-type:text/html;charset=utf-8");
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);

// 定义根目录;
define('SELF_URL','http://192.168.100.125/vinda');

//定义css、img、js常量路径
$arr=array(
    'Admin'=>'ADMIN',
    'Data'=>'DATA',
    'Smarty'=>'SMARTY'
);
foreach ($arr as $min =>$max)
{
    define($max."_CSS_URL",SELF_URL."/".$min."/Public/css");
    define($max."_IMG_URL",SELF_URL."/".$min."/Public/images");
    define($max."_JS_URL",SELF_URL."/".$min."/Public/js");
    define($max."_FONT_URL",SELF_URL."/".$min."/Public/fonts");
}
//上传文件目录
define('UPLOAD', '/Upload');
//下载文件地址
define('DOWNLOAD',SELF_URL."/Download");
//工具文件目录
define('TOOLS',SELF_URL."/Tools");
//问卷系统后台地址
DEFINE('WJ_URL','http://10.106.27.254/wj');
//问卷系统后台地址
DEFINE('LB_URL','http://192.168.27.254:2189');
//域控服务器地址
define('LDAP_IP','ldap://192.168.27.4:389');
// 引入ThinkPHP入口文件
require '../ThinkPHP/ThinkPHP.php';