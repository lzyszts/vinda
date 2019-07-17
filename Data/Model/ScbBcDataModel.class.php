<?php
namespace Data\Model;
use Think\Model;
use Tools\Page;
/*
 *  生产部报表数据表
 */
class ScbBcDataModel extends Model {
    private $userinfo;
    private $samaccountname;
    
    public function __construct(){
        parent::__construct();
        $userinfo=session("userinfo");
        $this->userinfo=$userinfo;
        $this->samaccountname=$userinfo['samaccountname'];
    }

    protected $_validate = array(
        array('jt_num', 'require', '机台编号不能为空！', 1, 'regex', 3),
        array('jt_num', 'number', '机台编号必须是一个整数！', 1, 'regex', 3),
        array('banci', 'require', '班次不能为空！', 1, 'regex', 3),
        array('banzu', 'require', '班组不能为空！', 1, 'regex', 3),
        array('banzu', 'number', '班组必须是一个数字！', 1, 'regex', 3),
        array('sc_num', 'number', '生产批号必须是一个数字！', 1, 'regex', 3),
        array('date', 'require', '日期不能为空！', 1, 'regex', 3),
        array('up_date', 'require', '下纸时间不能为空！', 1, 'regex', 3),
        array('cp_class', 'require', '产品种类不能为空！', 1, 'regex', 3),
        array('speed', 'require', '车速(m/min)不能为空！', 1, 'regex', 3),
        array('zhouzhong', 'require', '轴重不能为空！', 1, 'regex', 3),
        array('dingliang', 'require', '定量(g/m3)不能为空！', 1, 'regex', 3),
        array('zhou_num', 'require', '原值张轴号不能为空！', 1, 'regex', 3),
        array('guige', 'require', '规格不能为空！', 1, 'regex', 3),
        array('juanchang', 'require', '卷长(m)不能为空！', 1, 'regex', 3),
        array('maozhong', 'require', '毛重不能为空！', 1, 'regex', 3),
        array('maozhong', 'number', '毛重必须是一个数字！', 1, 'regex', 3),
        array('jingzhong', 'require', '净重不能为空！', 1, 'regex', 3),
        array('jingzhong', 'number', '净重必须是一个数字！', 1, 'regex', 3),
        array('buhege', 'number', '不合格重量必须是一个数字！', 1, 'regex', 3),
        array('zhijing', 'require', '直径(mm)不能为空！', 1, 'regex', 3),
        array('is_hg', 'require', '是否合格不能为空！', 1, 'regex', 3),
        array('is_hd', 'require', '换刀情况不能为空！', 1, 'regex', 3),
        array('is_dt', 'require', '断头情况不能为空！', 1, 'regex', 3),
        array('zz_level', 'require', '皱纹级别不能为空！', 1, 'regex', 3),
        array('next', 'require', '下道工序不能为空！', 1, 'regex', 3),
        array('bz', '0,90', '备注90个字符！', 1, 'length', 3),
    );
    
    /**
     * 造纸数据表插入
     */
    protected function _before_insert(&$data, $option){
        $userinfo=$this->userinfo;
        $data['gh']=$userinfo['samaccountname'];
        $data['name']=$userinfo['name'];
        $data['zhou_num']=strtoupper($data['zhou_num']);
        $data['addtime']=time();
    }

    /**
     * 造纸数据修改
     */
    protected function _before_update(&$data, $option){
        if(isset($data['zhou_num']))
        {
            $data['zhou_num']=strtoupper($data['zhou_num']);
        }
        $data['edittime']=time();
    }




    /**
     * 本人数据
     */
    public function my_data($pagesize){
        $gh=$this->userinfo['samaccountname'];
        $count=$this->count();//查询数据总数
        $page=new Page($count,$pagesize);
        $limit=substr($page->limit,6);
        $data=$this
        ->where("gh='{$gh}'")
        ->limit($limit)
        ->order('id desc')
        ->select();
        //转换为接口格式
        $data_str=$this->layui_data($data,$count);
        return $data_str;
    }    
    /**
     * 完工单号批量插入
     */
    public function add_wg_num(){
        $_POST=I("post.");
        $wg_num=$_POST['wg_num'];
        if(!is_numeric($wg_num)){
            $this->error="完工单号必须是一个数字！";
            return false;
        }
        $old_data=$this->where("wg_num='{$wg_num}'")->select();
        //查询拥有此完工单号的数据是否有锁定的
        foreach ($old_data as $v) {
            if($v['is_lock']==1)
            {
                $this->error="此完工单号表单已锁定，如需插入新数据，请联系管理员解锁！";
                return false;
                break;
            }
        }


        $ids=implode(",", $_POST['ids']);
        $res=$this->where("id in({$ids})")->save(array("wg_num"=>$wg_num));
        return $res;
    }


