<?php
namespace Smarty\Model;
use Think\Model;
/*
 * 质询会周报表
 */
class MonthhbModel extends Model {
    protected $trueTableName = 'rzb_zxh_monthhb';
    
    public function insert($data) {
        //处理数组分割成字符串
        $len=count($data['xuhao']);          
        $line=$len-1;                   //减去hide那行一共多少行
        //逗号分割
        for ($i=1;$i<$len;$i++){
            if($i==$len-1){
                $str_xuhao.=$data['xuhao'][$i];
                $str_ywmodel.=$data['ywmodel'][$i];
                $str_ejmodel.=$data['ejmodel'][$i];
                $str_result.=$data['result'][$i];
                $str_statustime.=$data['statustime'][$i];
                $str_status.=$data['status'][$i];
                $str_jieguo.=$data['jieguo'][$i];
                $str_yuanyin.=$data['yuanyin'][$i];
                $str_cuoshi.=$data['cuoshi'][$i];
                $str_zd.=$data['zd'][$i];
                $str_remark.=$data['remark'][$i];
            }else{
                $str_xuhao.=$data['xuhao'][$i].",";
                $str_ywmodel.=$data['ywmodel'][$i].",";
                $str_ejmodel.=$data['ejmodel'][$i].",";
                $str_result.=$data['result'][$i].",";
                $str_statustime.=$data['statustime'][$i].",";
                $str_status.=$data['status'][$i].",";
                $str_jieguo.=$data['jieguo'][$i].",";
                $str_yuanyin.=$data['yuanyin'][$i].",";
                $str_cuoshi.=$data['cuoshi'][$i].",";
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
        $data['jieguo']=$str_jieguo;       //结果
        $data['yuanyin']=$str_yuanyin;     //原因
        $data['cuoshi']=$str_cuoshi;     //原因
        $data['zd']=$str_zd;               //是否重点
        $data['remark']=$str_remark;       //备注
        $data['line']=$line;
        $data['time_stamp']=time();
        //判断之前有没有有则更新,没有则插入
        $pid=$data['pid'];
        $z=$this->where("pid=$pid")->find();
        if($z){
            $x=$this->where("pid=$pid")->save($data); //有的话update返回id
            if($x){
                return $z['id'];
            }else{
                return false;
            }
        }else{
            $x=$this->add($data);  //没有则插入
            if($x){
                return $x;
            }else{
                return false;
            }
        } 
    }
    
    
    
    /*
     * 月汇总数据
     */
    public function showhztable_month($year,$month){
        //先求序号
        $time['year']=$year;
        $time['month']=$month;
        $xuhao=$this->change_num($time);
        $result=$this->where("number='{$xuhao}'")->select();
//dump($result);
        $data=array();
        
        for($i=0;$i<count($result);$i++)
        {
            $data[$i]["dept"]=$result[$i]["dept"];
            $data[$i]["name"]=$result[$i]["name"];          
            //求计划共计
            $str_result=$result[$i]["result"];
            $arr_result=explode(",", $str_result);
            $data[$i]["jhgj"]=count(array_filter($arr_result)); //去掉空的
            //完成共计
            $str_jieguo=$result[$i]["jieguo"];
            $C=substr_count($str_jieguo,"已完成");
            $data[$i]["wcgj"]=$C;
            
            //未完成共计
            $data[$i]["wwcgj"]=$data[$i]["jhgj"]-$data[$i]["wcgj"];
            //完成率
            $T=$data[$i]["wcgj"]/$data[$i]["jhgj"]*100;
            $data[$i]["dcl"]=round($T,2)."%";
        }   
        return $data;
    }
    
    
    
    
    
   
    /*
     * 根据传来的年月周生成表单的序号
     */
    public function change_num($data){
        $xuhao=$data['year']; //年
        if($data['month']<10){ //月前补0
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
    
    
    
    
    
    
    
    
    
    
}