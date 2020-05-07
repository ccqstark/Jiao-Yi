<?php
namespace app\api\model;
use think\Model;
use think\Db;
use think\Session;
define('PERPAGE',2); //每页帖子数

Db::connect();

class MicroShare extends Model{

    public function postNew($author_id, $content){

        $db = Db::table('micro_share');
        $result = $db->indert([
            'share_author_id' => $author_id,
            'share_content'   => $content
        ]);

        return $result;
    }


}


