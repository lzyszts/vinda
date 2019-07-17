<?php
namespace Data\Controller;
use Tools\BackController;
/*
*车间班产报表控制器
*/
class BcController extends BackController {
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
     *  新增
     */
    
    public function add(){
    	$model_data=D('scb_bc_data');
    	if($_POST)
    	{
    		$_POST=I("post.");
    		$check=$model_data->create($_POST,1);
	        if($check)
	        {
	            $z=$model_data->add(trim($_POST));
	            if($z){
	                echo "1";
	            }else{
	                echo "添加失败!".$model_data->getError();
	            }
	        }else{
	            echo $model_data->getError();
	        }   
    	}else{
            //产品种类信息
            $type_info=D('scb_bc_type')->field('type_name,cp_num')->select();
            //下道工序
            $next_info=D('scb_bc_next')->select();
            //上一条本人信息
            $gh=$this->samaccountname;
            $data=$model_data->where("gh='{$gh}'")->order("id desc")->find();
            $this->assign(array(
                'name'=>$this->name,
                'data'=>$data,
                'type_info'=>$type_info,
                'next_info'=>$next_info,
                'json_type_info'=>json_encode($type_info)
            ));
            $this->display();
        }	
    }
    /**
     * 修改
     */
    public function edit(){
        $model_data=D('scb_bc_data');
        $gh=$this->samaccountname;
        if($_POST)
        {
            $_POST=I("post.");
            $id=$_POST['id'];
            $check=$model_data->create($_POST,2);
            if($check)
            {
                $z=$model_data->where("gh='{$gh}' and id='{$id}' and is_lock=0")->save(trim($_POST));
                if($z==1){
                    echo "1";
                }else{
                    echo "修改失败!".$model_data->getError();
                }
            }else{
                echo $model_data->getError();
            }   
        }else{
            $id=$_GET['id'];
            $data=$model_data->where("gh='{$gh}' and id='{$id}'")->find();
            //产品种类信息
            $type_info=D('scb_bc_type')->field('type_name,cp_num')->select();
            //下道工序
            $next_info=D('scb_bc_next')->select();
            $this->assign(array(
                'name'=>$this->name,
                'data'=>$data,
                'type_info'=>$type_info,
                'next_info'=>$next_info,
                'json_type_info'=>json_encode($type_info)
            ));
            $this->display();
        }   
    }
    /**
     * 删除       
     */
    public function del(){
        $model_data=D('scb_bc_data');
        $_POST=I("post.");
        $gh=$this->samaccountname;
        $id=$_POST['id'];
        $res=$model_data->where("gh='{$gh}' and id='{$id}' and is_lock=0")->delete();
        echo $res;
    }



    /**
     * 我的表单
     */
    public function mylist(){
    	$this->display();
    }
    /**
     * 本人所有表单数据
     */
    public function mydata(){
        $model_data=D('scb_bc_data');
        $pagesize=I('get.pagesize');
        $data=$model_data->my_data($pagesize);
        echo $data;
    }

    /**
     * 本人表单插入完工单号
     */
    public function add_wg_num(){
        $model_data=D('scb_bc_data');
        $res=$model_data->add_wg_num();
        if($res==false)
        {
            echo $model_data->getError();
        }else{
             echo "1";
        }
    }


    /**
     * 单个表单打印数据
     * 调用tcpdf读取printdata这个HTML文件
     */
    public function pri(){
        $id=$_GET['id'];
        import("Tools.tcpdf.tcpdf");
        //页面方向（P =纵向，L =横向）、单位（mm）、页面格式
        $pdf=new \TCPDF('L', 'mm', 'A5', true, 'UTF-8', false);
        //去掉页眉页脚
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        //设置图像比例
        $pdf->setImageScale(2);
        $pdf->setFontSubsetting(true);
        //设置默认等宽字体
        $pdf->SetDefaultMonospacedFont('courier');
        $pdf->SetFont('stsongstdlight', '', 13);
        //边距左上右
        $pdf->SetMargins(15,10, 15);
        //下
        $pdf->SetAutoPageBreak(TRUE, 0);//下
        //$pdf->SetDefaultMonospacedFont(true);
        //创建页面
        $pdf->AddPage();
        $url=U('printdata',array('id'=>$id),'',true);

        $str=file_get_contents($url);
        //输入HTML
        $pdf->writeHTML($str, true, false, true, false, '');
        $pdf->Output('t.pdf', 'I');
    }

       

    //打印需要的html页面
    public function printdata(){
        $id=$_GET['id'];
        $data=D('scb_bc_data')->find($id);
        $this->assign('data',$data);
        $this->display();
    }



    /**
     * 所有表单页面
     */
    public function alllist(){
    	$this->display();
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
     * 导出自己数据
     */
    public function export(){
        $model_data=D('scb_bc_data');
        //根据条件生成数据
        $data=$model_data->export_search();
        //导出
        $url=$model_data->export($data);
        echo json_encode($url);
    }


    public function test(){
    	$this->display();
    }
 
    
}