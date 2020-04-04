<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use app\index\model;

class UserTestIndex extends Controller{
    
    public function index(Request $request){
        $res = $request->post(); 
        $updateUser = $res["username"];
        $updateField = $res["field"];
        $newInfo = $res["new"];
        $usertest = new model\UserTest;
        // $result = $usertest->insertUserInfo($res);
        $result = $usertest->changeInfo($updateUser,$updateField,$newInfo);
        return json(['result'=>$result]); 

    }
}