    /**
     * 所有数据
     */
    public function all_data($pagesize){
        $count=$this->count();//查询数据总数
        $page=new Page($count,$pagesize);
        $limit=substr($page->limit,6);
        $data=$this
        ->limit($limit)
        ->order('id desc')
        ->select();
        //转换为接口格式
        $data_str=$this->layui_data($data,$count);
        return $data_str;
    }
    /**
     * 查询
     */
    public function data_search($pagesize){
        $_GET=I('get.');
        $count=$this->count();//查询数据总数
        $page=new Page($count,$pagesize);
        $limit=substr($page->limit,6);
        //查询的条件
        $date_start=$_GET['date_start'];
        $date_end=$_GET['date_end'];
        $title_class=$_GET['title_class'];
        $title=$_GET['title'];
        //查询条件
        if(!empty($date_start) && empty($date_end)){
            $where['date']=array("egt",$date_start);
        }
        if(!empty($date_end) && empty($date_start)){
            $where['date']=array("elt",$date_end);
        }
        if(!empty($date_end) && !empty($date_start)){
            $where['date']=array("between",array($date_start,$date_end));
        }
        //搜索框
        if(!empty($title_class) && !empty($title)){
            $title=$_GET['title'];
            //姓名
            if($title_class==1)
            {
                $where['name']=array("eq",$title);
            }
            //班组
            if($title_class==2)
            {
                $where['banzu']=array("eq",$title);
            }
            //机台编号
            if($title_class==3)
            {
                $where['jt_num']=array("eq",$title);
            }
            //产品种类
            if($title_class==4)
            {
                $where['cp_class']=array("like","%".$title."%");
            }
        }
        $data=$this
        ->where($where)
        ->limit($limit)
        ->order('id desc')
        ->select();
        $count=$this->where($where)->count();
        //转换为接口格式
        $data_str=$this->layui_data($data,$count);
        return $data_str;
    }








    /**
     * 查询导出的数据
     * admin为true的时候是后台导出
     * false为前台用户导出，需要增加工号筛选
     */
    public function export_search($is_admin=false){
        $_POST=I('post.');
        //查询的条件
        $date_start=$_POST['date_start'];
        $date_end=$_POST['date_end'];
        //查询条件
        $where['date']=array("between",array($date_start,$date_end)); 
        if(!$is_admin)
        {
            $where['gh']=$this->samaccountname;
        }
        $data=$this->where($where)->select(); 
        return $data;
    }

