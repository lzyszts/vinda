<?php
namespace Admin\Controller;
use Tools\BackController;
/*
 * 部门控制器
 */
class DeptController extends BackController {
    /**
     * 显示部门列表
     */
   public function showlist() {
       $model_dept=D('dept');
       $deptinfo=$model_dept->showlist();
       //处理部门人数
       $deptinfo=$model_dept->usercount($deptinfo);
       $count=$model_dept->count();
       $this->assign(array(
           'title' => '部门列表',
           'count' =>$count,
           'deptinfo' =>$deptinfo
       ));
       $this->display();
   }
   /**
    * 根据部门id显示用户列表信息
    * post批量修改用户部门
    */
   public function userlist(){
       $model_dept=D('dept');
       if($_POST)
       {
           $res=$model_dept->user_dept_update();
           echo $res;
       }else{
           $data=$model_dept->userlist();
           $deptinfo=D('dept')->select();
           $this->assign(array(
               'title' => '人员列表',
               'count' =>$data['count'],
               'userlist' =>$data['userlist'],
               'deptinfo' =>$deptinfo
           ));
           $this->display();
       }
   }
   /**
    * 添加新部门
    */
   public function add(){
       $model_dept=D("dept");
       if(!empty($_POST)){
           $_POST=I("post.");
           //顶级部门添加不用验证
           if($_POST['dept_pid']==0)
           {
               $check=true;
           }else{
               $check=$model_dept->create($_POST,1);
           }
           if($check)
           {
               $z=$model_dept->dept_add(trim($_POST));
               if($z){
                   echo "1";
               }else{
                   echo "添加部门失败!";
               }
           }else{
               echo $model_dept->getError();
           }
       }else{
           $deptinfo=D('dept')->showlist();
           $this->assign(array(
               'title' => '添加部门',
               'deptinfo' =>$deptinfo
           ));
           $this->display();
       }
   }
   /**
    * 部门编辑
    */
   public function edit(){
       $model_dept=D("dept");
       if(!empty($_POST)){
           $_POST=I("post.");
           $check=$model_dept->create($_POST,2);
           if($check)
           {
               $z=$model_dept->save(trim($_POST));
               if($z==1){
                   echo "1";
               }else{
                   echo "没有修改内容或者编辑失败!";
               }
           }else{
               echo $model_dept->getError();
           }
       }else{
           $deptinfo=$model_dept->find($_GET['id']);
           $this->assign(array(
               'title' => '编辑部门',
               'deptinfo' =>$deptinfo
           ));
           $this->display();
       }
   }
   
   /**
    * 部门删除
    */
   public function del(){
       $model_dept=D("dept");
       $check=$model_dept->create($_POST);
       if($check!==false){
           $z=$model_dept->dept_del();
           if ($z)
           {
               echo $z;
           }else{
               echo $model_dept->getError();
           }
       }else{
           echo $model_dept->getError();
       }
   }
 
   
   
   
   
   
 
}