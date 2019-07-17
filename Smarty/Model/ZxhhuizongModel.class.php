<?php
namespace Smarty\Model;
use Think\Model;
/*
 * 质询会周报表
 */
class ZxhhuizongModel extends Model {
    protected $trueTableName = 'rzb_zxh_hz';
    /*
     * 处理汇总表单后插入
     */
    public function insert($data){
        //1、先取出年和月后再数组这两个元素
        $value['year']=$year=$data['year'];
        $value['month']=$month=$data['month'];
        unset($data['year']);
        unset($data['month']);
        $value['line']=count($data);//有多少个表
        //序列化post数据
        $value['content']=serialize($data);
        $value['time_stamp']=time();
        
        //2、判断是否存在如果存在则更新
        $z=$this->where("year=$year and month=$month")->find();
        $id=$z['id'];
        if($id){ 
            $x=$this->where("id=$id")->save($value); //有的话update
            if($x){
                return $x;
            }else{
                return false;
            }
        }else{
            $x=$this->add($value);  //没有则插入
            if($x){
                return $x;
            }else{
                return false;
            }
        }     
    }
    
    
    
}