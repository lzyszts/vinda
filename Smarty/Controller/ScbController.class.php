<?php
namespace Smarty\Controller;
use Tools\BaseController;
use Smarty\Model\ScbDataModel;
use Smarty\Model\ScbGroupModel;
use Smarty\Model\ScbUserModel;

class ScbController extends BaseController {
    /*
     * 我的记录
     */
    public function change() {
        $userinfo=session("userinfo");
        $name=$userinfo["name"];
        $year=isset($_GET["year"])?$_GET["year"]:date("Y");
        $month=isset($_GET["month"])?$_GET["month"]:date("m");  
        $month=$this->zeroFill($month); //补零
        $model_data=new ScbDataModel();
        $data=$model_data->showlist($year,$month);
        $this->assign("name",$name);
        $this->assign("data",$data);
        $this->display();
    }

    /*
     * 插入页面
     */
    public function create(){
        if($_POST){
           $_POST=I('post.');
           $model_data=new ScbDataModel();
           $res=$model_data->addData();
           if($res)
           {
               echo "1";
           }else{
               echo "0";
           }
            
        }else{
            $model_user=new ScbUserModel();
            $group_name=$model_user->userGroup();
            if($group_name)
            {
                $userinfo=session("userinfo");
                $name=$userinfo["name"];
                $this->assign("group_name",$group_name);
                $this->assign("name",$name);
                $this->display();
            }else{
                $this->error("用户不属于任何车间,请联系管理员添加所属车间！",U("change"),15);
            }
        }
    }
    
    /* Ajax
     * 提交生成年月
     */
    public function date(){
        $arr_month=array('1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月');
        $arr_year=array(
            '2017'=>'2017',
            '2018'=>'2018',
            '2019'=>'2019',
            '2020'=>'2020',
            '2021'=>'2021',
            '2022'=>'2022',
            '2023'=>'2023',
            '2024'=>'2024',
            '2025'=>'2025',
            '2026'=>'2026',
            '2027'=>'2027',
            '2028'=>'2028',
        );
        $now_year=date('Y');
        $now_month=date('n');
        $data['arr_month']=$arr_month;
        $data['arr_year']=$arr_year;
        $data['now_year']=$now_year;
        $data['now_month']=$now_month;
        echo json_encode($data);
    }

    /* Ajax
     * 返回机台 json做下拉
     */
    public function machineList()
    {
        $data=D("scb_change_machine")->order("machine")->select();
        foreach ($data as $k=>$v)
        {
            $json .= json_encode($v).',';
        }
        $json = substr($json, 0, -1);
        echo "[$json]";
    }

    /*
     * 小于10的补0
     */
    public function zeroFill($num){
        if($num<10 && strlen($num)=="1")
        {
            $num="0".$num;
        }
        return $num;
    } 

    public function loginout(){
        session(null);
        header("Location:".SELF_URL);
    }
    
 
    //*********后台***************/
    
    /*
     * 1、后台首页用户列表
     *  data格式
     *  [1] => array(3) {
        ["id"] => string(1) "5"
        ["group_name"] => string(9) "一车间"
        ["userinfo"] => array(1) {
          [0] => array(5) {
            ["id"] => string(1) "5"
            ["group_id"] => string(1) "5"
            ["samaccountname"] => string(6) "105539"
            ["name"] => string(6) "张强"
            ["zhiwei"] => string(12) "仓储主管"
          }
        }
  }
     */
    public function back(){
        $userinfo=session("userinfo");
        $name=$userinfo["name"];
        $model_group=new ScbGroupModel();
        $group_list=$model_group->showlist();
        $this->assign("group_list",$group_list);
        $this->assign("name",$name);
        $this->display();
    }
    
    /*
     * 管理员根据工号查看记录
     */
    public function record(){
        $year=isset($_GET["year"])?$_GET["year"]:date("Y");
        $month=isset($_GET["month"])?$_GET["month"]:date("m");
        $samaccountname=$_GET["samaccountname"];
        $month=$this->zeroFill($month); //补零
        $model_data=new ScbDataModel();     
        $data=$model_data->showUserlist($year,$month,$samaccountname);
        $this->assign("data",$data);
        $this->display();
    }

