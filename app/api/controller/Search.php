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

class Search extends Controller{

    //搜索引擎，加分词提高精确度与结果范围
    public function search(Request $resuest){

        $res = $resuest->post();
        $keyWord = $res['keyWord'];

        $searchModel = new model\SearchEngine;
        $search_result = $searchModel->goSearch($keyWord);

        $len = sizeof($search_result);
        if(!$len){
            return json([
                'resultCode' => 0,
                'msg' => 'no result'
            ]);
        }else{
        return json([
                'resultCode' => 1,
                'data' => $search_result,
                'msg' => 'success'
            ]);
         }
    }



}

