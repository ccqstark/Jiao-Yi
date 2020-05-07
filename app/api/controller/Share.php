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

class Share extends Controller{

    public function post(Request $request){

        $res = $request->post();
        $author_id = Session::get('user_id');
        $share_content = $res['connent'];

        $shareModel = new model\MicroShare;
        $result = $shareModel->postNew($author_id,$share_content);

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