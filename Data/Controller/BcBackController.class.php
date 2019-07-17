<?php
namespace Data\Controller;
use Tools\BackController;
/*
*车间班产报表后台控制器
*/
class BcBackController extends BackController {
    private $userinfo;
    private $samaccountname;
    private $name;
    
    public function __construct(){
    	parent::__construct();
    	$userinfo=session("userinfo");
    	$this->userinfo=$userinfo;
    	$this->samaccountname=$userinfo['samaccountname'];
    	$this->name=$userinfo['name'];
    }
    /**
     * 首页
     */
    public function index(){
        $this->display();
    }

    /**
     * 后台导出搜索条件的数据
     */
    public function export(){
        $model_data=D('scb_bc_data');
        //根据时间生成数据,true是管理员
        $data=$model_data->export_search(true);
        //导出
        $url=$model_data->export($data);
        echo json_encode($url);
    }

     /**
     * 财务报表数据查询
     */
    public function cw_search(){ 
        $model_data=D('scb_bc_data');
        $pagesize=I('get.pagesize')?I('get.pagesize'):10;
        $data=$model_data->cw_data_search($pagesize);
        echo $data;
    }


    /**
     * 财务所需表单导出
     */
    public function export_cw(){
        $model_data=D('scb_bc_data');
        //根据时间生成数据
        $data=$model_data->search_cw_data();
        //处理财务需要数据
        $data=$model_data->make_cw_data($data);
        //导出
        $url=$model_data->export_cw($data);
        echo json_encode($url);
    }

    /**
     * 财务页面锁定表单
     */
    public function lock_data(){
        $model_data=D('scb_bc_data');
        $wg_nums=implode(",", $_POST['wg_nums']);
        if($_POST['state']=="lock")
        {
            $res=$model_data->lock_data("lock",$wg_nums);
        }else if($_POST['state']=="unlock"){
            $res=$model_data->lock_data("unlock",$wg_nums);
        }
        echo $res;
    }


     /**
     * 所有表单数据
     */
    public function alldata(){
        $model_data=D('scb_bc_data');
        $pagesize=I('get.pagesize');
        $data=$model_data->all_data($pagesize);
        echo $data;
    }
    /**
     * 产品种类维护
     */
    public function type(){
        $data=D('scb_bc_type')->select();
        $next_data=D('scb_bc_next')->select();
        $this->assign(array(
            "data"=>$data,
            "next_data"=>$next_data
        ));
        $this->display();
    }
    /**
     * 所有数据查询
     */
    public function search(){
        $model_data=D('scb_bc_data');
        $pagesize=I('get.pagesize')?I('get.pagesize'):10;
        $data=$model_data->data_search($pagesize);
        echo $data;
    }





    /**
     * 产品种类添加
     */

    public function type_add(){
        $_POST=I("post.");
        $type=$_POST['type_name'];
        $num=$_POST['cp_num'];
        $model_type=D('scb_bc_type');
        $res=$model_type->where("type_name='{$type}' or cp_num='{$num}'")->find();
        if($res)
        {
            echo "产品名或产品编码已存在！";
        }else{
            $r=$model_type->add($_POST);
            echo $r;
        }
    }

    /**
     * 产品种类删除
     */

    public function del(){
        $model_type=D('scb_bc_type');
        $_POST=I('post.');
        $res=$model_type->delete($_POST['id']);
        echo $res;
    }


    /**
     * 下道工序添加
     */

    public function next_add(){
        $_POST=I("post.");
        $type=$_POST['next_name'];
        $model_type=D('scb_bc_next');
        $res=$model_type->where("next_name='{$type}'")->find();
        if($res)
        {
            echo "下道工序已存在！";
        }else{
            $r=$model_type->add($_POST);
            echo $r;
        }
    }
    /**
     * 下道工序删除
     */

    public function next_del(){
        $model_type=D('scb_bc_next');
        $_POST=I('post.');
        $res=$model_type->delete($_POST['id']);
        echo $res;
    }


    /**
     * 数据分析页面
     */
    public function analysis(){
        $this->display();
    }


    /**
     * 单产品柱状图总净重数据
     */
    public function lineData(){
        $model_data=D('scb_bc_data');
        //查询
        $res=$model_data->lineDataSearch();
        //转换为柱状图数据
        $res['data']=$model_data->lindDataZ($res['data']);
        echo json_encode($res);
    }

     /**
     * 所有产品柱状图总净重数据
     */
    public function cpData(){
        $model_data=D('scb_bc_data');
        //查询
        $res=$model_data->lineDataSearch(true);
        //转换为柱状图数据
        $res['data']=$model_data->lindDataA($res['data']);
        echo json_encode($res);
    }







}