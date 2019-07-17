<?php
namespace Data\Controller;
use Tools\BaseController;

class ZzbController extends BaseController {
    
    public function kpi(){
    	$title=D('zzb_kpi_title')->select();
        $this->assign('title',$title);
        $this->display();
    }

    /**
     * layui返回表格数据
     * 天的数据
     */
    public function day_data(){
        $data=D('zzb_kpi_day')->select();
        $count=D('zzb_kpi_day')->count();
        $data_str=$this->lay_data($data,$count);
        echo $data_str;
    }
    /**
     * layui返回表格数据
     * 月的数据
     */
    public function month_data(){
        $data=D('zzb_kpi_month')->select();
        $count=D('zzb_kpi_month')->count();
        $data_str=$this->lay_data($data,$count);
        echo $data_str;
    }
    /**
     * layui返回表格数据
     * 年的数据
     */
    public function year_data(){
    	$data=D('zzb_kpi_year')->select();
        $count=D('zzb_kpi_year')->count();
        $data_str=$this->lay_data($data,$count);
		echo $data_str;
    }

    /**
     * ajax修改表单数据
     */
    public function change(){
        $_POST=I('post.');
        $data['id']=$_POST['id'];
        $data[$_POST['field']]=$_POST['value'];
        $table_name='zzb_kpi_'.$_POST['type'];
        $res=D($table_name)->save($data);
        echo $res;
    }
    /**
     * ajax修改表单标题
     */
    public function change_title(){
        $_POST=I('post.');
        $res=D("zzb_kpi_title")->save($_POST);
        echo $res;
    }




    public function lay_data($data,$count){
        $data_str='{"code":0,"msg":"","count":'.$count;
        foreach ($data as $k=>$v)
        {
            $json .= json_encode($v).',';
        }
        $json = substr($json, 0, -1);
        $data_str.=',"data":['.$json.']}';
        return $data_str;
    }
 
    
}