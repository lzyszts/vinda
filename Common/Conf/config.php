<?php
return array(
    //设置显示跟踪信息
    'SHOW_PAGE_TRACE' => false,
    
    //设置默认分组
    'DEFAULT_MODULE' => 'Admin',     //默认模块 
    'DEFAULT_ACTION' => 'login',     //默认方法
    //允许访问的分组信息
    'MODULE_ALLOW_LIST' => array('Home','Admin','Data','Smarty'),
    
    //设置使用Smarty模板
    //'TMPL_ENGINE_TYPE'  =>  'Smarty',
	'TMPL_ACTION_SUCCESS' => 'Public/jump' ,
    'TMPL_ACTION_ERROR' => 'Public/jump',
    //去掉U函数的伪静态  
    'URL_HTML_SUFFIX' =>'',
    //配置数据库
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '127.0.0.1', // 服务器地址
    'DB_NAME'               =>  'vinda',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  'root',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  '',    // 数据库表前缀
    'DB_PARAMS'          	=>  array(), // 数据库连接参数
    'DB_DEBUG'  			=>  TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'       =>  true,        // 启用字段缓存
    'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8	
	
	//本地验证用户
	"LOCAL_ARR" =>  array(
        "admin","1",
     ),
	
	//不需要登陆就可以访问的页面
	"DEFAULT_MCA" =>  array(
        "Admin-Index-login",     //登陆页面  
        "Admin-User-check",      //验证密码  
        "Data-Bc-printdata",     //班产报表打印
     ),
	//配置绑定权限模块
    //即拥有某个权限则对应数组中的权限也自动加入
	"BELONG_MCA"=>array(
		//班产报表
		"Data-Bc-mylist"=>array("Data-Bc-mydata","Data-Bc-pri","Data-Bc-export","Data-Bc-add_wg_num"),
		"Data-Bc-alllist"=>array("Data-Bc-alldata"),
        "Data-BcBack-index"=>array("Data-BcBack-alldata","Data-BcBack-search"),
        "Data-BcBack-type"=>array("Data-BcBack-type_add","Data-BcBack-del","Data-BcBack-next_add","Data-BcBack-next_del"),
        "Data-BcBack-analysis"=>array("Data-BcBack-lineData","Data-BcBack-cpData"),
        "Data-BcBack-bb"=>array("Data-BcBack-export_cw","Data-BcBack-cw_search","Data-BcBack-lock_data"),
	),

);
