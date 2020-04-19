$(document).ready(
    function () {
        $("#button").click(function () {
            var data={
                "username":$("#username").val(),
                "password":$("#password").val()
            };
            $.ajax({
                type:'POST',
                url:'http://47.107.76.178/jiaoyi/public/index.php/api/login/login',
                // url:'http://47.107.76.178/jiaoyi/public/index.php',
                data:JSON.stringify(data),
                contentType:'application/json',
                
                success:function (data) {
                    alert(data);
                },
                error:function () {
                    alert("ciao");
                }
            })
        })
    }
)// contentType:'application/json;charset=UTF-8',// url:'http://203.195.13.217/tutorial/public/index.php/index/index/simpleTest',// window.location.href="http://47.107.76.178/jiaoyi/public/index.php/api/login/login"