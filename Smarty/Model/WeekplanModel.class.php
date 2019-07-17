<?php
namespace Smarty\Model;
use Think\Model;
/*
 * 质询会周报表
 */
class WeekplanModel extends Model {
    protected $trueTableName = 'rzb_zxh_weekplan';
    
    /*
     *周报表标题验证是否存在 
     */
    public function check_weekplan($data){
        $userinfo=session('userinfo');
        $dept_ch=$userinfo['department'];   //部门名
		$samaccountname=$userinfo['samaccountname'];  //工号
        $theme="质询会周计划表---".$data['month']."月".$data['week'];
        $year=$data['year'];
        //根据序号和主题查询有没有
        $z=$this->where("number like '{$year}%' and theme='$theme' and samaccountname='$samaccountname'")->find();
        return $z;
    }

    
    /*
     * 周报表表单的插入和修改
     */
    public function insert($data) {
        //处理数组分割成字符串
        $len=count($data['xuhao']);             //一共多少行
        $line=$len-1;                   //减去hide那行一共多少行;
        //逗号分割
        for ($i=1;$i<$len;$i++){
            if($i==$len-1){
                $str_xuhao.=$data['xuhao'][$i];
                $str_ywmodel.=$data['ywmodel'][$i];
                $str_ejmodel.=$data['ejmodel'][$i];
                $str_result.=$data['result'][$i];
                $str_statustime.=$data['statustime'][$i];
                $str_status.=$data['status'][$i];
                $str_sf.=$data['sf'][$i];
                $str_zd.=$data['zd'][$i];
                $str_remark.=$data['remark'][$i];
            }else{
                $str_xuhao.=$data['xuhao'][$i].",";
                $str_ywmodel.=$data['ywmodel'][$i].",";
                $str_ejmodel.=$data['ejmodel'][$i].",";
                $str_result.=$data['result'][$i].",";
                $str_statustime.=$data['statustime'][$i].",";
                $str_status.=$data['status'][$i].",";
                $str_sf.=$data['sf'][$i].",";
                $str_zd.=$data['zd'][$i].",";
                $str_remark.=$data['remark'][$i].",";
            }
        }
        //再存进data
        $data['xuhao']=$str_xuhao; //序号
        $data['ywmodel']=$str_ywmodel;//业务模块
        $data['ejmodel']=$str_ejmodel;//二级模块
        $data['result']=$str_result;//结果定义
        $data['statustime']=$str_statustime;//状态时间
        $data['status']=$str_status;       //状态(已完成,未完成)
        $data['sf']=$str_sf;               //是否自罚
        $data['zd']=$str_zd;               //是否重点
        $data['remark']=$str_remark;       //备注
        $data['line']=$line;
        $data['time_stamp']=time();
        
        //如果传来的数据有id则更新 没有则插入
        if(isset($data['pid'])){
            $pid=$data['pid'];
            $x=$this->where("id=$pid")->save($data);
            if($x){
                return true;
            }else{
                return false;
            }
        }else{
            if($this->add($data)){
                return true;
            }else {
                return false;
            }
        }

    }
    
    /*
     * 汇总列表数据处理
     */
    public function showhzlist($year,$month) {
        $time['year']=$year;
        $time['month']=$month;
       //根据年月生成序号
        $xuhao=$this->change_num($time);
        
       //先按照月份根据id倒序然后再按照部门分组
       $sql="select * from(select * from rzb_zxh_weekplan where theme like '%{$month}月%' and number like '{$xuhao}%' order by id desc) as a order by dept";
       $data=$this->query($sql);  

       //处理数组形成部门名为下标的二位数组
      // $result=array(
      //     '人力资源及行政部'=>array(),
      //     '采购部'=>array(),
      //     '仓储科'=>array(),
      //     '品管部'=>array(),
      //     '工程技术部'=>array(),
      //     '生产部'=>array(),
      //     '制造部'=>array(),
      // );
      foreach ($data as $key=>$value){
           //去掉平管部-体系办的  -体系办
           $dept=$value['dept'];
           /*
           if($pos=strpos($dept,'-'))
           {
              $dept=substr($dept, 0,$pos);
           }*/
           $result[$dept][]=$value;
       } 
      return $result;
    }
    
    
    /*
     * 根据传来的年月周生成表单的序号
     */
    public function change_num($data){
        $xuhao=$data['year']; //年
     
        if($data['month']!="%" && $data['month']<10){ //月前补0
            $xuhao.="0".$data['month'];
        }else{
            $xuhao.=$data['month'];  
        }
        if($data['week']!=""){ 
            $arr=array(
                '第一周'=>'01',
                '第二周'=>'02',
                '第三周'=>'03',
                '第四周'=>'04',
                '第五周'=>'05'
            );
            $xuhao.=$arr[$data['week']];
        }
        return $xuhao;
    }


	/*
     * 数据汇总查询
     */
    public function hz($data){
        //先处理下数据
        if($data['year']=="0"){
            $data['year']="%";
        }
        if($data['month']=="0"){
           $data['month']="%";
        }
        //换成序号
        $number=$this->change_num($data);
        //如果没有周则后面加上%
        if($data['week']=="0"){
            $number.="%";
        }
        
        //2、拼凑序号的sql语句 得到结果数组
        $res=$this->where("number like '{$number}'")->limit(50)->select();   //最多50个表单的信息              
       
        //3、取出所有用户数据
        foreach ($res as $key=>$value){
            $user_data[$key]=unserialize($value['alldata']);         
            $user_data[$key]['id']=$value['id'];   //把数据对应的id也加入      
        }  
     return $user_data;
    }



    
}