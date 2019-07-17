<?php
namespace Admin\Model;
use Think\Model;
/*
 *  auth表
 */
class AuthModel extends Model {
    
     protected $_validate = array(     
        array('auth_name','require','权限名不能为空'),
        array('auth_pid','number','权限上级错误'),
        array('module','/^[A-Z]\w{1,}$/','模型格式错误'),
        array('controller','/^[A-Z]\w{1,}$/','控制器格式错误'),
        array('action','require','方法不能为空'),     
        array('auth_id','number','权限id字段异常'), 
        array('is_home',array(0,1),'前后台数据异常！',0,in),
        /*必选,描述,是否跳转
        array('is_bx',array(0,1),'error,异常！',0,in),
        array('data_time','number','error,跳转时间异常！',2),
        array('wj_des','require','error,问卷描述不能为空！'), 
        array('type',array('radio','checkbox','textarea','img-radio'),'error,类型异常！',0,in),
        //wj_id验证是不是本人的2是不为空的时候再验证
        array('wj_id','checkID','error,问卷ID异常。',2,'callback'),
        //验证是不是本人的0是存在就验证
        array('question_id','checkQuestion','error,题目ID异常。',0,'callback'),
        */

    );
     
   /**
    * 插入权限
    */
    public function auth_add(){
        //查找权限上级的auth_level然后+1
        if(!empty($_POST['auth_pid']))
        {
            $auth_p_level=$this->field("auth_level")->where("auth_id={$_POST['auth_pid']}")->find();
            $_POST["auth_level"]=$auth_p_level["auth_level"]+1;
        }
        //新页面打开
        $_POST['open']==1?$_POST['open']=1:$_POST['open']=0;

        $res=$this->add($_POST);
        return $res;
    }
     
    /**
     * 显示权限列表
     */
    public function showlist(){
        //后台权限
        $back_authinfo=$this->order("auth_id asc")->select();
        $data['back']=$this->get_tree($back_authinfo);
        return $data;
    }
    /** 递归分类权限
     * @param array $data 权限列表数组
     * @param number $auth_pid
     */
    private function get_tree($data,$auth_pid=0,$clear=false){
        static $res=array(); 
        if($clear)
        {
            $res=array();
        }
        foreach ($data as $v)
	    {
	        if($v['auth_pid']==$auth_pid)
	        {
	            $res[]=$v;
	            // 找子分类
	            $this->get_tree($data,$v['auth_id']);
	        }
	    }
	    return $res;
    }
    /**
     * 根据权限id求出所有子id
     * @param array $data 权限列表数组
     * @param string $auth_id 需要查找的权限id
     */
    private function get_child($data,$auth_id,$clear=false){
        static $res=array();
        if($clear)
        {
            $res=array();
        }
        foreach ($data as $v)
        {
            if($v['auth_pid']==$auth_id)
            {
                $res[]=$v['auth_id'];
                $this->get_child($data,$v['auth_id']);
            }
        }
        return $res;
    }
    
    /**
     * 编辑权限
     */
    public function auth_edit(){
        isset($_POST['open'])?$_POST['open']=1:$_POST['open']=0;
        $z=$this->save($_POST);
        return $z;
    }
    
    /**
     * 删除权限
     */
    public function auth_del(){
        $auth_id=$_POST['auth_id'];
        //先找子分类
        $data=$this->select();
        $ids=$this->get_child($data, $auth_id);
        array_unshift($ids, $auth_id);
        //全部删除
        $ids=implode(",", $ids);
        $z=$this->delete($ids);
        return $z;   
    }
 
     
     
     

    

    
	
}