    /**
     * 根据数据生成excel
     * @return [array]  导出数据状态,URL
     */
    public function export($data){
        import("Tools.PHPExcel");
        import("Tools.PHPExcel.Writer.Excel5");
        $PHPExcel=new \PHPExcel();
        //当前sheet对象
        $objSheet=$PHPExcel->getActiveSheet();
        $len=count($data);
        $title=$_POST["date_start"]."至".$_POST["date_end"]."明细表";
        //1行
        $objSheet->mergeCells("A1:AA1");
        $objSheet->setCellValue('A1',$title);
        //2行
        $objSheet
        ->setCellValue('A2','序号')
        ->setCellValue('B2','姓名')
        ->setCellValue('C2','工号')
        ->setCellValue('D2','原纸张轴号')
        ->setCellValue('E2','生产批号')
        ->setCellValue('F2','日期')
        ->setCellValue('G2','下纸时间')
        ->setCellValue('H2','产品种类')
        ->setCellValue('I2','产品编码')
        ->setCellValue('J2','定量(g/m3)')
        ->setCellValue('K2','机台编号')
        ->setCellValue('L2','班次')
        ->setCellValue('M2','班组')        
        ->setCellValue('N2','车速(m/min)')  
        ->setCellValue('O2','规格')    
        ->setCellValue('P2','卷长')    
        ->setCellValue('Q2','毛重(kg)')    
        ->setCellValue('R2','净重(KG)')    
        ->setCellValue('S2','直径(mm)')
        ->setCellValue('T2','是否合格')
        ->setCellValue('U2','换刀情况')
        ->setCellValue('V2','断头情况')
        ->setCellValue('W2','皱纹级别')
        ->setCellValue('X2','下道工序')
        ->setCellValue('Y2','备注')
        ->setCellValue('Z2','完工单号')
        ->setCellValue('AA2','轴重');
        for($i=0;$i<$len;$i++)
        {
            $current_row=$i+3;     
            $objSheet
            ->setCellValue("A$current_row",$i+1)   //序号:3行1开始
            ->setCellValue("B$current_row",$data[$i]['name'])
            ->setCellValue("C$current_row",$data[$i]['gh'])
            ->setCellValue("D$current_row",$data[$i]['zhou_num'])
            ->setCellValue("E$current_row",$data[$i]['sc_num'])
            ->setCellValue("F$current_row",$data[$i]['date'])
            ->setCellValue("G$current_row",$data[$i]['up_date'])
            ->setCellValue("H$current_row",$data[$i]['cp_class'])
            ->setCellValue("I$current_row",$data[$i]['cp_num'])
            ->setCellValue("J$current_row",$data[$i]['dingliang'])
            ->setCellValue("K$current_row",$data[$i]['jt_num'])
            ->setCellValue("L$current_row",$data[$i]['banci'])
            ->setCellValue("M$current_row",$data[$i]['banzu'])
            ->setCellValue("N$current_row",$data[$i]['speed'])
            ->setCellValue("O$current_row",$data[$i]['guige'])
            ->setCellValue("P$current_row",$data[$i]['juanchang'])
            ->setCellValue("Q$current_row",$data[$i]['maozhong'])
            ->setCellValue("R$current_row",$data[$i]['jingzhong'])
            ->setCellValue("S$current_row",$data[$i]['zhijing'])
            ->setCellValue("T$current_row",$data[$i]['is_hg'])
            ->setCellValue("U$current_row",$data[$i]['is_hd'])
            ->setCellValue("V$current_row",$data[$i]['is_dt'])
            ->setCellValue("W$current_row",$data[$i]['zz_level'])
            ->setCellValue("X$current_row",$data[$i]['next'])
            ->setCellValue("Y$current_row",$data[$i]['bz'])
            ->setCellValue("Z$current_row",$data[$i]['wg_num'])
            ->setCellValue("AA$current_row",$data[$i]['zhouzhong']);
        }
        $objWriter=\PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");
        $date = date("Y.m.d",time());
        $fileName = $date."造纸数据".rand().".xlsx";
        $fileName = iconv("utf-8", "gb2312", $fileName);
        $objWriter->save("./Download/zz/$fileName");
        //再转回去
        $fileName = iconv("gb2312","utf-8", $fileName);
        $res["url"]="/Download/zz/$fileName";
        $res["filename"]=$fileName;
        return $res;
    }


    /**
     * 财务导出数据查询
     */
    public function cw_data_search($pagesize){
        $_GET=I('get.');
        //查询的条件
        $date_start=$_GET['date_start'];
        $date_end=$_GET['date_end'];
        //查询条件
        $where['date']=array("between",array($date_start,$date_end)); 
        $data=$this->where($where)->order("wg_num,id desc")->select(); 
        //转换为财务的数据
        $data=$this->make_cw_data($data);
        $count=count($data);
        //转换为接口格式
        $data_str=$this->layui_data($data,$count);
        return $data_str;
    }

    /**
     * 财务报表导出的数据
     */
    public function search_cw_data(){
        $_POST=I('post.');
        //查询的条件
        $date_start=$_POST['date_start'];
        $date_end=$_POST['date_end'];
        //查询条件
        $where['date']=array("between",array($date_start,$date_end)); 
        $data=$this->where($where)->order("wg_num,id desc")->select(); 
        return $data;
    }

