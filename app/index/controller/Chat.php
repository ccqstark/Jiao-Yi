<?php
namespace app\index\controller;

use think\Controller;
use think\Exception;

class Chat extends Controller
{
    public function chat()
    {
        $fromid = input("fromid");
        $toid = input("toid");
        $this->assign("fromid",$fromid);
        $this->assign("toid",$toid);
        return $this->fetch('chat');

	}

}
   
