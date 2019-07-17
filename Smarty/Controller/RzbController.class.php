<?php
//命名空间
namespace Smarty\Controller;
use Think\Controller;
use Tools\BackController;
use Tools\BaseController;
use Smarty\Model\WeekplanModel;
use Smarty\Model\MonthplanModel;
use Smarty\Model\WeekhbModel;
use Smarty\Model\MonthhbModel;
use Smarty\Model\ZxhhuizongModel;
use Smarty\Model\PublicModel;
use Smarty\Model\TalkModel;
use Smarty\Model\DecisionModel;

/**
 * 质询会主要控制器，权限只判断到控制器
 */
class RzbController extends BaseController {
   /*
    * 质询会主界面
    */
    public function zxhframe(){
        $this->display();
    }
    public function zxh_top(){
        C(SHOW_PAGE_TRACE,false);
        $this->display();
    }
    
    /*
     * 周计划时间选择
     */
    public function weekplan_time(){
        $this->assign("title","时间选择");
        $this->display();
    }

    /*
     * 周计划表验证是否填写
     */
    public function check_weekplan(){
        $model_weekplan=new WeekplanModel();
        //传get数据调用模型的验证周表单是否存在方法
        $z=$model_weekplan->check_weekplan($_GET);
        if($z){
            echo "1";     
        }else{
            echo '不存在';
        }
    }
        
    
    /*
     * 质询会周计划
     */
    public function weekplan(){
        if($_GET){
            //有GET数据来的时候展示表单
            //事件和日期
            $model_weekplan=new WeekplanModel();
            $day=date('j');
            $month=date('n');     //几月
            $theme="质询会周计划表---".$_GET['month']."月".$_GET['week'];
            $xuhao=$model_weekplan->change_num($_GET);//生成序号20160502 末尾数代表第几周        
            $writedate=date('Y').'-'.date('m').'-'.date('d');//填写时间
            //传日期
            $this->assign('month',$month);
            $this->assign('theme',$theme);
            $this->assign('xuhao',$xuhao);
            $this->assign('writedate',$writedate);
            $this->display();
        }
        else
        {//没有则是提交插入数据
            $model_weekplan=new WeekplanModel();
           I('post.');
            $data=$model_weekplan->create();
            //把序列化的数据加入data中
            $data['alldata']=serialize(I('post.'));
            $z=$model_weekplan->insert($data);
            if($z){
                $this->redirect('weekplanlist');
            }else {
                echo "插入失败！";
            }
        }
    }
    
    
    /*
     * 修改周计划表
     * 如果有get传来的id则关联表单显示，否则则是update数据
     */
    public function weekplan_modify($id=""){
        //展示
        if(!empty($id)){
            $model_weekplan=new WeekplanModel();
            $data=$model_weekplan->find($id);
            $alldata=unserialize($data['alldata']);
            $alldata['writetime']=date('Y').'-'.date('m').'-'.date('d');//填写时间
            $alldata['id']=$data['id'];
            $alldata['line']=$data['line']; //总行数
            $this->assign('data',$alldata); //总数据
            $this->assign('id',$data['id']); //周计划id
            $this->assign('line',$data['line']); //总行数
            $this->display();
        }else{//根据id更新计划表
            $model_weekplan=new WeekplanModel();
            $table_log=D('rzb_zxh_weekplan_log');
            //先存之前表的ID
            $pid=$_POST['pid'];
            //取得该跳转哪里
            $redirect=$_POST['redirect'];
            $userinfo=session('userinfo');
            //过滤
            $data=$model_weekplan->create();
            I('post.');
            //把序列化的数据加入data中
            $data['alldata']=serialize(I('post.'));
            $data['pid']=$pid;
            //调用模型插入
            $z=$model_weekplan->insert($data);
            if($z){
                //判断跳转页面到传来的该跳转的页面 其他则是自己的
                if(!empty($redirect)){
                    //1、把主题的名字修改一下
                    $theme=$data['theme'];
                    $str1=mb_substr($theme,3,4,'utf-8');  //截取 周计划表
                    $str2=mb_substr($theme,10,5,'utf-8'); //截取 8月第一周
                    $new_theme=$str1.$str2; 
                    //2、被管理员修改后插入修改日志
                    $log['operate']="修改";
                    $log['op_name']=$userinfo['name'];      //修改的人
                    $log['user_name']=$data['name'];      //被修改的行的人
					$log['samaccountname']=$data['samaccountname'];  //被修改的人的工号
                    $log['op_id']=$data['pid'];      
                    $log['op_theme']=$new_theme;      
                    $log['op_number']=$data['number'];      
                    $log['preview']="preview";          //预览界面的控制器名
                    $log['op_time']=time();                
                    $log['state']="no";                
                    $table_log->add($log);
                    $this->redirect($redirect);
                }
                else{
                    $this->redirect('weekplanlist');
                }
            }else {
                echo "插入失败！";
            }
        }
    }
    
