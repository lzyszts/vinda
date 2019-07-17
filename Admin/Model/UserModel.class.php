<?php
namespace Admin\Model;
use Think\Model;
use Tools\Page;
/*
 *  用户表
 */
class UserModel extends Model {
    
     protected $_validate = array(     
        array('username','require','工号不能为空'), 
        array('password','require','密码不能为空'), 
        array('name','require','姓名不能为空'), 
        array('user_id','number','用户id错误'), 
        array('data_id','number','数据id异常'), 
        array('role_ids','check_ids','角色id错误',0,'callback'),  
        array('user_auth_ids','check_ids','后台权限id错误',0,'callback'), 
    );
   
   /**
    * 验证1、角色ids是否都为数字
    */
    public function check_ids($arr){
        foreach ($arr as $v){
           if(!is_numeric($v))
           {
               return false;
           }
        }
        return true;
    }


    /*
     * 1、域控读用户信息
     */
    public function getUserInfo($username,$password){
        $user=$username."@vinda.com";
        /*LDAP读用户信息*/
        $host=LDAP_IP;
        $ad = ldap_connect($host);
        $set = ldap_set_option($ad,LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option ( $ad, LDAP_OPT_REFERRALS,0);
        $basedn = "DC=vinda,DC=com"; #基本DN
        $filter="(sAMAccountName=".$username.")";  //查询条件
        $attributes=array('name','title','telephoneNumber','physicalDeliveryOfficeName','distinguishedName','company','department','sAMAccountName','mail','mobile');
        $bd=ldap_bind($ad,$user,$password);
        $search=ldap_search($ad,$basedn,$filter,$attributes);
        $ad_info=ldap_get_entries($ad,$search);
        /*处理获得的数据*/
        $ad_info=$ad_info[0];  
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
            'mobile'=>$ad_info[mobile][0]
        );
        return $info;
    }

    /*
     * 2、本地工号密码认证
     */
    public function checkInfo($username,$password) {
       $userinfo=$this->where("samaccountname='{$username}'")->find();
       $password=$this->encrypt($_POST['password'], 'E');
       if($userinfo['password']==$password){
           if ($userinfo['state']!=1) {
               $this->error="此用户已禁用，请联系管理员！";
               return false;
           }else{
                return $userinfo;
           }
       }else {
           $this->error="帐号或密码错误";
           return false;
       }
    }


