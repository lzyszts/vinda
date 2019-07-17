<?php
namespace Admin\Controller;
use Tools\BackController;
/*
 * 用户控制器
 */
class UserController extends BackController {
 
    /**
     * 显示用户列表
     */
   public function showlist() {
       $model_user=D('user');
       $pagesize=isset($_GET['pagesize'])?$_GET['pagesize']:10;
       //有查询数据传来的时候查询
       if(!empty($_GET['search'])){
           $text=$_GET['search'];
           $data=$model_user->search($pagesize);
       }else{
           //没有则展示所有user信息并且分页
           $data=$model_user->showlist($pagesize);
       }
       $this->assign(array(
           "title" => "用户列表",
           "data"=>$data['data'],
           "pagearr"=>$data['pagearr'],
           "pagelist" =>$data['pagelist'],
           'count' =>$data['count']
       ));
       $this->display();
   }
   /**
    * 添加用户
    */
   public function user_add(){
       $model_user=D('user');
       if(!empty($_POST)){         
           $_POST=I("post.");
           $check=$model_user->create($_POST,1);
           if($check!==false)
           {
               $z=D('user')->user_add();
               if($z){
                   echo "1";
               }
               elseif($z===false)
               {
                   echo $model_user->getError();
               }else{
                   echo "添加失败!";
               }
           }else{
               echo $model_user->getError();
           }
       }else{
           //$deptinfo=D('dept')->select();
           $this->assign(array(
               "title" => "添加新用户",
               //"deptinfo"=>$deptinfo
           ));
           $this->display();
       }
   }
   /**
    * 改变用户状态 启用禁用
    */
   public function change_state(){
      $model_user=D('user');
      $res=$model_user->save($_POST);
      echo $res;
   }





/****************************功能******************************/
    /**
     * 封装layui返回的用户导入结果信息
     */
    public function layui_data($data,$code=0,$msg=""){
        $data_str='{"code":'.$code.',"msg":"'.$msg;
        foreach ($data as $k=>$v)
        {
            $v['create_time']=date("Y-m-d H:i:s",$v['create_time']);
            if(!empty($v['finish_time']))
            {
                $v['finish_time']=date("Y-m-d H:i:s",$v['finish_time']);
            }
            
            $json .= json_encode($v).',';
        }
        $json = substr($json, 0, -1);
        $data_str.=',"data":['.$json.']}';
        return $data_str;
    }


   
   /**
    * 用户编辑角色展示,修改
    */
   public function role_edit(){
       if(!empty($_POST)){
           $_POST=I("post.");
           $check=D('user')->create($_POST,2);
           if($check!==false)
           {
               $z=D('user')->role_edit();
               if($z>0){
                   echo "1";
               }
               if($z===0)
               {
                   echo "没有修改数据或修改失败";
               }
               else{
                   echo D('user')->getError();
               }
           }else{
               echo D('user')->getError();
           }
       }else{
           $role_list=D('role')->select();
           //用户角色ids
           $user_id=$_GET['id'];     
           $user_role_ids=D('user')->field('role_id')->find($user_id);
           $user_role_ids=explode(',', $user_role_ids['role_id']);
           $this->assign(array(
               "title" =>"用户角色编辑",
               "user_role_ids"=>$user_role_ids,
               "role_list"=>$role_list,
               "user_id" =>$user_id 
           ));
           $this->display();
       } 
   }
   
   /**
    * 用户单独权限编辑展示,修改
    */
   public function auth_edit(){
       $model_user=D('user');
       if(!empty($_POST)){
           $_POST=I("post.");
           $check=$model_user->create($_POST);
           if($check!==false)
           {
               $z=D('user')->auth_edit();
               if($z){
                   echo "1";
               }else{
                   echo "没有修改内容或者编辑失败!";
               }
           }else{
               echo $model_user->getError();
           }
       }else{
           //用户有权限ids则显示用户权限，无则直接显示权限列表
           $userinfo=$model_user->find($_GET['id']);
           $auth_ids=$userinfo['auth_ids'];
           if ($auth_ids!=="N")
           {
               $auth_ids=explode(',',$auth_ids);
           }
           $authinfo_0 = D('auth')->order('auth_id desc')->where('auth_level=0')->select();
           $authinfo_1 = D('auth')->where('auth_level=1')->select();
           $authinfo_2 = D('auth')->where('auth_level=2')->select(); 
           $this->assign(array(
               "title" => "用户权限编辑",
               "authinfo_0"=>$authinfo_0,
               "authinfo_1"=>$authinfo_1,
               "authinfo_2" =>$authinfo_2,
               "userinfo" =>$userinfo,
               "user_auth_ids" =>$auth_ids,
           ));
           $this->display();
       } 
   }


   
   /**
    * 用户删除
    */
   public function user_del(){
       $model_user=D("user");
       $check=$model_user->create($_POST);
       if($check!==false){
           $z=$model_user->delete($_POST['user_id']);
           if ($z)
           {
               echo $z;
           }else{
               echo "删除失败";
           }
       }else{
           echo $model_user->getError();
       }
   }
   
   
/*************用户审核******************/   
   /**
    * 用户审核
    */
   public function auditlist(){
       $model_user=D('user');
       $data=D("user")->auditlist();
       $count=D('userAudit')->count();
       $this->assign(array(
           "title" =>"用户审核列表",
           "data" => $data,
           "count" => $count
       ));
       $this->display();
   }
    
   /**
    * 新用户审核通过
    */
   public function audit_add(){
       $model_user=D('user');
       $id=$_POST['data_id'];
       $z=D('user')->audit_add();
       if($z>1){
           //再删除审核表中记录
           D('userAudit')->delete($id);
           echo "1";
       }else
      {
           echo $model_user->getError();
       }

   }
   
   
   /**
    * 新用户审核拒绝
    */
   public function audit_del(){
       $model_user=D("user");
       $_POST=I("post.");
       $check=$model_user->create($_POST);
       if($check!==false){
           $z=D("userAudit")->delete($_POST['data_id']);
           if ($z)
           {
               echo $z;
           }else{
               echo "删除失败";
           }
       }else{
           echo $model_user->getError();
       }
   }
   
   
   
 
}