    /*
     * 周计划删除
     */
    public function weekplan_del($id){
         $model_weekplan=new WeekplanModel();
         $model_weekhb=new WeekhbModel();
         //1、先看有没有填写汇报表有则删除
         $data=$model_weekplan->where("id=$id")->find();
         $id_hb=$data['hb'];
         if(!empty($id_hb)){
             $model_weekhb->where("id='$id_hb'")->delete();
         }
         //2、然后判断有没有上传文件,有则先删除文件
         $info=$model_weekplan->where("id=$id")->find();
         if(!empty($info['upload'])){
            $this->del_upload($info['upload']);
         }
         //3、没有则直接删除计划表
         $z=$model_weekplan->where("id=$id")->delete();
         if($z){
             echo "true";
         }else{
             echo "false";
         }
    }
    
    /*
     * 质询会用户自己周计划列表
     */
    public function weekplanlist($year="",$month=""){
        //如果没传年月来就用当前月
        if($year=="" || $month==""){
            $year=date('Y');
            $month=date('n');     //几月
        } 
        $date['year']=$year;//当前年月
        $date['month']=$month;
        $model_weekplan=new WeekplanModel();
        $userinfo=session('userinfo');
        $dept_ch=$userinfo['department'];   //部门名
		$samaccountname=$userinfo['samaccountname']; //工号
        //根据年月生成序号
        $xuhao=$model_weekplan->change_num($date);
        //根据序号和工号确认搜索
        $data=$model_weekplan->where("number like '{$xuhao}%' and samaccountname='$samaccountname'")->order('number desc')->select();
        //查禁止周计划表修改时间
        $week_forbid_timestamp=$this->select_forbid_timestamp(1);
        //传禁止修改的时间戳
        $this->assign(array(
            'title'=>'我的表单',
            'forbid_time'=>$week_forbid_timestamp,
            'data'=>$data,
            'date'=>$date,//传年月
        ));
        $this->display();
    }
       
    
    /*
     * 预览自己的表
     * $id  get传来的id
     */
    public function preview($id) {
        $model_weekplan=new WeekplanModel();
        $model_weekhb=new WeekhbModel();  
        $data=$model_weekplan->find($id);   //计划表数据
        $datahb=$model_weekhb->where("pid='$id'")->find(); //汇报表数据
        //反序列化之前的POST数据
        $alldata=unserialize($data['alldata']);
        $alldatahb=unserialize($datahb['alldata']);
        $this->assign('data',$alldata);
        $this->assign('datahb',$alldatahb);
        //把行数也加进去
        $this->assign('line',$data['line']);
        $this->assign('linehb',$datahb['line']);
        $this->assign('id',$id);
        $this->display();
    }
    
        
    /*
     * 显示质询会周汇报表单
     */
    public function weekhb($id){
        $model_weekplan=new WeekplanModel();
        $data=$model_weekplan->find($id);    
        //直接传反序列化好的
        $alldata=unserialize($data['alldata']);  
        //替换主题
        $alldata['theme']=str_replace('计划','汇报',$alldata['theme']);
        $alldata['line']=$data['line'];
        $alldata['id']=$data['id'];
        $alldata['writetime']=date('Y').'-'.date('m').'-'.date('d');//填写时间
        $this->assign('data',$alldata); //总数据
        $this->assign('id',$data['id']); //周计划id
        $this->assign('line',$data['line']); //总行数
        $this->display();
    }    
    
