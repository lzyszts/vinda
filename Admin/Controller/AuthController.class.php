<?php
namespace Admin\Controller;
use Tools\BackController;
use Think\Controller;
/*
 * 权限控制器
 */
class AuthController extends BackController {
    /**
     * 显示所有权限
     */
   public function showlist() {
       $authinfo=D('auth')->showlist();
       $count['back']=D('auth')->count();   
       $this->assign(array(
           'title' =>'权限管理',
           'count' =>$count,
           'authinfo' =>$authinfo,
           'c'=>"0"
       ));
       $this->display();
   }
    /**
     * 权限添加
     */
    public function add(){
        //展示、收集
        $model_auth=D("auth");      
        if(!empty($_POST)){
            $_POST=I("post.");
            //顶级权限添加不用验证
            if($_POST['auth_pid']==0)
            {
                $check=true;
            }else{
                $check=$model_auth->create($_POST,1);
            }
            if($check)
            {
                $z=$model_auth->auth_add(trim($_POST));
                if($z){
                    echo "1";
                }else{
                    echo "添加权限失败!";
                }
            }else{
                echo $model_auth->getError();
            }   
        }else{
            $authinfo=D('auth')->showlist();
            $this->assign(array(
                'authinfo'=>$authinfo,
                'title' =>'添加权限'
                )
            );
            $this->display();
        }
    }
    /**
     * 权限编辑
     */
    public function edit(){
        //展示、收集
        $model_auth=D("auth");
        if(!empty($_POST)){
            $_POST=I("post.");
            //顶级权限添加不用验证
            if($_POST['auth_pid']==0)
            {
                $check=true;
            }else{
                $check=$model_auth->create($_POST,2);
            }
            if($check)
            {
                $z=$model_auth->auth_edit(trim($_POST));
                if($z==1){
                    echo "1";
                }else{
                    echo "没有修改内容或者编辑失败!";
                }
            }else{
                echo $model_auth->getError();
            }   
        }else{
            $authinfo=$model_auth->find($_GET['id']);
            $this->assign(array(
                'title'=>'编辑权限节点',
                'authinfo'=>$authinfo,
            ));
               
            $this->display();
        }
    }
    /**
     * 权限删除
     */
    public function del(){
        $model_auth=D("auth");
        $check=$model_auth->create($_POST);
        if($check!==false){
            $z=$model_auth->auth_del();
            if ($z)
            {
                echo $z;
            }else{
                echo "删除失败";
            }
        }else{
            echo $model_auth->getError();
        }
    }
    
    

    

    
}