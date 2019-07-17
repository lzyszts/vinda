<?php
namespace Admin\Controller;
use Think\Controller;
use Tools\BackController;
use Admin\Model\UserModel;
use Admin\Model\IndexModel;

class IndexController extends BackController {

    /* 登陆页面展示
     * 1、账号密码验证,admin账户本地验证
     * 2、通过了域控读取用户信息存session
     * 3、根据域控信息查看本地user表,没有则插入,有则updata
     */
    public function login() {
        if($_POST)
        {
            $_POST=I("post.");
            $model_user=D('user');

            //1、基础验证
            if($model_user->create($_POST))
            {
                $username=$_POST['username'];
                $password=$_POST['password'];
                $user=$_POST['username']."@vinda.com";
                //读取配置文件的本地验证工号
                $local_arr=C('LOCAL_ARR');
                //$local_arr=array('admin','1');//本地验证的工号
                //2、如果是admin或指定工号 读本地user表*/
                if(in_array($username,$local_arr)){
                    $userinfo=$model_user->checkInfo($username,$password);
                    if($userinfo)
                    {
                        //管理员过期时间6小时
                        session('userinfo',$userinfo);
                        //cookie('PHPSESSID',cookie("PHPSESSID"),6*3600);
                        //存ip和登陆时间
                        $model_user->login_log($userinfo);
                        //跳转地址
                        echo U("Admin/Index/index");
                    }else{
                        echo $model_user->getError();
                    }
                }
                else{
                    /*3、其他情况用域认证*/
                    $host=LDAP_IP;
                    $ad = ldap_connect($host);
                    $bd = ldap_bind($ad,$user,$password);
                    //认证通过
                    if($bd)
                    {
                        //读域信息
                        $userinfo=$model_user->getUserInfo($username,$password);
                        $userinfo['password']=$model_user->encrypt($password, 'E');
                        //4、根据域控信息查看本地user表,没有则插入,有则updata 并且都返回本地id
                        $local_userinfo=$model_user->getLocalUser($userinfo);
                        if($local_userinfo['state']==1)
                        {
                            //存session
                            session('userinfo',$local_userinfo);
                            //存ip和登陆时间
                            $model_user->login_log($local_userinfo);
                            //跳转地址
                            echo U("Admin/Index/index");

                        }else{
                            echo "此用户已禁用，请联系管理员！";
                        }
                       
                    }
                    else  //密码错误
                    {
                        echo "工号或密码错误";
                    }
                }
            }else{
               echo $model_user->getError();
            }          
        }else{
            $this->display();
        }
    }


    /**
     * 后台首页
     */
    public function index(){
        $model_index=new IndexModel();
        //1、读当前用户信息
        $userinfo=session('userinfo');
        //2、读取用户权限ids
        $authids=$model_index->get_auth_ids();
        //3、根据权限ids读取用户列表信息
        $userlist=$model_index->get_user_list($authids);
        $this->assign(array(
            'authinfo_1'=>$userlist['authinfo_1'],
            'authinfo_2'=>$userlist['authinfo_2'],
            'userinfo'=>$userinfo,
            'title'=>"维达员工自助系统后台"
        ));
        $this->display();
    }

    /**
     * 我的桌面
     */

    public function home(){
        $userinfo=session('userinfo');
        $table_inform=D('rzb_zxh_inform');
        $data=$table_inform->limit(5)->order('id desc')->select();
        $this->assign('data',$data);      
        $this->assign('userinfo',$userinfo);
        $this->display();
    }


    /*
     * 添加公告
     */
    public function gg_add(){
        $table_inform=D('rzb_zxh_inform');
        $_POST['add_name']=$_SESSION['userinfo']['name'];
        $_POST['add_time']=time();
        $z=$table_inform->add($_POST);
        if($z){
            echo "succeed";
        }else{
            echo "false";
        }
    }

    /*
     * 删公告
     */
    public function gg_del($id){
        $table_inform=D('rzb_zxh_inform');
        $z=$table_inform->delete($id);
    }





    /**
     * 退出
     */
    public function loginout(){
        session(null);
        header("Location:".U('login'));
    }
    




}