    /*
     * 质询会周汇报表附件ajax上传
     */
    public function weekhb_upload(){
        C(SHOW_PAGE_TRACE,false);
        //处理上传文件
        if($_FILES['upload']['error']==0){
            $arr=array(
                'rootPath'      =>  'upload/',
            );
            $upload=new \Think\Upload($arr);
            //返回文件的信息
            $file=$upload->uploadOne($_FILES['upload']);
            $filename=$upload->rootPath.$file['savepath'].$file['savename'];
            //返回路径
            echo $filename; 
        }
    }
    
    
    
    /*
     * 质询会周汇报表提交
     */
    public function weekhbadd(){  
        $model_weekhb=new WeekhbModel();
        $model_weekplan=new WeekplanModel();
        $data=$model_weekhb->create();
        I('post.');
        $data['alldata']=serialize(I('post.'));
        //2、处理上传文件 
        if($_POST['upload']!=""){
            //路径信息根据父id放入计划表的upload字段中
            $pid=$data['pid'];
            $tmp_array['upload']=$_POST['upload'];
            $model_weekplan->where("id=$pid")->save($tmp_array);
        }
       //调用模型插入 $z是返回的汇报表的id
       $z=$model_weekhb->insert($data);
       if($z){
            //取出父id并且update 更新汇报表的id到weekplan的hb字段
            $pid=$data['pid'];
            $save['hb']=$z;
            $model_weekplan->where("id='$pid'")->save($save);
            $this->redirect('weekplanlist');
        }else {
            echo "插入失败！";
        }
    }
    

    
    
    /*月*********************************************************/
    
    /*
     * 月计划时间选择
     */
    public function monthplan_time(){
        $this->display();
    }
    
    /*
     * 周计划表验证是否填写
     */
    public function check_monthplan(){
        $model_monthplan=new MonthplanModel();
        $z=$model_monthplan->check_monthplan($_GET);
        if($z){
            echo "1";
        }else{
            echo '不存在';
        }

    }

    /*
     * 质询会月计划填写
     */
    public function monthplan(){
          if($_GET){
            //有GET数据来的时候展示表单
            $get_year=$_GET['year'];
            $get_month=$_GET['month'];
            $time_str=$get_year."-".$get_month."-1";
            $time_stamp=strtotime($time_str);
            $days=date('t',$time_stamp);      //这月多少天
            $month=date('n',$time_stamp);     //几月
            $theme="质询会月计划表---".$_GET['month']."月";
            $model_weekplan=new WeekplanModel();
            $xuhao=$model_weekplan->change_num($_GET);
            $writedate=date('Y').'-'.date('m').'-'.date('d');//填写时间
            //传日期
            $this->assign('year',$get_year);
            $this->assign('month',$month);
            $this->assign('days',$days);
            $this->assign('xuhao',$xuhao);
            $this->assign('theme',$theme);
            $this->assign('writedate',$writedate);
            $this->display();
        }
        else
        {
            $model_monthplan=new MonthplanModel();
            //过滤
            $data=$model_monthplan->create();
            I('post.');
            $data['alldata']=serialize(I('post.'));
            $z=$model_monthplan->insert($data);
            if($z){
                $this->redirect('monthplanlist');
            }else {
                echo "插入失败！";
            }
        }
    }
    
    /*
     * 质询会用户自己月计划列表
     */
    public function monthplanlist($year=""){
        //如果没传年来就用当前年
        if($year=="" ){
            $year=date('Y');
        }
        $model_monthplan=new MonthplanModel();
        $userinfo=session('userinfo');
        $dept_ch=$userinfo['department'];   //部门名
        $samaccountname=$userinfo['samaccountname']; //工号
        $data=$model_monthplan->where("number like '{$year}%' and samaccountname='$samaccountname'")->order('number desc')->select();
        //查禁止月计划表修改时间传id 2
        $week_forbid_timestamp=$this->select_forbid_timestamp(2);
        $this->assign('forbid_time',$week_forbid_timestamp);
        $this->assign('data',$data);
        $this->assign('year',$year);//传年月
        $this->display();
    }
  