    /*
     * 3、根据工号查询用户名
     */
    function searchUser(){
        $userinfo=session('userinfo');
        $username=$userinfo['samaccountname'];
        $user=$userinfo['samaccountname']."@vinda.com";
        $pwd=$userinfo['password'];
        /*LDAP读用户信息*/
        $host=LDAP_IP;
        $ad = ldap_connect($host);
        $set = ldap_set_option($ad,LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option ( $ad, LDAP_OPT_REFERRALS,0);
        $basedn = "DC=vinda,DC=com"; #基本DN
        //拼凑查询条件工号
        $samaccountname=trim($_POST["samaccountname"]);
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



    /*
     * 4、根据域读出的信息,本地user表,没有则插入,有则updata
     */
    public function getLocalUser($userinfo){
        $samaccountname=$userinfo['samaccountname'];   //工号
        $local_user=$this->where("samaccountname='$samaccountname'")->find();
        //最后处理名字后面有括号的    
        if(!$local_user)  //如果本地没有用户数据则插入，并返回本地用户信息  
        {
            $id=$this->add($userinfo);
            $local_userinfo=$this->where("id='$id'")->find();
            return $local_userinfo;
        }else{  //有则updata，并返回本地用户信息  
            $z=$this->where("id='{$local_user[id]}'")->save($userinfo);
            $local_userinfo=$this->where("id='{$local_user[id]}'")->find();
            return $local_userinfo;
        }  
    }




















    /**
    *  前台
    *  检查用户是否已经登录
    *  return bool
    */
     
    public function check_h_state(){
        $userinfo=session('uinfo');
        $user_id=$userinfo['id'];
        if(!empty($user_id)){
            return true;
        }else{
            return false;
        }
    } 
    
    /**
     * 显示用户列表并分页
     */
     public function showlist($pagesize){  
         //*分页
         $count=$this->count();//查询数据总数
         $count=$count-1;//减去admin
         $page=new Page($count,$pagesize);
         $page->pageArr=array("10","30","50","100"); //新增显示每页数量
         $limit=substr($page->limit,6);
         //每页数据,联表取部门中文名
         $data=$this
         ->where("samaccountname!='admin'")
         ->limit($limit)
         ->order("id")
         ->select();
         //2、用role_id求对应的中文名
         $data=$this->ids_to_ch($data);
         // 获得页码列表这里代表只连接345678的html不要总页数哪些信息
         $pagelist = $page->fpage(array(3,4,5,6,7));
         $pagearr= $page->flist();      //生成每页数据select框
         return array(
             'data'=>$data,
             'pagelist'=>$pagelist,
             'pagearr'=>$pagearr,
             'count' =>$count   
         );
     }
     /**
      * 显示查询数据并分页
      */
     public function search($pagesize){
         $v=$_GET['search'];
         $count=$this->where("samaccountname!='admin' and (samaccountname like '%{$v}%' or name like '%{$v}%' or department like '%{$v}%')")->count();  
         $page=new Page($count,$pagesize);
         $page->pageArr=array("10","30","50","100"); //新增显示每页数量
         //处理Limit 0,5为0,5
         $limit=substr($page->limit,6);
         $data=$this
         ->where("samaccountname!='admin' and (samaccountname like '%{$v}%' or name like '%{$v}%' or department like '%{$v}%')")
         ->limit($limit)
         ->select();
         //2、用role_id求对应的中文名
         $data=$this->ids_to_ch($data);
         // 获得页码列表这里代表只连接345678的html不要总页数哪些信息
         $pagelist = $page->fpage(array(3,4,5,6,7));
         $pagearr= $page->flist();      //生成每页数据select框
         return array(
             'data'=>$data,
             'pagelist'=>$pagelist,
             'pagearr'=>$pagearr,
             'count' =>$count   
         );
     }
     
    /**
     * 前台修改密码
    */
    public function change_pwd($userinfo){
        //验证老密码和新密码是否相同
        if($_POST['old']===$_POST['new'])
        {
            return "原密码和新密码不能相同";
        }
        $data['id']=$userinfo['id'];
        $data['password']=$this->encrypt($_POST['new'], 'E');
        $data['mobile']=$_POST['mobile'];
        $res=$this->save($data);
        return $res;  
    }


     /**
      * 添加新用户
      */
     public function user_add(){
        $username=$_POST['username'];
        $res=$this->where("samaccountname='{$username}'")->find();
        if($res)
        {
           $this->error="工号已存在!";
            return false;
        }
        //设置为默认用户
        $mr=D('role')->find(1);
        if ($mr)
        {
            $data['role_id']=$mr['role_id'];
            $data['auth_ids']="N";
        }else{
            $this->error="需要先设置默认角色，请在角色管理中添加 '默认角色'";
            return false;
        }
        $data['samaccountname']=$username;
        $data['password']=$this->encrypt($_POST['password'], 'E');
        $data['name']=$_POST['name'];
        $data['mobile']=$_POST['mobile'];
        $z=$this->add($data);
        return $z;
     }




/******************************修改******************************/     
     /**
      * 修改用户角色同时把权限设为N
      */
     public function role_edit(){
         if (isset($_POST['role_ids']))
         {
             $role_ids=$_POST['role_ids'];
             $role_ids=implode(',', $_POST['role_ids']);
             $data['role_id']=$role_ids;  
         }else{
             //没有提交任何角色则修改为默认角色
             $mr=D('role')->find(1);
             if ($mr)
             {
                 $data['role_id']=$mr['role_id'];
             }else{
                 $this->error="没有选择角色,需要先设置默认角色";
                 return false;
             }
         }
         $data['auth_ids']="N";
         $data['home_ids']="N";
         $data['id']=$_POST['user_id'];
         $z=$this->save($data);
         return $z;
     }
     /**
      * 单独分配用户权限同时把角色改为N
      */
     public function auth_edit(){
         $auth_ids=implode(',', $_POST['user_auth_ids']);  
         $data['id']=$_POST['id'];
         $data['auth_ids']=$auth_ids;
         $data['role_id']="N";
         $z=$this->save($data);
         return $z;
     }


     
     
/******************************工具******************************/      
     /** 工具
      *  根据传来的角色ids字符串合并所有角色权限
      *  return 合并后的权限ids
      *  @param $type  0：后台权限 1：前台权限
      */
     public function roleids_authids($role_ids,$type=0){
         //1、合并多个角色的权限
         $roleinfo=D('role')->select($role_ids);
         //遍历用户对应的所有角色信息然后合并权限ids
         foreach ($roleinfo as $v){
            $role_auth_ids.=$v['role_auth_ids'].",";
         }
         $role_auth_ids=explode(',',$role_auth_ids);
         //去掉为空的元素
         foreach ($role_auth_ids as $v) {
             if (empty($v)) {
                 continue;
             }
             $ids[] = $v;
         }
         //去掉重复的
         $role_auth_ids=array_unique($ids);
         //再转成权限ids数组
         $role_auth_ids=implode(',', $role_auth_ids);
         return $role_auth_ids;
     }
     
     
     
     
     
     /** 工具
      *  根据传来的权限ids(字符串)
      *  return 这些权限的m-c-a格式
      */
     public function auth_ids_mca($role_auth_ids){
        //没有ids则直接为空
        if($role_auth_ids)
        {
            $authinfo=D('auth')->select($role_auth_ids);
        }else{
            $authinfo="";
        }
        //根据权限ids数组制作role_m_a_c
        foreach ($authinfo as $v){
             //当不是1级菜单的时候
             if($v['auth_level']!=0){
                 $str.= $v['module']."-".$v['controller']."-".$v['action'].',';
             }
         }
         $str = rtrim($str,',');//去除最后逗号
         return $str;
     }
     
     /**工具
      * 根据权限ids求对应中文名
      */
     public function ids_to_ch($data){
         $role_data=D('role')->select();
         //1、把role表数据做成以id为下标的数组
         foreach ($role_data as $key=>$value){
             $role_data2[$value['role_id']]=$value;
         }
         //2、把用户的role_id分别转换成中文
         foreach ($data as $k=>$v){
             $role_id=$v['role_id'];
             $arr_ids=explode(',', $role_id); //用户权限ids分成数组
             foreach ($arr_ids as $v2){      //v2是用户每一个role_id
                 $arr_str[]=$role_data2[$v2]['role_name'];
             }
             $str=implode('，', $arr_str);
              
             $data[$k]['role_name']=$str;
             unset($arr_str);
         }
         return $data;
     }

     
     /** 工具
      * 记录用户登录信息
      */
     public function login_log($userinfo){
         $data['id']=$userinfo['id'];
         $data['last_login_ip']=$_SERVER ['REMOTE_ADDR'];
         $data['last_login_time']=date ('Y-m-d h:m:s');
         $this->save($data);
     }
      
     
     
     /** 工具
      * 函数encrypt($string,$operation,$key)
      * $string：需要加密解密的字符串；
      * $operation：判断是加密还是解密，E表示加密，D表示解密；
      * $key：密匙。
      */
     
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






