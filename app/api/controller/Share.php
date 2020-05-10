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

    //发布微享
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

    //微享内容查看含有权限分级
    //微享第一页
    public function browseFirst(){
        
        $shareModel = new model\MicroShare;
        $data = $shareModel->getFirstPage();

        if(!$data){ //无内容
            return json([
                'resultCode' => 0,
                'msg' => 'no content'
            ]);
        }else{  //有内容
            return json([
                'resultCode' => 1,
                'data' => $data
            ]);
        }
    }


    //微享下一页
    public function browseNext(){

        $shareModel = new model\MicroShare;
        $data = $shareModel->getNextPage();

        if(!$data){ //无内容
            return json([
                'resultCode'=>0,
                'msg' => 'no content'
            ]);
        }else{  //有内容
            return json([
                'resultCode' => 1,
                'data' => $data
            ]);
        }     
    }







}