    /**
     * 制作财务报表所需数据
     */
    public function make_cw_data($data){
        $bc=array("A"=>"早","B"=>"中","C"=>"晚");
        //按照完工单号分组
        foreach ($data as $v) 
        {
            //必须有完工单号
            if(!empty($v['wg_num']))
            {
                $arr[$v['wg_num']][]=$v;
            }
          
        }  
        foreach($arr as $k=>$v)
        {
            //k完工单号 v当前完工单号对应所有单子
            $count=0; //造了多少轴
            $is_dt=0; //断头
            $is_hd=0; //换刀
            $zz_level_A=0;
            $zz_level_B=0;
            $zz_level_C=0;
            $jingzhong=0;
            $is_lock=0;
            $next=array();//下道工序取本组出现次数最多的
            foreach ($v as $key=>$value) 
            {
               if($key==0)
               {
                  $res[$k]['date']=$value['date'];
                  $res[$k]['banci']=$bc[$value['banci']];
                  $res[$k]['banzu']=$value['banzu'];
                  $res[$k]['jt_num']=$value['jt_num'];
                  $res[$k]['sc_num']=$value['sc_num'];
                  $res[$k]['wg_num']=$k;
                  $res[$k]['cp_num']=$value['cp_num'];
                  $res[$k]['cp_class']=$value['cp_class'];
               } 
               $is_dt+=$value['is_dt'];
               $jingzhong+=$value['jingzhong'];
               $value['is_hd']=="换刀"?$is_hd++:"";
               $value['zz_level']=="A"?$zz_level_A++:"";
               $value['zz_level']=="B"?$zz_level_B++:"";
               $value['zz_level']=="C"?$zz_level_C++:"";
               $count++;        
               if($value['is_lock']==1)
               {
                  $is_lock++;    
               }  
               $next[$value['next']]+=1;      
            }
            //$is_dt>1?$res[$k]['dt_dayu_1']=$is_dt:$res[$k]['dt_dayu_1']="";
            //$is_dt==1?$res[$k]['is_dt']=1:$res[$k]['is_dt']="";
            $res[$k]['is_dt']=$is_dt;
            $res[$k]['is_hd']=$is_hd;
            $res[$k]['juanzhou']=$count;
            $res[$k]['zz_level_A']=$zz_level_A;
            $res[$k]['zz_level_B']=$zz_level_B;
            $res[$k]['zz_level_C']=$zz_level_C;   
            $res[$k]['jingzhong']=$jingzhong;   
            $is_lock>0?$res[$k]['is_lock']=1:$res[$k]['is_lock']=0;   
            //下道工序取出现最多的
            $res[$k]['next']=array_search(max($next),$next);
        }

        $i=0;
        //把结果数据的key变成0、1、2、3
        foreach ($res as $k=>$v) {
            $result[$i]=$v;
            $i++;
        }
//dump($result);
        return $result;

    }


