<?php
namespace Admin\Model;
use Think\Model;
use Tools\Page;
/*
 *  部门表
 */
class DeptModel extends Model {
    
     protected $_validate = array(     
         array('dept_name','require','部门不能为空'),
         //部门名字不一样是为了批量导入用户不出错
         array('dept_name','','部门已存在',0,'unique',3),
         array('dept_id','number','部门信息错误'),
         array('dept_pid','number','部门上级信息错误'),
    );
   
     /**
      * 显示部门列表
      */
     public function showlist(){
         $deptinfo=$this->select();
         $data=$this->get_tree($deptinfo);
         return $data;
     }
     /**
      * 处理部门人数
      */
     public function usercount($deptinfo){
         foreach ($deptinfo as $k=>$v)
         {
             $deptinfo[$k]['count']=$this->dept_user_num($v['dept_id']); 
         }
         return $deptinfo;
     }
     
     /**
      * 根据传来的部门id部门及子部门的人数
      */
     public function dept_user_num($dept_id){
         $deptinfo=$this->select();
         //子部门id
         $dept_ids=$this->get_child($deptinfo,$dept_id,ture); 
         //加上自己
         $dept_ids[]=$dept_id;
         $dept_ids=implode(",", $dept_ids);
         $count=D('user')->where("dept_id in ({$dept_ids})")->count();
         return $count;
     }
     
     /** 递归分类部门
      * @param array $data 部门列表数组
      * @param number $dept_pid
      */
     private function get_tree($data,$dept_pid=0,$clear=false){
         static $res=array();
         //第一次调用需要清空静态数组，后面调用则不需要
         if($clear)
         {
             $res=array();
         }
         foreach ($data as $v)
         {
             if($v['dept_pid']==$dept_pid)
             {
                 $res[]=$v;
                 // 找子分类
                 $this->get_tree($data,$v['dept_id']);
             }
         }
         return $res;
     }
     
     /**
      * 根据部门id求出所有子id
      * @param array $data 部门列表数组
      * @param string $dept_id 需要查找的部门id
      */
     private function get_child($data,$dept_id,$clear=false){
         static $res=array();
         //第一次调用需要清空静态数组，后面调用则不需要
         if($clear)
         {
             $res=array();
         }
         foreach ($data as $v)
         {
             if($v['dept_pid']==$dept_id)
             {
                 $res[]=$v['dept_id'];
                 $this->get_child($data,$v['dept_id']);
             }
         }
         return $res;
     }
     
     
    /**
     * 根据传来的部门id求部门及子部门的人员信息
     */
    public function userlist(){
        $deptinfo=$this->select();
        //子部门id
        $dept_ids=$this->get_child($deptinfo, $_GET['dept_id']);
        //加上自己
        $dept_ids[]=$_GET['dept_id'];
        $dept_ids=implode(",", $dept_ids);
        //根据部门ids查找用户,联表取部门中文名字
        $userlist=D('user')
        ->field("a.*,b.dept_name")
        ->alias('as a')
        ->join('left join __DEPT__ as b on a.dept_id=b.dept_id')
        ->where("a.dept_id in ({$dept_ids})")->select();
        $count=D('user')->where("dept_id in ({$dept_ids})")->count();
        $data['userlist']=$userlist;
        $data['count']=$count;
        return $data;
    }
    

    
    
    
    
    /**
     * 用户批量修改部门
     */
    public function user_dept_update(){
        //去掉空
        foreach ($_POST['ids'] as $v) {
            if (empty($v)) {
                continue;
            }
            $ids[] = $v;
        }
        $ids=implode(",",$ids);
        $dept=$this->find($_POST['dept_id']);
        $dept_name=$dept['dept_name'];
        //批量修改
        $data['dept_id']=$_POST['dept_id'];
        $data['dept']=$dept_name;
        $res=D('user')->where("id in ({$ids})")->save($data);
        return $res;
    }
     
    /**
     * 插入新部门
     */
    public function dept_add(){
        //查找权限上级的dept_level然后+1
        if($_POST['dept_pid']!=0)
        {
            $dept_p_level=$this->field("dept_level")->where("dept_id={$_POST['dept_pid']}")->find();
            $_POST["dept_level"]=$dept_p_level["dept_level"]+1;
        }
        $res=$this->add($_POST);
        return $res;
    }
    
    /**
     * 删除部门
     * 查询部门下面有无人员,有人员无法删除
     */
    public function dept_del(){
        $dept_id=$_POST['dept_id'];
        //先找子分类
        $data=$this->select();
        $ids=$this->get_child($data, $dept_id);
        array_unshift($ids, $dept_id);
        //自己和子部门的id
        $ids=implode(",", $ids);
        //找这些部门下有没有用户
        $count=D('user')->where("dept_id in ({$ids})")->count();
        if($count>0)
        {
            $this->error="当前部门下还存在".$count."人,无法删除";
            return false;
        }else{
            $z=$this->delete($ids);
            return $z;
        }
    }
    
    /**
     * 返回所有最底级部门信息
     * 批量导入用户时候用的
     */
    public function low_dept(){
        $dept_info=$this->select();
        foreach ($dept_info as $k => $v) {
            foreach ($dept_info as $key => $value) {
                if($value['dept_pid']==$v['dept_id'])
                {
                    continue 2;
                }
            }
            $data[]=$v;
        }
        return $data;
    }

   
    
	
}






