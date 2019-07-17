<?php
namespace Admin\Model;
use Think\Model;
/*
 *  article_category表
 */
class ArticleCategoryModel extends Model {
    protected $userinfo;
    protected $gh;
    public function __construct(){
        parent::__construct();
        $this->userinfo=session('userinfo');
        $this->gh=$this->userinfo['username'];
    }
    protected $_validate = array(     
        array('cg_name','require','分类名不能为空'),
        array('cg_pid','require','分类上级不能为空'),
        array('cg_pid','number','分类上级错误'),  
        array('cg_id','number','分类id字段异常'),
        array('cg_id','checkId','分类id字段异常',0,'callback',3),
    );
     
     /**
      * 验证ID是否为本人创建的分类
      * @return bool
      */
    function checkId($cg_id){
        $info=$this->find($cg_id);
        if($info['gh']==$this->gh)
        {
            return true;
        }else{
            return false;
        }
    }


   /**
    * 插入分类
    */
    public function cg_add(){
        if($_POST['cg_pid']==1)
        {
            //顶级分类
            $_POST['cg_pid']=0;
        }else{
            //非顶级分类查找分类上级的cg_level然后+1
            $cg_level=$this->field("cg_level")->where("cg_id={$_POST['cg_pid']}")->find();
            $_POST["cg_level"]=$cg_level["cg_level"]+1;
        }
        $userinfo=session('userinfo');
        $_POST['gh']=$userinfo['username'];
        $res=$this->add($_POST);
        return $res;
    }
     
    /**
     * 分类列表数据
     */
    public function showlist(){
        $userinfo=session('userinfo');
        $gh=$userinfo['username'];
        //分类列表
        $categoryinfo=$this->where("gh='{$gh}' and cg_id>1")->order("cg_id asc")->select();
        $data=$this->get_tree($categoryinfo,0,true); 
        return $data;
    }


    /**
     * 编辑分类
     */
    public function category_edit(){
        $z=$this->save($_POST);
        return $z;
    }
    


    /**
     * 删除分类
     */
    public function category_del(){
        $cg_id=$_POST['cg_id'];
        //先找子分类
        $data=$this->select();
        $ids=$this->get_child($data, $cg_id);
        array_unshift($ids, $cg_id);
        //全部删除
        $ids=implode(",", $ids);
        $z=$this->delete($ids);
        return $z;   
    }



/***************************各种方法**********************************/

    /** 递归分类
     * @param array $data 分类列表数组
     * @param number $cg_pid
     */
    private function get_tree($data,$cg_pid=0,$clear=false){
        static $res=array(); 
        if($clear)
        {
            $res=array();
        }
        foreach ($data as $v)
	    {
	        if($v['cg_pid']==$cg_pid)
	        {
	            $res[]=$v;
	            // 找子分类
	            $this->get_tree($data,$v['cg_id']);
	        }
	    }
	    return $res;
    }
    /**
     * 根据分类id求出所有子id
     * @param array $data 分类列表数组
     * @param string $cg_id 需要查找的分类id
     */
    public function get_child($data,$cg_id,$clear=false){
        static $res=array();
        if($clear)
        {
            $res=array();
        }
        foreach ($data as $v)
        {
            if($v['cg_pid']==$cg_id)
            {
                $res[]=$v['cg_id'];
                $this->get_child($data,$v['cg_id']);
            }
        }
        return $res;
    }
  
    /**
     * 生成树需要的数据格式
     * var homeContent =[{auth_id:28,cg_pid:0,name:'TPM提报',open:true},{auth_id:29,cg_pid:28,name:'我的改善'}];
     * @param  [array] $data 无限分类的数据
     * @param  [type] $id   id字段的名
     * @param  [type] $pid  pid字段名
     * @param  [type] $name 分类名的字段名
     * @param  [type] $is_top  是否需要显示顶级分类选项
     * @return [string]       组合好的字符串
     */
    public function tree_data($data,$id,$pid,$name,$is_top=false){
        if($is_top)
        {
          $str="[{".$id.":1,".$pid.":0,"."name:'顶级分类'"."},";
        }else{
          $str="[";
        }
        foreach ($data as $v) 
        {
            $state=false; //默认无子节点
            //判断当前元素还有子节点没
            foreach($data as $value)
            {
               //如果有子节点
               if($value[$pid]==$v[$id]){
                  $state=true;
                  break 1;
               }
            }
            if($state)
            {
              $str.= "{".$id.":".$v[$id].",".$pid.":".$v[$pid].",name:'".$v[$name]."'},";
            }else{
              $str.= "{".$id.":".$v[$id].",".$pid.":".$v[$pid].",name:'".$v[$name]."',icon:'".ADMIN_IMG_URL."/file.png'},";
            } 
        }
        $str.="]";
        return $str;
    }



    

    
	
}






