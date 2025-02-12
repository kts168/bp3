<?php

    session_start();

    require_once("../config.php");
    require_once("../functions.php");
    
    if(empty($_SESSION['user'])){
        if($config['control']['open_grant']==0){
            echo '{"msg":"user not login"}';
            die;
        }
    }
    
        
    // 允许携带重定向参数，参数为get，参数名display
    // 携带参数访问本页面，则在获取授权后携带结果重定向请求参数地址
    $display = $_GET['display'];
    if(isset($display)){
        // 存在redirect参数
        $_SESSION['display'] = $display;
    }else{
        // 不存在重定向，则在默认页面显示
        $_SESSION['display'] = "display";
    }
    
    
    // 1. 获取基础信息
    $title = $config['site']['title'];
    $app_id = $config['connect']['app_id'];
    $redrect_uri = $config['connect']['redirect_uri'];
    
    // 2. 拼接授权信息
    $state = rand(10,100);
    $_SESSION['state'] = $state;
    
    // 2.1自动检测链接
    $conn = "https://openapi.baidu.com/oauth/2.0/authorize?response_type=code&client_id=$app_id&redirect_uri=$redrect_uri&scope=basic,netdisk&display=popup&state=$state";
    // 2.2强制登录链接
    $force_conn = $conn.'&force_login=1';
    // 3. 点击下面的链接以获取token
?>
<!doctype html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>bp3授权系统</title>
    </head>
    <body>
        <h2>这是<a href=".."><?php echo $title;?></a>的简易授权系统</h2>
        <?php
            if($_SESSION['result']){
                echo "<h2>提示：当前已经获取授权，查看最后一次<a href='./display.php'>授权结果</a></h2>";
            }
        
        ?>
        <h2>获取授权方式：手动获取</h2>
        <p>手动授权方式，是指手动点击授权，即可获取授权信息</p>
        <p><b>提示：</b>原始access_token必须通过手动获取</p>
        <p>点击右边的链接，然后获取授权
            》》<a id="link" href="<?php echo $conn;?>">授权链接</a></p>
        <p><b>提示：</b>默认自动检测当前登录的百度账号，
        如果需要强制登录，请勾选<input id="force" type="checkbox"/><label for="force">强制登录</label></p>
        <script>
            let auto_login = `<?php echo $conn;?>`
            let force_ligin = `<?php echo $force_conn;?>`
            let force = document.getElementById("force")
            let link = document.getElementById("link")
            let changeNum = 1;
            force.onchange = function(){
                changeNum++;
                if(changeNum%2==0){
                    link.href = force_ligin
                }else{
                    link.href = auto_login
                }
            }
        </script>
    </body>
</html>