    /* Ajax
     * 管理员删除用户
     */
    public function delData(){
        $model_data=new ScbDataModel();
        $res=$model_data->delete($_POST['id']);
        if($res)
        {
            echo "1";
        }else{
            echo "0";
        }
    }
    
    /* Ajax
     * 插入分组
     */
    public function addGroup(){
        $_POST=I("post.");
        $model_group=new ScbGroupModel();
        $res=$model_group->addGroup();
        if($res)
        {
           echo "1";
        }else{
           echo "0";
        }
    }
    /* Ajax
     * 插入用户分组
     */
    public function addUser(){
        $_POST=I("post.");
        $model_scb_user=new ScbUserModel();
        $res=$model_scb_user->addUser();
        if($res)
        {
            echo "1";
        }else{
            echo "0";
        }
    }
    /* Ajax
     * 删除分组
     */
    public function delGroup(){
         $model_group=new ScbGroupModel();
         $res=$model_group->delGroup();
         if($res)
         {
             echo "1";
         }else{
             echo "0";
         }  
    }
    /* Ajax
     * 删除用户分组
     */
    public function delUser()
    {
        $_POST=I("post.");
        $model_scb_user=new ScbUserModel();
        $res=$model_scb_user->where("samaccountname={$_POST['gh']}")->delete();
        echo $res;
    }
    /* Ajax
     * 修改分组名
     */
    public function changeGroup(){
        $model_group=new ScbGroupModel();
        $res=$model_group->where("id={$_POST['group_id']}")->save($_POST);
        if($res)
        {
            echo "1";
        }else{
            echo "0";
        }
    }

    
    /* Ajax
     * 工号返回姓名
     */ 
    public function searchUser(){
        $model_scb_user=new ScbUserModel();
        //1、验证是否存在
        $res=$model_scb_user->checkUser();
        if($res===true)
        {
            $data=$model_scb_user->searchUser();
            if(!empty($data["name"]))
            {
                echo json_encode($data);
            }else{
                echo "0";
            }
        }else{
            echo json_encode($res);
        }    
    }

    /*
     * 2、机台管理
     */
    public function machine(){
        $userinfo=session("userinfo");
        $name=$userinfo["name"];
        $data=D("scb_change_machine")->order("machine")->select();
        $this->assign("name",$name);
        $this->assign("data",$data);
        $this->display();
    }
    /* Ajax
     * 插入机台
     */
    public function addMachine(){
        $_POST=I("post.");
        $data["machine"]=trim($_POST["machine"]);
        $res=D("scb_change_machine")->where("machine='{$data['machine']}'")->find();
        if(!$res){
            if(D("scb_change_machine")->add($data))
            {
                echo "1";
            }else{
                echo "0";
            } 
        }else{
            echo "0";   
        }   
    }
    /* Ajax
     * 机台删除
     */
    public function delMachine(){     
        $_POST=I("post.");
        $res=D("scb_change_machine")->delete($_POST["id"]);
        echo $res;
    }
    
    /*
     * 3、数据中心
     */
    public function data(){
        if($_POST)
        {
            $model_data=new ScbDataModel();
            $data=$model_data->search();
            if($data)
            {
                foreach ($data as $k=>$v)
                {
                    $json .= json_encode($v).',';
                }
                $json = substr($json, 0, -1);       
                echo "[".$json."]";     
            }else{
                echo "0";
            }
            
        }else{
            $userinfo=session("userinfo");
            $name=$userinfo["name"];
            $this->assign("name",$name);
            $this->display();
        }  
    }
    /*
     *  根据条件 时间段 车间
     *  导出数据
     */
    public function export(){
        $model_data=new ScbDataModel();
        $res=$model_data->export();
        echo json_encode($res);
    }
    /*
     *  根据年
     *  导出年汇总数据
     */
    public function export_year(){
        $model_data=new ScbDataModel();
        $res=$model_data->export_year();
        echo json_encode($res);
    }
    
    
    
}