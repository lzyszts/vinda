<?php
namespace Smarty\Model;
use Think\Model;
/*
 * 生产部车间转交记录数据模型
 */
class ScbDataModel extends Model {
   protected $trueTableName = 'scb_change_data';
   
   /*
    * 显示
    */
   public function showlist($year,$month){
       $userinfo=session("userinfo");
       $date=$year."-".$month;
       $data=$this->where("samaccountname={$userinfo['samaccountname']}  and date like '{$date}%'")->order("date desc")->select();
       return $data;
   }
   /*
    * 后台管理员显示用户数据
    */
   public function showUserlist($year,$month,$samaccountname){
       $date=$year."-".$month;
       $data=$this->where("samaccountname='{$samaccountname}'  and date like '{$date}%'")->order("date desc")->select();
       return $data;
   } 
   
   /*
    * 插入
    */
    public function addData() {
        $userinfo=session("userinfo");
        $_POST["samaccountname"]=$userinfo["samaccountname"];
        $_POST["create_time"]=time();
        $old=strtoupper($_POST["old"]);
        $new=strtoupper($_POST["new"]);
        $_POST["old"]=trim($old);
        $_POST["new"]=trim($new);
        $res=$this->add($_POST);
        if($res)
        {
            return true;
        }else{
            return false;
        }   
    }
    /*
     * 汇总查询
     * 根据日期段+车间查询
     */
    public function search(){
        $_POST=I("post.");
        $where=array();
        //时间
        $time_left=$_POST["time_left"];
        $time_right=$_POST["time_right"];
        $where["date"]=array("between",array($time_left,$time_right));
        //车间
        $group_id=$_POST["group"];
        if($group_id)
        {
            $group=D("scb_change_group")->find($group_id);
            $group_name=$group["group_name"];
            $where["group_name"]=array("eq",$group_name);
        }
        $data=$this->where($where)->order("name")->select();
        return $data;
    }
    
    

    
    
    /*
     * 导出Excel,返回下载url
     */
    public function export(){
        //1、生成查询信息
        $data=$this->search();
        //2、生成Excel表
        import("Tools.PHPExcel");
        import("Tools.PHPExcel.Writer.Excel2007");
        $PHPExcel=new \PHPExcel();
        //当前sheet对象
        $objSheet=$PHPExcel->getActiveSheet();
        $len=count($data);
        $group=D("scb_change_group")->find($_POST["group"]);
        $group_name=$group?$group["group_name"]:"所有";
        $title=$_POST["time_left"]."至".$_POST["time_right"].$group_name."转产记录明细表";
        //1行
        $objSheet->mergeCells("A1:L1");
        $objSheet->setCellValue('A1',$title);
        //2行
        $objSheet
        ->setCellValue('A2','序号')
        ->setCellValue('B2','日期')
        ->setCellValue('C2','起始时间')
        ->setCellValue('D2','结束时间')
        ->setCellValue('E2','机台')
        ->setCellValue('F2','机长')
        ->setCellValue('G2','车间')
        ->setCellValue('H2','原产品')
        ->setCellValue('I2','更换产品')
        ->setCellValue('J2','实际耗时(小时)')
        ->setCellValue('K2','标准转产时间')
        ->setCellValue('L2','补产时间')    
        ->setCellValue('M2','8h标准产量')    
        ->setCellValue('N2','产量损失');     
        for($i=0;$i<$len;$i++)
        {
            $current_row=$i+3;     
            $objSheet
            ->setCellValue("A$current_row",$i+1)   //序号:3行1开始
            ->setCellValue("B$current_row",$data[$i]['date'])
            ->setCellValue("C$current_row",$data[$i]['time_left'])
            ->setCellValue("D$current_row",$data[$i]['time_right'])
            ->setCellValue("E$current_row",$data[$i]['machine'])
            ->setCellValue("F$current_row",$data[$i]['name'])
            ->setCellValue("G$current_row",$data[$i]['group_name'])
            ->setCellValue("H$current_row",$data[$i]['old'])
            ->setCellValue("I$current_row",$data[$i]['new'])
            ->setCellValue("J$current_row",$data[$i]['decimal'])
            ->setCellValue("K$current_row",$data[$i]['standard'])
            ->setCellValue("L$current_row",$data[$i]['supply'])
            ->setCellValue("M$current_row",$data[$i]['8h_biaozhun'])
            ->setCellValue("N$current_row",$data[$i]['sunshi']);
        }
        $objWriter=\PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");
        $date = date("Y.m.d",time());

        $fileName = $title.rand().".xlsx";
        $fileName = iconv("utf-8", "gb2312", $fileName);
        $objWriter->save("./download/scb/$fileName");
        //再转回去
        $fileName = iconv("gb2312","utf-8", $fileName);
        $res["url"]="/download/scb/$fileName";
        $res["filename"]=$fileName;
        return $res;
    }
    
    
    public function year_data(){
        $_POST=I("post.");
        $year=$_POST["year"];
        $data=$this->where("date like '{$year}-%'")->order("new")->select();//所有转产产品
        return $data;
    }
    
    
    /*
     * 导出年数据Excel,返回下载url
     */
    public function export_year(){
        //1、生成查询信息
        $data=$this->year_data();
        //2、生成Excel表
        import("Tools.PHPExcel");
        import("Tools.PHPExcel.Writer.Excel2007");
        $PHPExcel=new \PHPExcel();
        //当前sheet对象
        $objSheet=$PHPExcel->getActiveSheet();
        $len=count($data);
        $title=$_POST["year"]."转产记录汇总表";
        //1行
        $objSheet->mergeCells("A1:F1");
        $objSheet->setCellValue('A1',$title);
        //2行
        $objSheet
        ->setCellValue('A2','序号')
        ->setCellValue('B2','转产产品')
        ->setCellValue('C2','机台')
        ->setCellValue('D2','车间')
        ->setCellValue('E2','标准转产时间')
        ->setCellValue('F2','补产时间');     
        for($i=0;$i<$len;$i++)
        {
            $current_row=$i+3;     
            $objSheet
            ->setCellValue("A$current_row",$i+1)   //序号:3行1开始
            ->setCellValue("B$current_row",$data[$i]['new'])
            ->setCellValue("C$current_row",$data[$i]['machine'])
            ->setCellValue("D$current_row",$data[$i]['group_name'])
            ->setCellValue("E$current_row",$data[$i]['standard'])
            ->setCellValue("F$current_row",$data[$i]['supply']);
        }
        $objWriter=\PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");
        $date = date("Y.m.d",time());
        $fileName = $title.rand().".xlsx";
        $fileName = iconv("utf-8", "gb2312", $fileName);
        $objWriter->save("./download/scb/year/$fileName");
        //再转回去
        $fileName = iconv("gb2312","utf-8", $fileName);
        $res["url"]="/download/scb/year/$fileName";
        $res["filename"]=$fileName;
        return $res;
    }
    
    
    
    
    
    
    //找出某车间所有工号
    public function test(){
        $ghs=D("scb_change_user")->alias("as a")
        ->field("samaccountname")
        ->join("left join scb_change_group as b on a.group_id=b.id")
        ->where("b.id={$group_id}")->select();
        $ghs=i_array_column($ghs,"samaccountname");
        $ghs=implode(",",$ghs);
        //$where["samaccountname"]=array("in",$ghs);
    }
    
    
    
}