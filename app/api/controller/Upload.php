<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use app\api\model;
use think\Session;
use think\File;
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

    //头像
    public function profile(){

        // 获取表单上传文件
        $file = request()->file('images');
            
        if($file){
            // 移动到框架应用根目录/public/uploads/ 目录下
            $path = ROOT_PATH . 'public' . DS . 'uploads';
            $info = $file->rule('md5')->move($path);

            if($info){//上传成功
            
                //获取存储路径（日期目录+图片命名.后缀）
                $image_path = $info->getSaveName();
                //存入数据库
                $userModel = new model\UserBaseInfo;
                $result = $userModel->uploadProfile($image_path);
                if($result){
                    return([
                        'resultCode'=>1,
                        'msg'=> 'success'
                    ]);
                }else{
                    return([
                        'resultCode'=>0,
                        'msg'=> 'failed'
                    ]);
                }

            }else{
                $error = $file->getError();
                // 上传失败获取错误信息
                return([
                    'resultCode'=>0,
                    'msg'=> $error
                ]);            
            }
        }        
    }


    //商品图片
    public function commodityImage(){

        // 获取表单上传文件
        $file = request()->file('images');
        $commodity_id = Session::get('new_commodity');   
        if($file){
            // 移动到框架应用根目录/public/uploads/ 目录下
            $path = ROOT_PATH . 'public' . DS . 'uploads';
            $info = $file->rule('md5')->move($path);
    
            if($info){//上传成功
               
                //获取存储路径（日期目录+图片命名.后缀）
                $image_path = $info->getSaveName();
                //存入数据库
                $commodeityModel = new model\CommodityBaseInfo;
                $ressult = $commodeityModel->uploadImage($commodity_id,$image_path);
                if($result){
                    return([
                        'resultCode'=>1,
                        'msg'=> 'success'
                    ]);
                }else{
                    return([
                        'resultCode'=>0,
                        'msg'=> 'failed'
                    ]);
                }   
    
            }else{
                $error = $file->getError();
                // 上传失败获取错误信息
                return([
                    'resultCode'=>0,
                    'msg'=> $error
                ]);            
            }
        }       
    }



}
