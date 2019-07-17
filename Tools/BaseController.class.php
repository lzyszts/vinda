<?php
namespace Tools;
use Think\Controller;

/*
 * 基础权限的控制器。老版本后台使用的，只验证到控制器不验证方法
 */
class BaseController extends Controller{
    public function __construct(){
        parent::__construct();//先执行父类构造方法，避免功能缺失
        
        //在此处做用户访问权限控制过滤功能
        //获得目前访问的“控制器 和 操作方法”，并连接为字符串称作“当前操作”
        //并使得"当前操作"  与  "权限列表"做对比
        $nowac=CONTROLLER_NAME."-".ACTION_NAME;//"当前操作"
        $userinfo=session('userinfo');
        $user_id=$userinfo['id'];
        $username=$userinfo['samaccountname'];          
        
        //①没有登陆跳转登录
        //验证无需要登陆
        $yunxu_ac = "Archive-index";
        //如果session下没有工号或者没有出现在允许列表的就跳转到首页
        if(empty($user_id) && strpos($yunxu_ac,$nowac)===false){
			$this->error('请先登陆!',U("Admin/Index/login"),2);
            /*
            $group_url = __APP__;
            $js = <<<eof
                <script type="text/javascript">
                    //top：作用使得整个frameset都跳转
                    window.top.location.href = "$group_url/Admin/Index/login";
                </script>
eof;
            echo $js;
            */
        }
        
    }
  
}