    /*
     * 查看自己的月表
     * $id  get传来的id
     */
    public function monthpreview($id) {
        $model_monthplan=new MonthplanModel();
        $model_monthhb=new MonthhbModel();
        $data=$model_monthplan->find($id);   //总结表数据
        $datahb=$model_monthhb->where("pid='$id'")->find(); //汇报表数据
        //反序列化之前的POST数据
        $alldata=unserialize($data['alldata']);
        $alldatahb=unserialize($datahb['alldata']);
        //直接传反序列化好的
        $this->assign('data',$alldata);
        $this->assign('datahb',$alldatahb);
        //把行数也加进去
        $this->assign('line',$data['line']);
        $this->assign('linehb',$datahb['line']);
        $this->assign('id',$id);
        $this->display();
    }
    
    /*
     * 显示质询会月汇报表单
     */
    public function monthhb($id){
        $model_monthplan=new MonthplanModel();
        $data=$model_monthplan->find($id);
        $alldata=unserialize($data['alldata']);
        //替换主题
        $alldata['theme']=str_replace('计划','汇报',$alldata['theme']);
        $alldata['line']=$data['line'];
        $alldata['id']=$data['id'];
        $alldata['writetime']=date('Y').'-'.date('m').'-'.date('d');//填写时间
        
        $this->assign('data',$alldata); //总数据
        $this->assign('id',$data['id']); //周计划id
        $this->assign('line',$data['line']); //总行数
        $this->display();
    }
    
    
    
    /*
     * 月汇报表附件ajax上传
     */
    public function monthhb_upload(){
        C(SHOW_PAGE_TRACE,false);
        if($_FILES['upload']['error']==0){
            $arr=array(
                'rootPath'      =>  'upload/',
            );
            $upload=new \Think\Upload($arr);
            $file=$upload->uploadOne($_FILES['upload']);
            $filename=$upload->rootPath.$file['savepath'].$file['savename'];
            //返回路径
            echo $filename;
        }
    }

    
    
    
    
    
    /*
     * 质询会月汇报表提交
     */
    public function monthhbadd(){
        $model_monthhb=new MonthhbModel();
        $model_monthplan=new MonthplanModel();
        $data=$model_monthhb->create();
        I('post.');
        //2、处理上传文件
        if($_POST['upload']!=""){
            //路径信息根据父id放入计划表的upload字段中
            $pid=$data['pid'];
            $tmp_array['upload']=$_POST['upload'];
            $model_monthplan->where("id=$pid")->save($tmp_array);
        }
        
        //把序列化的数据加入data中
        $data['alldata']=serialize(I('post.'));
        //4、调用模型插入
        $z=$model_monthhb->insert($data);
        if($z){
            //5、取出父id并且update 这条的hb字段返回的id
            $pid=$data['pid'];
            $save['hb']=$z;
            $model_monthplan->where("id='$pid'")->save($save);
            $this->redirect('monthplanlist');
        }else {
            echo "插入失败！";
        }
    }

