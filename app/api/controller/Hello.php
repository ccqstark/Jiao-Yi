<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use app\api\model;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: token, Origin, X-Requested-With, Content-Type, Accept, Authorization");
header('Access-Control-Allow-Methods: POST,GET,PUT,DELETE');

if(request()->isOptions()){
    exit();
}

class Hello extends Controller{

    public function hello(Request $request){
        $res = $request->post();
        $username = $res['username'];
        $password = $res['password'];
        return json(['user'=>$username,'psw'=>$password]);

    }

}
