<?php

namespace app\home\controller;

use app\common\model\User;
use think\captcha\Captcha;
use think\Controller;
use think\Request;

class Login extends Controller
{
    /**
     * 显示登录页面
     *
     * @return \think\Response
     */
    public function index()
    {
        return view('Login');
    }

    /**
     * 登录跳转的方法
     *
     * @return \think\Response
     */
    public function login()
    {

        // 接收参数
        $param['username'] = input('username');
        $param['password'] = md5(md5(input("password")));
        $captcha = new \app\home\vaildate\captcha();
//        print_r($param);
//        die();
        // 做表单验证
        $result = $this->validate($param,
            [
                'username'  => 'require',
                'password'   => 'require',
            ],
            [
            'username'  =>  '请填写用户名',
            'password' =>  '请填写密码',
            ]
        );
        // 对验证类进行校验
        if (!$captcha->check(\request()->param())){
            return json([
                'code' => USER_LOGIN_VALIDATE_ERROR,
                //$userVaildata->getError()可以获取到具体的错误信息
                'msg' => $captcha->getError()
            ]);
        }
        if(true !== $result){
            // 验证失败 输出错误信息
            $this->error($result);
        }

        //对验证码进行验证
        //$userVaildata->check(Request::param())这个方法是获取用户输入的信息
        //$userVaildata->getError()这个方法是返回验证错误信息

        // 查询数据表中数据
        $data = User::select()->toArray();
//        print_r($data);
//        die();
        foreach ($data as $val){
            // 验证用户名是否存在表中
            // 如果不在表中
            if ($param['username'] != $val['username']){
                $this->error("用户名错误，请重试","home/Login/index");
            }
            // 如果用户名在表中，验证密码是否正确
            if ($param['password'] != $val['password']){
                $this->error("密码错误，请重试","home/Login/index");
            }else{
                // 将用户信息存到session中
                $user = User::where('username',$param['username'])->find()->toArray();
                session("user",$user);
                $this->redirect("home/Photo/index",['id'=>$user['id']]);
            }
        }
        //$data = User::where('username',$param['username'])->find()->toArray();
        //print_r($data);
        // 验证用户名密码是否正确

    }



    /**
     * 退出登录的方法
     *
     * @return \think\Response
     */
    public function LoginOut(){
        session("user",null);
        $this->redirect("home/Login/index");
    }
}
