<?php
namespace Smarty\Model;
use Think\Model;
use Admin\Model\UserModel;
/*
 * 生产部车间转交记录分组模型
 */
class ScbUserModel extends Model {
   protected $trueTableName = 'scb_change_user';
   
   /* 
    * 1、查询用户是否有权限分配了,没有则插入并赋予权限,有则忽略
    * 2、插入用户分组
    */
   public function addUser()
   {     
        $userinfo=D("user")->where("samaccountname='{$_POST['samaccountname']}'")->find();
        //如不不存在则插入基础权限
        if(!$userinfo)
        {
            //查找转产记录普通成员权限id
            $role=D("role")->where("role_name='转产记录普通成员'")->find();
            //然后插入user表给予权限
            $data['samaccountname']=$_POST['samaccountname'];
            $data['name']=$_POST['name'];
            $data['password']="111111";
            $data['auth_ids']="N";
            $data['role_id']=$role['role_id'];
            D("user")->add($data);
        }
        $res=$this->add($_POST);
        return $res;
   }
   
   /*
    * 根据工号验证用户是否存在并且返回所在车间
    */
   public function userGroup(){
       $userinfo=session("userinfo");
       $samaccountname=$userinfo["samaccountname"];
       $res=$this->where("samaccountname='{$samaccountname}'")->find();
       if($res)
       {
           $group=D("scb_change_group")->field("group_name")->find($res["group_id"]);
           return $group["group_name"];
       }else{
           return false;
       }
   }
   
   

   /*
    * 验证用户是否存在
    * return turn false
    */
   public function checkUser(){
	   //如果输入为空则动态修改工号为0
       $gh=trim($_POST["gh"])?trim($_POST["gh"]):"0";
       $res=$this->where("samaccountname='{$gh}'")->find();
       if($res){
           $group=D("scb_change_group")->where("id={$res['group_id']}")->find();     
           return $group["group_name"];
       }else{
           return true;
       }
   }
   
   
   
   /*
    * 根据工号查询用户名
    */
   function searchUser(){
       $userinfo=session('userinfo');
       $username=$userinfo['samaccountname'];
       $user=$userinfo['samaccountname']."@vinda.com";
       $pwd=$this->encrypt($userinfo['password'], 'D');
       /*LDAP读用户信息*/
       $host=LDAP_IP;
       $ad = ldap_connect($host);
       $set = ldap_set_option($ad,LDAP_OPT_PROTOCOL_VERSION, 3);
       ldap_set_option ( $ad, LDAP_OPT_REFERRALS,0);
       $basedn = "DC=vinda,DC=com"; #基本DN
       //拼凑查询条件工号
       $samaccountname=trim($_POST["gh"]);
       $filter="(sAMAccountName=".$samaccountname.")";  //按照工号查
       $attributes=array('name','title','telephoneNumber','physicalDeliveryOfficeName','distinguishedName','company','department','sAMAccountName','mail','mobile','whenCreated','whenChanged');
       $bd=ldap_bind($ad,$user,$pwd);
       $search=ldap_search($ad,$basedn,$filter,$attributes);
       $ad_info=ldap_get_entries($ad,$search);
       /*处理获得的数据*/
       $ad_info=$ad_info[0];
       //转成时间戳
       $whencreated_str=$ad_info[whencreated][0];
       $whenchanged_str=$ad_info[whenchanged][0];
       $whencreated=strtotime(substr($whencreated_str,0,14));
       $whenchanged=strtotime(substr($whenchanged_str,0,14));     
       //处理名字(数字,括号) 
       $info_name=$ad_info['name'][0];
       //带括号的
       $kuohao=strpos($info_name,'(');
       if($kuohao>0){
           $info_name=substr($info_name, 0,$kuohao);
       }
       //数字
       $info_name=preg_replace('/\d/','',$info_name);
       //整理数据
       $info=array(
           'samaccountname'=>$ad_info['samaccountname'][0],
           'name'=>$info_name,
           'company'=>$ad_info[company][0],
           'department'=>$ad_info[department][0],
           'title'=>$ad_info[title][0],
           'telephonenumber'=>$ad_info[telephonenumber][0],
           'dn'=>$ad_info[distinguishedname][0],
           'mail'=>$ad_info[mail][0],
           'office'=>$ad_info[physicaldeliveryofficename][0],
           'mobile'=>$ad_info[mobile][0],
           'whencreated'=>$whencreated,
           'whenchanged'=>$whenchanged
       );
       return $info;
   }
   

  function encrypt($string,$operation,$key=''){
         $key=md5($key);
         $key_length=strlen($key);
         $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
         $string_length=strlen($string);
         $rndkey=$box=array();
         $result='';
         for($i=0;$i<=255;$i++){
             $rndkey[$i]=ord($key[$i%$key_length]);
             $box[$i]=$i;
         }
         for($j=$i=0;$i<256;$i++){
             $j=($j+$box[$i]+$rndkey[$i])%256;
             $tmp=$box[$i];
             $box[$i]=$box[$j];
             $box[$j]=$tmp;
         }
         for($a=$j=$i=0;$i<$string_length;$i++){
             $a=($a+1)%256;
             $j=($j+$box[$a])%256;
             $tmp=$box[$a];
             $box[$a]=$box[$j];
             $box[$j]=$tmp;
             $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
         }
         if($operation=='D'){
             if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){
                 return substr($result,8);
             }else{
                 return'';
             }
         }else{
             return str_replace('=','',base64_encode($result));
         }
  }
   
   
    
}