    /*
     * 修改月计划表
     * 如果有get传来的id则关联表单显示，否则则是update数据
     */
    public function monthplan_modify($id=""){
        //展示
        if(!empty($id)){
            $model_monthplan=new MonthplanModel();
            $data=$model_monthplan->find($id);
            $alldata=unserialize($data['alldata']);
            $alldata['writetime']=date('Y').'-'.date('m').'-'.date('d');
            $alldata['id']=$data['id'];
            $alldata['line']=$data['line']; //总行数
            $this->assign('data',$alldata); //总数据
            $this->assign('id',$data['id']); //月计划id
            $this->assign('line',$data['line']); //总行数
            $this->display();
        }else{//根据id更新计划表
            $model_monthplan=new MonthplanModel();
            //先存之前表的ID
            $pid=$_POST['pid'];
            //取得该跳转哪里
            $redirect=$_POST['redirect'];
            $data=$model_monthplan->create();
            $data['alldata']=serialize($_POST);
            $data['pid']=$pid;
            //调用模型插入
            $z=$model_monthplan->insert($data);
            if($z){
                //判断跳转页面1代表是管理员进来 没有代表是本人修改
                if($redirect=="1"){
                    $this->redirect('showhzlist');
                }else{
                    $this->redirect('monthplanlist');
                }
            }else {
                echo "插入失败！";
            }
        }
    }
    
    /*
     * 月计划删除
     */
    public function monthplan_del($id){
        $model_monthplan=new MonthplanModel();
        $model_monthhb=new MonthhbModel();
        //1、先看有没有填写汇报表有则删除
        $data=$model_monthplan->where("id=$id")->find();
        $id_hb=$data['hb'];
        if(!empty($id_hb)){
            $model_monthhb->where("id='$id_hb'")->delete();
        }
        //2、然后判断有没有上传文件,有则先删除文件
        $info=$model_monthplan->where("id=$id")->find();
        if(!empty($info['upload'])){
            $this->del_upload($info['upload']);
        }
        //3、没有则直接删除计划表
        $z=$model_monthplan->where("id=$id")->delete();
        if($z){
            echo "true";
        }else{
            echo "false";
        }
    }
    
    
    
    /*汇总*********************************************************/
   
     
    /*
     * 汇总主界面
     */
    public function zxhhzframe(){
        $this->display();
    }
    public function zxhhz_top(){
        C(SHOW_PAGE_TRACE,false);
        $this->display();
    }
    
    /*
     * 查看汇总列表
     */
    public function showhzlist($year="",$month=""){     
        //1、如果没传年月来就用当前月
        if($year=="" || $month==""){
            $year=date('Y');
            $month=date('n');     //几月
        }
        //把数据中加入当前选的年和月
        $date['year']=$year;
        $date['month']=$month;
       //2、交给模型处理所有周汇报列表
       $model_weeklpan=new WeekplanModel();
       $data=$model_weeklpan->showhzlist($year,$month);
       //3、在做月汇总报汇总表数据
       $model_monthplan=new MonthplanModel();
       //根据年月生成月汇总的序号
       $xuhao=$model_weeklpan->change_num($date);
       $month_data=$model_monthplan->where("number='{$xuhao}'")->select();
       //4、查禁止周和月计划表修改时间
       $week_forbid_timestamp=$this->select_forbid_timestamp(1);
       $month_forbid_timestamp=$this->select_forbid_timestamp(2);
       $this->assign('forbid_time',$week_forbid_timestamp);
       $this->assign('month_forbid_time',$month_forbid_timestamp) ;
       $this->assign('month_data',$month_data);
       $this->assign('data',$data);
       $this->assign('date',$date); //传年月
       $this->display();
    }
    