    /**
     * 财务所需报表
     * 根据数据生成excel
     * @return [array]  导出数据状态,URL
     */
    public function export_cw($data){
        import("Tools.PHPExcel");
        import("Tools.PHPExcel.Writer.Excel5");
        $PHPExcel=new \PHPExcel();
        //当前sheet对象
        $objSheet=$PHPExcel->getActiveSheet();
        $len=count($data);
        $title=$_POST["date_start"]."至".$_POST["date_end"]."造纸机产量及单耗量";
        //1行
        $objSheet->mergeCells("A1:P1");
        $objSheet->setCellValue('A1',$title);
        //2行
        $objSheet
        ->setCellValue('A2','时间')
        ->setCellValue('B2','班次')
        ->setCellValue('C2','班组')
        ->setCellValue('D2','机台')
        ->setCellValue('E2','生产批号')
        ->setCellValue('F2','完工单号')
        ->setCellValue('G2','产品编码')
        ->setCellValue('H2','产品种类')
        ->setCellValue('I2','断头总数')
        ->setCellValue('J2','换刀情况')
        ->setCellValue('K2','卷轴数')
        ->setCellValue('L2','皱纹级别A')
        ->setCellValue('M2','皱纹级别B')        
        ->setCellValue('N2','皱纹级别C')  
        ->setCellValue('O2','合格纸重KG')    
        ->setCellValue('P2','下道工序');    
        for($i=0;$i<$len;$i++)
        {
            $current_row=$i+3;     
            $objSheet
            ->setCellValue("A$current_row",$data[$i]['date'])
            ->setCellValue("B$current_row",$data[$i]['banci'])
            ->setCellValue("C$current_row",$data[$i]['banzu'])
            ->setCellValue("D$current_row",$data[$i]['jt_num'])
            ->setCellValue("E$current_row",$data[$i]['sc_num'])
            ->setCellValue("F$current_row",$data[$i]['wg_num'])
            ->setCellValue("G$current_row",$data[$i]['cp_num'])
            ->setCellValue("H$current_row",$data[$i]['cp_class'])
            ->setCellValue("I$current_row",$data[$i]['is_dt'])
            ->setCellValue("J$current_row",$data[$i]['is_hd'])
            ->setCellValue("K$current_row",$data[$i]['juanzhou'])
            ->setCellValue("L$current_row",$data[$i]['zz_level_A'])
            ->setCellValue("M$current_row",$data[$i]['zz_level_B'])
            ->setCellValue("N$current_row",$data[$i]['zz_level_C'])
            ->setCellValue("O$current_row",$data[$i]['jingzhong'])
            ->setCellValue("P$current_row",$data[$i]['next']);
        }
        //行宽
        for($i="G";$i<="P";$i++)
        {
            $objSheet-> getColumnDimension($i) -> setWidth(13);
        }
        $objSheet-> getColumnDimension("H") -> setWidth(50);

        $objWriter=\PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");
        $date = date("Y.m.d",time());
        $fileName = $date."造纸数据".rand().".xlsx";
        $fileName = iconv("utf-8", "gb2312", $fileName);
        $objWriter->save("./Download/zz/$fileName");
        //再转回去
        $fileName = iconv("gb2312","utf-8", $fileName);
        $res["url"]="/Download/zz/$fileName";
        $res["filename"]=$fileName;
        return $res;
    }



    /**
     * 柱状图数据查询
     * is_cp为true时只查询时间段的所有数据，不筛选产品
     */
    public function lineDataSearch($is_all_data=false){
        $where=array();
        $_POST=I("post.");
        //没有日期传来则取本月1号到现在
        $today=Date("Y-m-d");
        $date_start=$_POST['date_start'];
        $date_end=$_POST['date_end'];
        if(empty($_POST['date_start'])){
            $date_start=date('Y-m-01', strtotime($today));
        }
        if(empty($_POST['date_end'])){
            $date_end=$today;    
        }
        if(!$is_all_data){
            $cp_class=$_POST['cp_class'];
            $where['cp_class']=array("eq",$cp_class);
        }        
        $where['date']=array("between",array($date_start,$date_end));
        $data=$this->where($where)->order("date asc")->select();
        //为了防止刷新页面的时候layui时间没有加载出来所以服务器返回时间
        $res['data']=$data;
        $res['date_start']=$date_start;
        $res['date_end']=$date_end;
        return $res;
    }


    /**
     * 数据处理成柱状图需要格式
     * key机台，v是这个产品的总净重
     * [1]=>100.22
     */
    public function lindDataZ($data){
        $res=array();
        foreach ($data as $k => $v) {
            $res[$v["jt_num"]]+=$v['jingzhong'];
        }
        return $res;
    }
    /**
     * 数据处理成柱状图需要格式
     * key
     * [1]=>100.22
     */
    public function lindDataA($data){
        $res=array();
        foreach ($data as $k => $v) {
            $res[$v["cp_class"]]+=$v['jingzhong'];
        }
        return $res;
    }

    /**
     *  根据传来的完工单号数组锁定数据
     */
    public function lock_data($state,$wg_nums){
        if(!empty($wg_nums)){
            if($state=="lock")
            {
                $res=$this->where("wg_num in({$wg_nums})")->save(array("is_lock"=>"1"));
            }
            if($state=="unlock")
            {
                $res=$this->where("wg_num in({$wg_nums})")->save(array("is_lock"=>"0"));
            }
            
        }
        return $res;
    }







    /****************************功能******************************/
    /**
     * 封装layui返回的表格数据
     */
    public function layui_data($data,$count,$code=0,$msg=""){
        $data_str='{"code":'.$code.',"msg":"'.$msg.'","count":'.$count;
        foreach ($data as $k=>$v)
        {
            $json .= json_encode($v).',';
        }
        $json = substr($json, 0, -1);
        $data_str.=',"data":['.$json.']}';
        return $data_str;
    }





    
}






