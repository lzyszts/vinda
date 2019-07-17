<?php
namespace Smarty\Controller;
use Tools\BaseController;

class AqkController extends BaseController {
   /*
    * 展示
    */
    public function admin() {
       $data=D('aqk_kpi')->where('id<=4')->select();
       $safe_day=D('aqk_kpi')->where("dept='安全天数'")->find();
       $this->assign('data',$data);
       $this->assign('safe_day',$safe_day);
       $this->display();
    }
    /*
     * 修改时间
     */
    public function modify(){
       //1、处理安全天数
       $data['no_die']=strtotime($_POST['safe_day']);
       unset($_POST['safe_day']); 
       //2、转换所有日期成时间戳
       foreach ($_POST as $key=>$value){//$key是生产部
           foreach ($value as $k=>$v){ //$k是no_die $v是2016-01-01
              $_POST[$key][$k]=strtotime($v);
           }
       }
       //3、更新数据
       D('aqk_kpi')->where("dept='生产部'")->save($_POST['生产部']);
       D('aqk_kpi')->where("dept='制造部'")->save($_POST['制造部']);
       D('aqk_kpi')->where("dept='工程技术部'")->save($_POST['工程技术部']);
       D('aqk_kpi')->where("dept='仓储科'")->save($_POST['仓储科']);
       D('aqk_kpi')->where("dept='安全天数'")->save($data);
       //4、跳转回去
       $this->redirect('admin');
    }
    
    
    
}