<?php
namespace Smarty\Model;
use Think\Model;
/*
 * 质询会报表汇总
 */
class WeekhbModel extends Model {
    protected $trueTableName = 'rzb_zxh_weekhb';
    
    /*
     * 保存汇总table的数据
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
            $id=$this->add($data);  //没有则插入并且返回id
            if($id){
                return $id;
            }else{
                return false;
            }
        } 
    }
    
    /*
     * 生成各部门完成、未完成、完成率
     * 1、循环根据月周汇总表找指定月的所有数据
     * 2、反序列化处理数组形成部门名为下标元素为这个部门的内容的数组
     * 3、处理形成的数组并且循环这个数组计算每个部门已完成、未完成、汇总数据存入已部门名为下标的$res数组中
     * 4、再把得到的$res存入下标为'x月第x周汇总数据'的数组中然后返回
     */
    public function showhztable($year,$month){
        /*第一步、先根据各部门提交的统计*/
        $week=array('一','二','三','四','五');
        $datahz=array(); //数组中每个元素存一周的所有汇总数据
        $count=0;   //初始化一个索引数用作模板循环给class命名
        //先求序号
        $time['year']=$year;
        $time['month']=$month;
        $xuhao=$this->change_num($time);
        for($i=0;$i<count($week);$i++){  
            $data=$this->where("theme like '%{$month}月第{$week[$i]}周' and number like '{$xuhao}%'")->select();
           //初始化
            $result=array(); 
            $res=array();//存部门对应的汇总数据
            $total_jihua=0;
            $total_finished=0;
            $total_unfinished=0;
            $totle_continue=0;
            //反序列化处理数组形成部门名为下标的数组
            foreach ($data as $key=>$value){
                //先反序列化
                $result[$value['dept']]=unserialize($value['alldata']);
            }
           
            //再算出计划、完成、未完成、完成率存进$res
            foreach ($result as $k=>$v){
                //处理结果定义空的元素后求长度
                $jgdy=array_filter($v['result']);
                $jihua=count($jgdy);//计划共计数量
                //统计结果中完成和未完成的个数
                $jieguo=$v['jieguo'];
                $count_jieguo=array_count_values($jieguo);
                $finished=$count_jieguo['已完成']; //已完成数量
                if($finished==null){//全部未完成则为0
                    $finished=0;
                }
                $continue=$count_jieguo['持续中']; //持续中数量
                $unfinished=$jihua-$finished; //未完成数量
                $wanchenglv=round(($finished/$jihua)*100)."%"; //完成率
                //统计总共的
                $total_jihua+=$jihua;
                $total_finished+=$finished;
                $total_unfinished+=$unfinished;
                $totle_continue+=$continue;
                //加入结果数组中
                $res[$k]['jihua']=$jihua;
                $res[$k]['finished']=$finished;
                $res[$k]['unfinished']=$unfinished;
                $res[$k]['zxh_wanchenglv']=$wanchenglv;  
                $res[$k]['name']=$v['name'];   
                $res[$k]['dept']=$v['dept']; 
                //加入默认为0
                $res[$k]['zxh_benzhou']=0; 
                $res[$k]['zxh_wancheng']=0; 
                $res[$k]['zxh_weiwancheng']=0; 
                $res[$k]['zxh_chixugz']=0; 
                $res[$k]['zxh_chixuwc']=0; 
                $res[$k]['zxh_weiwancheng2']=0; 
            }   
//dump($res);die;
         
            //下标为第几周元素为这周数据汇总,没有则为空
            $str_week=$month."月第".$week[$i]."周数据汇总";
            $datahz[$str_week]=$res;
            //最后把总计数据存进去
            //当这一周数据不为空的时候再存入总计完成
            if($datahz[$str_week]!=array()){
               $datahz[$str_week]['data']['i']=$count;
                $count++;
                $datahz[$str_week]['data']['total_jihua']=$total_jihua;
                $datahz[$str_week]['data']['total_finished']=$total_finished;
                $datahz[$str_week]['data']['total_unfinished']=$total_unfinished;
                //质询会情况下面默认为0
                $datahz[$str_week]['data']['total_benzhou']=0;
                $datahz[$str_week]['data']['total_wancheng']=0;
                $datahz[$str_week]['data']['total_weiwancheng']=0;
                $datahz[$str_week]['data']['total_chixugz']=0;
                $datahz[$str_week]['data']['total_chixuwc']=0;
                $datahz[$str_week]['data']['total_weiwancheng2']=0;
                $total_wanchenglv=round(($total_finished/$total_jihua)*100)."%";
                $datahz[$str_week]['data']['total_wanchenglv']=$total_wanchenglv;
            }
        }  

//dump($datahz);die;
      
       //第二步、查询汇总表,如果存在则取出来对比
        $arr=array();
        $arr_total=array(); //存总计那行数据的数组
        $table_hz=D('rzb_zxh_hz');
        $result_hz=$table_hz->where("year=$year and month=$month")->find();
         //如果有、表名之前已经保存过、取出来
        if($result_hz){
            //反序列化
            $content=unserialize($result_hz['content']);
//dump($content);die;
            //1、先去掉总结哪一行的数据
            $str_total=array('total_jihua','total_finished','total_unfinished','total_benzhou','total_wancheng','total_weiwancheng','total_chixugz','total_chixuwc','total_weiwancheng2','total_wanchenglv');
     
            foreach ($content as $key=>$value){//key是6月第一周数据汇总 value是这周所有数据
                foreach ($value as $k=>$v){ //k是dept…v是对应的数组
                    if(in_array($k,$str_total)){
                        $arr_total[$key]['data'][$k]=$v[0]; //总结那行存入新数组
                        unset($content[$key][$k]);
                    }
                }
            }
//dump($arr_total);die;            
            //2、处理汇总表反序列化的数组
            foreach ($content as $key=>$value){ //key是6月第一周数据汇总 value是这周所有数据
               for($i=0;$i<count($value["dept"]);$i++){
                   foreach ($value as $k=>$v){ //k是dept v是dept对应的数组
                       $arr[$key][$value["dept"][$i]][$k]=$v[$i];
                   }
                }
            } 
            //合并总结的数据和每行数据
            $content_hz=array_merge_recursive($arr,$arr_total); 
//dump($content_hz);
//dump($datahz);die;
            //3、处理$content_hz(保存统计数组)和$datahz(用户信息自动生成数组)如果相同则用$content_hz覆盖
            foreach ($datahz as $key=>$value){//key是6月第一周数据汇总 value是这周所有数据 
                   foreach ($value as $k=>$v){//k是人力资源及行政部 v是对应的数组
                       if(!empty($content_hz[$key][$k])){ //如果保存的数据中也存在这一项
                           //先合并部门数据
                           $datahz[$key][$k]=array_merge($datahz[$key][$k],$content_hz[$key][$k]);
                       }
                   }             
            }  
        }
//dump($datahz);die;
        return $datahz;
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


     /*
     * 未完成汇总查询
     */
    public function hz_wwc($data){
        //先处理下数据好调用change_num以免返回000
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
        $res=$this->where("number like '{$number}'")->limit(50)->select();     
        //3、取出所有用户数据
        foreach ($res as $key=>$value){
            $user_data[$key]=unserialize($value['alldata']);
            $user_data[$key]['id']=$value['pid'];   //把数据对应计划表的id加入
        }
        return $user_data;
    }
   
	

}