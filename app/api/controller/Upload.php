<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use app\api\model;
use think\Session;
//CORS跨域
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: token, Origin, X-Requested-With, Content-Type, Accept, Authorization");
header('Access-Control-Allow-Methods: POST,GET,PUT,DELETE');
if(request()->isOptions()){
    exit();
}
//防XSS
// ini_set("session.cookie_httponly", 1);

class Upload extends Controller{

    public function image(Requset $requset){

        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('images');
    
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                // echo $info->getExtension();
                // // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                // echo $info->getSaveName();
                // // 输出 42a79759f284b767dfcb2a0197904287.jpg
                // echo $info->getFilename(); 
                return json([
                    'resultCode'=> 1,
                    'msg'=>'good job!'
                ]);
            }else{
                // 上传失败获取错误信息
                $error =  $file->getError();
                return json([
                    'resultCode'=> 0,
                    'msg'=>$error
                ]);
            }
        }


    }




}
