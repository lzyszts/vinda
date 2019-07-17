<?php
//命名空间
namespace Admin\Controller;
use Tools\BackController;
use Model\RoleModel;
use Model\UserModel;
/*
 * 角色控制器
 */
class RoleController extends BackController {
    /*
     * 显示角色列表
     */
   public function showlist() {
       $roleinfo=D('Role')->select();
       $count=D('role')->count();
       $this->assign(array(
           'title' => '角色管理',
           'count' =>$count,
           'roleinfo' =>$roleinfo
       ));
       $this->display();
   }

   /**
    * 添加角色
    */
   public function add(){
       //提交
       $model_role=D("role");
       if(!empty($_POST))
       {
           $_POST=I("post.");
           $check=$model_role->create($_POST,1);
           if($check)
           {
               $z=$model_role->role_add($_POST);
               if($z){
                   echo "1";
               }else{
                   echo "添加权限失败!";
               }
           }else{
               echo $model_role->getError();
           }
       }else{
           //展示
           //$authinfo=$model_role->get_auth_list();
           $authinfo_0 = D('Auth')->order('auth_id desc')->where('auth_level=0')->select();
           $authinfo_1 = D('Auth')->where('auth_level=1')->select();
           $authinfo_2 = D('Auth')->where('auth_level=2')->select();
           $this->assign(array(
               'title' => '添加角色',
               "authinfo_0"=>$authinfo_0,
               "authinfo_1"=>$authinfo_1,
               "authinfo_2" =>$authinfo_2
           ));
           $this->display();
       }
   }
   
   
   /**
    * 角色编辑
    */
   public function edit(){
       //提交
       $model_role=D("role");
       if(!empty($_POST))
       {
           $_POST=I("post.");
           $check=$model_role->create($_POST,2);
           if($check)
           {
               $z=$model_role->role_edit($_POST);
               if($z){
                   echo "1";
               }else{
                   echo "没有修改内容或者编辑失败!";
               }
           }else{
               echo $model_role->getError();
           }
       }else{
           //展示所选角色权限
           $roleinfo=$model_role->find($_GET['id']);
           //后台
           $role_auth_ids=explode(',', $roleinfo['role_auth_ids']);

           $authinfo_0 = D('auth')->order('auth_id desc')->where('auth_level=0')->select();
           $authinfo_1 = D('auth')->where('auth_level=1')->select();
           $authinfo_2 = D('auth')->where('auth_level=2')->select(); 
           $this->assign(array(
               'title' => '编辑角色',
               "authinfo_0"=>$authinfo_0,
               "authinfo_1"=>$authinfo_1,
               "authinfo_2" =>$authinfo_2,
               "roleinfo" =>$roleinfo,
               "role_auth_ids" =>$role_auth_ids,
           ));
           $this->display();
       }
   }
   /**
    * 角色删除
    */
   public function del(){
       $model_role=D("role");
       $check=$model_role->create($_POST);
       $role_id=$_POST['role_id'];
       $count=D('user')->where("role_id like '%,{$role_id},%' or role_id like '%{$role_id},%' or role_id like '%,{$role_id}%' or role_id ='{$role_id}'")->count();
       if ($count>0)
       {
           echo "角色下存在".$count."个用户,必须移除用户才能删除角色";
       }else{
           if($check!==false){
               $z=$model_role->delete($role_id);
               if ($z)
               {
                   echo $z;
               }else{
                   echo "删除失败";
               }
           }else{
               echo $model_role->getError();
           }
       }
     
   }
   
   /**
    * 根据角色id显示用户列表信息
    * post批量移除角色下的用户
    */
   public function userlist(){
       $model_role=D('role');
       if($_POST)
       {
           $res=$model_role->del_user();
           echo $res;
       }else{
           $data=$model_role->userlist();
           $this->assign(array(
               'title' => '人员列表',
               'count' =>$data['count'],
               'userlist' =>$data['userlist'],
           ));
           $this->display();
       }
   }
   
   

   
   
 
}