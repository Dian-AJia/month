<?php

namespace app\home\controller;

use think\Controller;
use think\Db;
use think\Request;

class Photo extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index($id)
    {
        $data = \app\common\model\Photo::where('uid',$id)->distinct(true)->field('brand')->select()->toArray();
       // print_r($data);
        //die();
        return view('list',['data'=>$data]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        // 做登录身份验证
        $user = session("user");
        if (!$user){
            $this->error("请登录","home/Login/index");
        }
        return view('create');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $user = session("user");
        //获取用户id
        $uid = $user['id'];
        // 标题
        $title = input("title");
        // 类型
        $brand = input('brand');
        // 获取表单上传文件
        $file = \request()->file('image');
        // 对传输的文字必填验证
        $result = $this->validate(
            [
                'title' => $title,
                'brand' => $brand
            ],
            [
                'title'  => 'require',
                'brand'   => 'require',
            ],
            [
                'title'  =>  '请填写标题',
                'brand' =>  '请填写分类',
            ]
        );
        if(true !== $result){
            // 验证失败 输出错误信息
            $this->error($result);
        }
        // 对图片做验证，并移动到框架应用根目录/public/uploads/ 目录下    图片分类的生成管理配置
        if ($file) {
            $info = $file->validate(['size'=>50000,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads'. DS . $brand);
            if ($info) {
                // 成功上传后 获取上传信息
                $path = DS.'uploads'.DS.$brand.DS.$info->getSaveName();
                //print_r($path);
                $data = ['uid'=>$uid,'title'=>$title,'brand'=>$brand,'image'=>$path];
                //print_r($data);
                $res = \app\common\model\Photo::create($data);
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
    }

    /**
     * 图片列表
     *
     */
    public function photolist()
    {
        $phot = Db::table('photo')->paginate(5);
        $photo = Db::table('photo')->paginate(5)->toArray();
        //$photo = \app\common\model\Photo::paginate(5)->toArray();
        $photo = $photo['data'];
        return view('photolist',['photo'=>$photo,'phot'=>$phot]);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }


    //搜索分类
    public function searchp(){
        $searchp = input('searchp');
        //print_r($searchp);
        $data = \app\common\model\Photo::where('brand',$searchp)->select()->toArray();
        return view('searchp',['data'=>$data]);
        //print_r($data);
    }

    //模糊
    public function search(){
        $search = input('search');
        $data = \app\common\model\Photo::where('brand','like',$search)->select()->toArray();
        return view('search',['data'=>$data]);

    }
}