    /*
     * 数据汇总表单显示
     */
    public function showhztable($year="",$month=""){        
        //如果没传年月来就用当前月
        if($year=="" || $month==""){
            $year=date('Y');
            $month=date('n');     //几月
        }
        //交给模型处理数据
        $model_weekhb=new WeekhbModel();
        $model_monthhb=new MonthhbModel();
        $data=$model_weekhb->showhztable($year,$month);
        $month_data=$model_monthhb->showhztable_month($year, $month);

        //把数据中加入当前选的年和月
        $data['year']=$year;
        $data['month']=$month;
        $this->assign("month_data",$month_data);
        $this->assign('data',$data);
        $this->display();
        
    }
    /*
     * 数据汇总表单保存
     */
    public function addhz(){
        $model_zxhhz=new ZxhhuizongModel();
        $id=$model_zxhhz->insert($_POST);
        //插入成功后返回id
        if($id){
            $url=__CONTROLLER__;
            $js=<<<JS
		<script>
			alert("保存成功");
           window.location.href = "$url/showhztable";
		</script>
JS;
            echo $js;
        }else{
            echo "保存数据失败！请重试";
        }
    }
    /*
     * 删除已保存的汇总表单数据
     */
    public function del_hz(){
          $model_zxhhz=new ZxhhuizongModel();
          $year=$_GET['year'];
          $month=$_GET['month'];
          $z=$model_zxhhz->where("year='$year' and month='$month'")->delete();
          if($z){
               echo "true";
          }else{
               echo "false";
          }
    }
    
/* 弹出框************************************************* */    
    /*
     * 插入周禁止修改的时间戳
     */
    public function  forbid_timestamp(){
        $model_weekplan=new WeekplanModel();
        $table_public=D('rzb_zxh_public');
        //获得禁止修改的时间戳
        $forbid_timestamp=strtotime($_POST['forbid_timestap']);
        //现在的时间戳
        $_POST['modify_timestamp']=time();
        $_POST['forbid_timestamp']=$forbid_timestamp;
        //然后拼接年月周  
        //先把周转换成数字
        $data['year']=$_POST['year'];
        $data['month']=$_POST['month'];
        $data['week']=$_POST['week'];
        $xuhao=$model_weekplan->change_num($data);//生成序号20160502
        //存入post
        $_POST['forbid_week']=$xuhao;
        $z=$table_public->where("id=1")->save($_POST);
        if($z){
            echo "success";
        }else{
            echo "false";
        }   
    }
    /*
     *填写周计划的时候验证是否过了禁止时间 
     *$id 1代表周计划禁止时间 2代表月计划禁止时间
     */
    public function check_forbid(){
        $id=$_GET['id'];
        $model_weekplan=new WeekplanModel();
        //生成序号20160502
        $xuhao=$model_weekplan->change_num($_POST);
        //读取设定的禁止时间
        $week_forbid_timestamp=$this->select_forbid_timestamp($id);
        //如果禁止序号-输入序号 也就是禁止的那周之前的不能填写      
        if($week_forbid_timestamp['forbid_week']-$xuhao>=0){
            echo "false";
        }else{
            echo "success";
        }
        
    }
    
    /*
     * 插入月禁止修改的时间戳
     */
    public function  forbid_timestamp_month(){
       $model_weekplan=new WeekplanModel();
        $table_public=D('rzb_zxh_public');
        $_POST['modify_timestamp']=time();
        $data['year']=$_POST['year'];
        $data['month']=$_POST['month'];
        $xuhao=$model_weekplan->change_num($data);//生成序号201605
        //存入post
        $_POST['forbid_week']=$xuhao;
        $z=$table_public->where("id=2")->save($_POST);
        if($z){
            echo "success";
        }else{
            echo "false";
        }
    }
    
    
    /*
     * 显示当前锁定的日期
     */
    public function clock_info(){
        $model_public=new PublicModel();
        $clock_info=$model_public->clock_info();
        echo $clock_info;
    }
    
    
    
    
    
    
    /*交流议题****************************************************************************/
    public function zxh_talk_frame(){
        $this->display();
    }

    /*
     * 根据年月周查看交流议题
     */
    public function talklist($year="",$month="",$week=""){ 
        //1、如果没传年月来就用当前年月周
        if($year=="" || $month==""||$week==""){
            $date=$this->y_m_w();
        }else{
            $date['year']=$year;
            $date['month']=$month;
            $date['week']=$week;
        }
        $model_weekplan=new WeekplanModel();
        $xuhao=$model_weekplan->change_num($date);     
        //3、根据序号查询
        $model_talk=new TalkModel();
        $data=$model_talk->where("xuhao='$xuhao'")->select();
        $len=count($data);
        //4、判断是不是有管理员,是的话传1过去
        if($_GET['mark']=="1"){
            $is_admin=1;
            $this->assign('is_admin',$is_admin);
        }
        $this->assign('date',$date);
        $this->assign('len',$len);
        $this->assign('data',$data);
        $this->display();
    }
    
    
    /*
     * 选择交流议题的填写时间
     */
    public function talk_time(){
        $this->display();
    }
    
