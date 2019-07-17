<?php
namespace Admin\Model;
use Think\Model;
use Admin\Model\AuthModel;
/*
 *  Role表
 */
class RoleModel extends Model {
    
     protected $_validate = array(     
        array('role_name','require','权限名不能为空'), 
        array('role_name','','权限名已存在',0,'unique',1),
        array('auth_id','number','权限id字段异常'),
    );
     

    /**
     * 添加角色
     */
    public function role_add(){
        $role_auth_ids = implode(',',$_POST['role_auth_ids']);
        $_POST['role_auth_ids']=$role_auth_ids;
        $z=$this->add($_POST);
        return $z;
    }
    
    /**
     * 编辑角色
     */
    public function role_edit(){
        $role_auth_ids = implode(',',$_POST['role_auth_ids']);
        $_POST['role_auth_ids']=$role_auth_ids;
        $z=$this->save($_POST);
        return $z;
    }
    
    
    /**
     * 根据传来的角色id
     * return 拥有此角色的人员列表
     */
    public function userlist(){
        $role_id=$_GET['role_id'];
        //$count=D('user')->where("role_id like '%,{$role_id},%' or role_id like '%{$role_id},%' or role_id like '%,{$role_id}%' or role_id ='{$role_id}'")->count();  
        $count=D('user')->where("FIND_IN_SET({$role_id},role_id)")->count();  
        $userlist=D('user')
        //->where("a.role_id like '%,{$role_id},%' or a.role_id like '%{$role_id},%' or a.role_id like '%,{$role_id}%' or a.role_id ='{$role_id}'")
        ->where("FIND_IN_SET({$role_id},role_id)")
        ->select();
        $data['userlist']=$userlist;
        $data['count']=$count;
        return $data;
    }
    
    
    
    /**
     * 传入role_id和用户ids数组
     * 角色中移除用户,如果用户没角色了则改为默认角色
     */
    public function del_user(){
        $role_id=$_POST['role_id'];
        $user_ids=$_POST['ids'];
        $count=0;
        //去掉空
        foreach ($user_ids as $v) {
            if (empty($v)) {
                continue;
            }
            $ids[] = $v;
        }
        /*
         * 循环用户
         * 1、取出角色id
         * 2、转换为数组后移除指定角色
         * 3、update
         */
        foreach ($ids as $v)
        {
            //1、修改角色
            $userdata=D('user')->find($v);
            //当前id用户的角色
            $now_role_id=$userdata['role_id'];
            $role_id_arr=explode(",", $now_role_id);
            //删掉这个角色
            foreach ($role_id_arr as $j=>$k)
            {
                if ($role_id_arr[$j]==$role_id)
                {
                    unset($role_id_arr[$j]);
                }
            }
            //如果没有角色了则把角色改为默认角色
            if (empty($role_id_arr))
            {
                $role_id_arr[]="1";
            }
            //2、重新赋予角色
            $role_id_str=implode(',',$role_id_arr); 
            $data['role_id']=$role_id_str;
            $data['id']=$v;
            $z=D('user')->save($data);
            if ($z)
            {
                $count++;
            }
        }
        return $count;
    }
    
    
    
    
    
 
    

    
	
}






