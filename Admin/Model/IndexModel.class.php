<?php
namespace Admin\Model;
use Think\Model;
/*
 *  Index模型
 */
class IndexModel extends Model {
    protected $tableName = 'user';
    protected $_validate = array(     
   
    );
     
     
    /**
     * 登陆后读取后台用户权限
     */
     public function get_auth_ids(){
         //1、读取用户的权限ids,如果没角色就直接读,有角色就读角色的
         $userinfo=session('userinfo');
         if($userinfo['role_id']=="N")
         {
             $auth_ids=$userinfo['auth_ids'];
         }
         else //读角色权限
         {
             //查当前用户的角色信息保存所有权限ids
             $role_ids=$userinfo['role_id'];
             $auth_ids=D('user')->roleids_authids($role_ids,0);             
         }
         return $auth_ids;        
     }
     
     
     
     
     /**
      *  根据用户权限ids读取后台用户列表
      */
     public function get_user_list($auth_ids){
         $userinfo=session('userinfo');
         if($userinfo['samaccountname']=="admin"){  //如果是admin显示所有菜单
             $data['authinfo_1']=D('auth')->where("auth_level = 0")->select();
             $data['authinfo_2']=D('auth')->where("auth_level = 1")->select();
         }elseif(empty($auth_ids)){
              
         }else{//其他的根据权限显示
             $data['authinfo_1']=D('auth')->where("auth_level = 0 and auth_id in ($auth_ids)")->select();
             $data['authinfo_2']=D('auth')->where("auth_level = 1 and auth_id in ($auth_ids)")->select();
         }
         return $data;
     }
     
     
     /**
      * 根据用户权限ids读取用户待办事项
      */
     public function backlog($userauth){
         //1、用户有用户审核权限则显示待审
         $res=D("auth")->where("auth_name='用户审核'")->find();
         $userinfo=session('userinfo');
         $id=$res['auth_id'];
         $userauth=explode(",", $userauth);
         if (in_array($id, $userauth) || $userinfo['username']=="admin")
         {
             $count=D('userAudit')->count();
             if ($count!=0)
             {
                $data[0]['msg']="新用户注册审核记录数";
                $data[0]['count']=$count;
                $data[0]['url']=U('Admin/User/auditlist');
                return $data;
             }else{
                 return false;
             }     
         }else{
             return false;
         }
         
     }

    

    
	
}






