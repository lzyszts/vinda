<?php
namespace Smarty\Model;
use Think\Model;
/*
 * 公共数据表
 */
class PublicModel extends Model {
    protected $trueTableName = 'rzb_zxh_public';
	/*
	*	返回当前锁定的日期
	*/
	public function clock_info(){
		//周表锁定信息
	    $data=$this->find(1);
		//月表锁定信息
		$data_month=$this->find(2);
		$forbid_timestamp=$data['forbid_timestamp'];
		$forbid_week=$data['forbid_week'];
		
		$forbid_month=$data_month['forbid_week'];
		$forbid_month=substr($forbid_month,4,2);
		
		//转换为2016-06-24 13:55:00
		$forbid_time=date("Y-m-d H:i",$forbid_timestamp);
		//再计算是第几周
		$arr_week=array("第一周","第二周","第三周","第四周","第五周");
		//取出20160502  的最后一个数字  2
		$week=substr($forbid_week, -1);
		$year=substr($forbid_week,0,4);
		$month=substr($forbid_week,4,2);
		//转换为中文
		$forbid_week=$arr_week[$week-1];
		$str="禁止修改:<font color='red'>".$forbid_time."</font> &nbsp;锁定:<font color='red'>".$year."年".$month."月".$forbid_week."</font>计划表 &nbsp;开启:<font color='red'>".$forbid_week."</font>汇报表 |&nbsp;月报表:<font color='red'>".$forbid_month."月</font>";                                     
		return $str;
	}
    
}