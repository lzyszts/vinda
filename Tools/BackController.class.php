<?php
namespace Tools;
use Think\Controller;
use Admin\Model\UserModel;
/*
 * 后台平台控制器，验证权限和是否登录用
 */
class BackController extends Controller{
    public function __construct(){
        parent::__construct();//先执行父类构造方法，避免功能缺失
        //当前mca
        $now_mca=MODULE_NAME."-".CONTROLLER_NAME."-".ACTION_NAME;//"当前操作"      
        $userinfo=session('userinfo');
        $user_id=$userinfo['id'];
        $username=$userinfo['samaccountname'];     
        //不用登陆可用的页面
        $default_mca = C("DEFAULT_MCA");
        //验证是否登陆    
        if(empty($user_id) && in_array($now_mca,$default_mca)===false)
        {
            $this->error('请先登陆!',U("Admin/Index/login"),2);
        }
        //登陆后的用户权限(array)
        //函数中同时调用了allow_mca，加入登陆后不需要验证的权限mca数组
        //这里调用会存session
        $user_mca=$this->get_user_auth();
        //也可以单独调用不会存session
        //$user_mca=$this->allow_mca($user_mca);

        //① 当前操作  没有出现在  权限列表 里边
        //② 当前操作  没有出现在 默认允许的 权限列表 里边
        //③ 当前用户 还不是超级管理员admin
        //以上①、②、③ 条件同时满足，就“没有访问权限”
        if(in_array($now_mca,$user_mca)===false && in_array($now_mca,$default_mca)===false && $username!=='admin')
        {
            exit("无权限访问或操作");
        }
    }

    /**方法
     * 制作不需要验证的mca加入user_mca
    */
    public function allow_mca($user_mca){
        //1、登陆后不需要权限的页面
        $allow_mca = array(
            "Admin-Index-login",      //登陆页面  
            "Admin-User-check",      //验证密码  
            "Admin-Index-loginout",
            "Admin-Index-index",
            "Admin-Index-home",
            "Admin-Index-gg_add",   //公告添加删除
            "Admin-Index-gg_del",
        );
        //2、某个权限下所属权限
        //例如：展示页面下面有一些数据接口，绑定进来则不用权限验证
        /*$belong_mca=array(
            //班产报表
            "Data-Bc-mylist"=>array("Data-Bc-mydata"),
        );*/
        //取配置文件的绑定权限
        $belong_mca=C('BELONG_MCA');
        //外层用户拥有mca
        foreach ($user_mca as $value) {
            if(!empty($belong_mca[$value]))
            {
                //内层当前权限下面绑定的权限
                foreach ($belong_mca[$value] as $v) {
                    $allow_mca[]=$v;
                }
            }
        }
        //3、合并用户权限和其他允许的权限
        $user_mca=array_merge($user_mca,$allow_mca);
        $user_mca=array_unique($user_mca);
        return $user_mca;
    }






    /**工具
     * 根据用户id查询 
     * 单独权限直接取出权限
     * 角色就遍历角色取出权限
     * return m-c-a数组
     */
    public function get_user_auth(){
        $userinfo=session('userinfo');
        $auth_mca=$userinfo['auth_mca'];
        if(isset($userinfo) && !$auth_mca)
        {   
            $model_user=new UserModel();
            //单独权限直接制作m-c-a
            if ($userinfo['role_id']=="N")
            {
                $auth_mca=$model_user->auth_ids_mca($userinfo['auth_ids']);
                $auth_mca=explode(",", $auth_mca);
            }else{
                //有角色则 根据角色s找权限ids
                $auth_ids=$model_user->roleids_authids($userinfo['role_id'],0);
                //再制作m-c-a
                $auth_mca=$model_user->auth_ids_mca($auth_ids);
                $auth_mca=explode(',',$auth_mca);
            }
            //根据用户权限加入默认允许mca
            $auth_mca=$this->allow_mca($auth_mca);
            //更新权限m-c-a进用户session
            $userinfo['auth_mca']=$auth_mca;
            session("userinfo",$userinfo);
        }
        return $auth_mca;  
    }
    
  

}