    /*
     *  交流议题附件ajax上传
     */
    public function talk_upload(){
        C(SHOW_PAGE_TRACE,false);
        //处理上传文件
        if($_FILES['upload']['error']==0){
            $arr=array(
                'rootPath'      =>  'upload/',
            );
            $upload=new \Think\Upload($arr);
            $file=$upload->uploadOne($_FILES['upload']);
            $filename=$upload->rootPath.$file['savepath'].$file['savename'];
            //返回路径
            echo $filename;
        }
    }
    
    
    /*
     *  1、有GET根据GET的展示填写表单
     *  2、有POST则插入 
     */
    public function talk(){
        //有GET展示填写表单，有POST提交
        if($_GET){
            //GET中只有年月周
            $model_weekplan=new WeekplanModel();
            $theme=$_GET['month']."月".$_GET['week']."交流议题";
            $xuhao=$model_weekplan->change_num($_GET);//生成序号20160502 末尾数代表第几周
            $writedate=date('Y').'-'.date('m').'-'.date('d');//填写时间
            $this->assign('theme',$theme);
            $this->assign('xuhao',$xuhao);
            $this->assign('writedate',$writedate);
            $this->assign('date',$_GET);//年月周传过去做跳转用
            $this->display();
        }else{
            $model_talk=new TalkModel();
            //填写人的名字也存进去
            $userinfo=session('userinfo');
            I('post.');
            $_POST['add_name']=$userinfo['name'];
            //调用模型插入
            $z=$model_talk->add(I('post.'));
            if($z){
                //跳转到填写的年月周的交流议题列表
                $this->redirect('talklist',array('year'=>$_POST['year'],'month'=>$_POST['month'],'week'=>$_POST['week']));
            }else {
                echo "插入失败！";
            }
        }
    }
    
    /*
     * 删除交流议题
     */
    public function talk_del(){
        $id=$_GET['id'];
        $model_talk=new TalkModel();
        //判断有没有上传文件,有则先删除文件
        $info=$model_talk->where("id=$id")->find();
        if(!empty($info['upload'])){
            $this->del_upload($info['upload']);
        }
        $z=$model_talk->where("id=$id")->delete();
        if($z){
            echo "true";
        }else{
            echo "false";
        }
    }
    /*决议事项*************************************************************************/
    
    
    /*
     * 展示决议事项表单
     */    
    public function decision($year="",$month="",$week="") {
    //1、如果没传年月来就用当前年月周
        if($year=="" || $month==""||$week==""){
            $date=$this->y_m_w();
        }else{
            $date['year']=$year;
            $date['month']=$month;
            $date['week']=$week;
        }
        $model_weekplan=new WeekplanModel();
        $xuhao=$model_weekplan->change_num($date);
        //3、根据序号查询
        $model_decision=new DecisionModel();
        $data=$model_decision->where("xuhao='$xuhao'")->select();
        $this->assign('xuhao',$xuhao);
        //传年月周做跳转
        $this->assign('date',$date);
        $this->assign('data',$data);
        $this->display();
    }
    
    /*
     * 插入决议事项
     */
    public function decision_add(){
        //填写人的名字和时间也存进去
        $userinfo=session('userinfo');
        $_POST['add_name']=$userinfo['name'];
        $_POST['add_time']=time();
        $model_decision=new DecisionModel();
        $z=$model_decision->add(I('post.'));
        if($z){
            //跳转到填写的年月周的交流议题列表
            $this->redirect('decision',array('year'=>$_POST['year'],'month'=>$_POST['month'],'week'=>$_POST['week']));
        }else {
            echo "插入失败！";
        }
    }

    
    
