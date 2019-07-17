<?php
namespace Smarty\Model;
use Think\Model;
/*
 * 生产部车间转交记录分组模型
 */
class ScbGroupModel extends Model {
   protected $trueTableName = 'scb_change_group';
   
   /*
    * 显示用户列表
    */
   public function showlist(){
      $group_list=$this->select();
      foreach ($group_list as $id=>$group_name)
      {
          $userinfo=D("scb_change_user")->where("group_id={$group_name['id']}")->select();
          $group_list[$id]['userinfo']=$userinfo;
      }
      return $group_list;
   }
   
   /*
    * 插入分组
    */
   public function addGroup()
   {
       $group_name=$_POST['group_name'];
       if(trim($group_name)=="")
       {
           return false;
       }
       $res=$this->where("group_name='{$group_name}'")->find();
       if($res)
       {
           return false;
       }else{
           if($this->add($_POST)){
               return true;
           }else{
               return false;
           }
       }
   }
    /*
     * 删除分组
     */
   public function delGroup(){
       $data=D("scb_change_user")->where("group_id={$_POST['id']}")->select();
       $ids=i_array_column($data,"id");
       D("scb_change_user")->delete(implode($ids,","));
       $res=$this->delete($_POST['id']);
       return $res;
   }
   
   
    
}