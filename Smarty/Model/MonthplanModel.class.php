<?php
namespace Smarty\Model;
use Think\Model;
/*
 * 质询会月计划报表
 */
class MonthplanModel extends Model {
    protected $trueTableName = 'rzb_zxh_monthplan';
    
    /*
     *月报表标题验证是否存在
     */
    public function check_monthplan($data){
        $userinfo=session('userinfo');
        $dept_ch=$userinfo['department'];   //部门名
		$samaccountname=$userinfo['samaccountname'];  //工号
        $theme="质询会月计划表---".$data['month']."月";
        $year=$data['year'];
        //根据序号和主题查询有没有
        $z=$this->where("number like '{$year}%' and theme='$theme' and samaccountname='$samaccountname'")->find();
        return $z;
    }
    
    /*
     * 插入和修改月计划表
     */
    public function insert($data) {
        
        //处理数组分割成字符串
        $len=count($data['xuhao']);             //一共多少行
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
}