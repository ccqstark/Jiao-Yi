<?php
namespace app\api\controller;
use think\Controller; 

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: token, Origin, X-Requested-With, Content-Type, Accept, Authorization");
header('Access-Control-Allow-Methods: POST,GET,PUT,DELETE');

if(request()->isOptions()){
    exit();
}

require_once("PHPMailer.php");
require_once("SMTP.php");

class Mailer extends Controller{
    
    public static function mailsender($address,$vercode){

        //发出
        self::sendMail("$address", '验证码', 
        '【蕉忆】宁好，宁注册的邮箱验证码为'.$vercode.'。
        可不用妥善保存，可以随意丢弃删除，甚至可以告诉他人(狗头)。'); 
        
    }
     
     
    //发邮件所需函数
    public static function sendMail($to, $title, $content){

        //实例化PHPMailer核心类
        $mail = new \PHPMailer\PHPMailer\PHPMailer();

        //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
        $mail->SMTPDebug = 1;

        //使用smtp鉴权方式发送邮件
        $mail->isSMTP();

        //smtp需要鉴权 这个必须是true
        $mail->SMTPAuth = true;

        //链接qq域名邮箱的服务器地址
        $mail->Host = 'smtp.163.com';

        //设置使用ssl加密方式登录鉴权
        $mail->SMTPSecure = 'ssl';

        //设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587
        $mail->Port = 465;

        //设置发件人的主机域 可有可无 默认为 内容任意，建议使用你的域名
        $mail->Hostname = '47.107.76.178';

        //设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
        $mail->CharSet = 'UTF-8';

        //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $mail->FromName = '蕉忆官方';

        //smtp登录的账号 这里填入字符串格式的qq号即可
        $mail->Username = 'ccqstark';

        //smtp登录的密码 使用生成的授权码（就刚才叫你保存的最新的授权码）
        $mail->Password = 'ccq1428';

        //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
        $mail->From = 'ccqstark@163.com';

        //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
        $mail->isHTML(true);

        //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
        $mail->addAddress($to, '用户弟弟');

        //添加该邮件的主题
        $mail->Subject = $title;

        //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
        $mail->Body = $content;

        $status = $mail->send();
    

    }


}