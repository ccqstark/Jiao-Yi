<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use app\api\model;
use app\api\validate;
use app\api\controller\Mailer;
use think\Session;
//CORS跨域
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: token, Origin, X-Requested-With, Content-Type, Accept, Authorization");
header('Access-Control-Allow-Methods: POST,GET,PUT,DELETE');
if(request()->isOptions()){
    exit();
}

class Focus extends Controller{

    //添加关注
    public function follow(Request $request){

        $res = $request->post();
        $user_id = Session::get('user_id');
        $focus_id = $res['focus_id'];

        $focusModel = new model\UserFollow;
        $result = $focusModel->addFollow($user_id, $focus_id);

        if($result){
            return json([
                'resultCode' => 1,
                'msg' => 'success'
            ]);
        }else{
            return json([
                'resultCode' => 0,
                'msg' => 'failed'
            ]);
        }
    }


    







}