    /*
     * 删除决议事项
     */
    public function decision_del($id){
        $model_decision=new DecisionModel();
        $z=$model_decision->where("id=$id")->delete();
        if($z){
            echo "true";
        }else{
            echo "false";
        }
    }
    
    
    /*
     * 1、有父id展示编辑决议事项
     * 2、有post则update
     */
    public function decision_edit() {
        if(!empty($_GET['pid'])){
            $pid=$_GET['pid'];
            //根据父id查询
            $model_decision=new DecisionModel();
            $data=$model_decision->where("id='$pid'")->find();
            echo json_encode($data);
        }else {
            $pid=$_POST['pid'];
            $userinfo=session('userinfo');
            $_POST['add_name']=$userinfo['name'];
            $_POST['add_time']=time();
            $model_decision=new DecisionModel();
            $z=$model_decision->where("id='$pid'")->save(I('post.'));
            if($z){
                //跳转到填写的年月周的交流议题列表
                $this->redirect('decision',array('year'=>$_POST['year'],'month'=>$_POST['month'],'week'=>$_POST['week']));
            }else {
                echo "插入失败！";
            }
        }
    }
   /* ***************数据汇总****************/
   /*
    * 计划表汇总查询
    */
    public function hz(){
        $model_weekplan=new WeekplanModel();
        $data=$model_weekplan->hz($_GET);
        //2、判断有没有部门 业务模块
        if($_GET['ywmodel']!="0"){
            $this->assign('ywmodel',$_GET['ywmodel']);
        }
        if($_GET['dept']!="0"){
            $this->assign('dept',$_GET['dept']);
        }
        if($_GET['zd']!="0"){
            $this->assign('zd',$_GET['zd']);
        }
        $this->assign('data',$data);
        $this->display();
    }
    
    
    /*
     * 未完成汇总查询
     */
    public function hz_wwc(){
        $model_weekhb=new WeekhbModel();  
        $data=$model_weekhb->hz_wwc($_GET);
        //2、判断有没有部门 业务模块
        if($_GET['ywmodel']!="0"){
            $this->assign('ywmodel',$_GET['ywmodel']);
        }
        if($_GET['dept']!="0"){
            $this->assign('dept',$_GET['dept']);
        }
        if($_GET['zd']!="0"){
            $this->assign('zd',$_GET['zd']);
        }
        $this->assign('data',$data);
        $this->display();
    }
    /*其他*********************************************************/
    /*
     * ajax提交生成年月
     */
    public function date(){
        $arr_month=array('0月','1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月');
        $arr_year=array(
            '2016'=>'2016',
            '2017'=>'2017',
            '2018'=>'2018',
            '2019'=>'2019',
            '2020'=>'2020',
            '2021'=>'2021',
            '2022'=>'2022',
            '2023'=>'2023',
            '2024'=>'2024',
            '2025'=>'2025',
        );
        $now_year=date('Y');
        $now_month=date('n');
        //第几周
        $day=date('j');
        $arr_num=ceil($day/7)-1;
        $arr_week=array("第一周","第二周","第三周","第四周","第五周");
        $week=$arr_week[$arr_num];    //本月第几周大写
        $data['arr_month']=$arr_month;
        $data['arr_year']=$arr_year;
        $data['arr_week']=$arr_week;
        $data['now_year']=$now_year;
        $data['now_month']=$now_month;
        $data['now_week']=$week;

        echo json_encode($data);
    }
    
    /*
     * 查周计划表禁止时间
     */
    public function select_forbid_timestamp($id){
        $model_public=new PublicModel();
        $data=$model_public->find($id);
        return $data;
    }
     

    /*
     * 返回当前年月周
     */
    public function y_m_w(){
        $year=date('Y');
        $month=date('n');     //几月
        //第几周
        $day=date('j');
        $arr_num=ceil($day/7)-1;
        $arr_week=array("第一周","第二周","第三周","第四周","第五周");
        $week=$arr_week[$arr_num];    //本月第几周大写
        $date['year']=$year;
        $date['month']=$month;
        $date['week']=$week;
        return $date;
    }
    /*
     * 根据路径删除文件
     */
    public function del_upload($file){
        if (!empty($_POST['flie']))
        {
            $file=$_POST['file'];
        }
        unlink($file);
    }
    
}
