<?php
namespace app\api\model\VicWord;
//定义词典文件路径
define('_VIC_WORD_DICT_PATH_',__DIR__.'/Data/dict.igb');
require __DIR__.'/vendor/autoload.php';
use Lizhichao\Word\VicWord;

class Cut {

    public static function goCut($phrase){

        //type: 词典格式
        $vic = new VicWord('igb');

        //长度优先分词
        $word_array = $vic->getWord($phrase);

        return $word_array;
    }

}
