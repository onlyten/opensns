<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
	<!-- <link href="/testsns/favicon.ico" rel="shortcut icon" /> -->
	<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<?php echo hook('syncMeta');?>

<?php $oneplus_seo_meta = get_seo_meta($vars,$seo); ?>
<?php if($oneplus_seo_meta['title']): ?><title><?php echo ($oneplus_seo_meta['title']); ?></title>
    <?php else: ?>
    <title><?php echo modC('WEB_SITE_NAME',L('_OPEN_SNS_'),'Config');?></title>
    <!-- <title>未命名</title> --><?php endif; ?>
<?php if($oneplus_seo_meta['keywords']): ?><meta name="keywords" content="<?php echo ($oneplus_seo_meta['keywords']); ?>"/><?php endif; ?>
<?php if($oneplus_seo_meta['description']): ?><meta name="description" content="<?php echo ($oneplus_seo_meta['description']); ?>"/><?php endif; ?>

<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" media="screen" />

<!-- zui -->
<link href="/testsns/Public/zui/css/zui.css" rel="stylesheet">

<link href="/testsns/Public/zui/css/zui-theme.css" rel="stylesheet">
<link href="/testsns/Public/static/os-icon/simple-line-icons.min.css" rel="stylesheet">
<link href="/testsns/Public/static/os-loading/loading.css" rel="stylesheet">
<link href="/testsns/Public/css/core.css" rel="stylesheet"/>
<link type="text/css" rel="stylesheet" href="/testsns/Public/js/ext/magnific/magnific-popup.css"/>
<!--<script src="/testsns/Public/js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="/testsns/Public/js/com/com.functions.js"></script>

<script type="text/javascript" src="/testsns/Public/js/core.js"></script>-->
<script src="/testsns/Public/js.php?f=js/jquery-2.0.3.min.js,js/com/com.functions.js,static/os-loading/loading.js,js/core.js,js/com/com.toast.class.js,js/com/com.ucard.js"></script>



<!--Style-->
<!--合并前的js-->
<?php $config = api('Config/lists'); C($config); $count_code=C('COUNT_CODE'); ?>
<script type="text/javascript">
    var ThinkPHP = window.Think = {
        "ROOT": "/testsns", //当前网站地址
        "APP": "/testsns/index.php?s=", //当前项目地址
        "PUBLIC": "/testsns/Public", //项目公共目录地址
        "DEEP": "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
        "MODEL": ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
        "VAR": ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"],
        'URL_MODEL': "<?php echo C('URL_MODEL');?>",
        'WEIBO_ID': "<?php echo C('SHARE_WEIBO_ID');?>"
    }
    var cookie_config={
        "prefix":"<?php echo C('COOKIE_PREFIX');?>",// cookie 名称前缀
        "path" :"<?php echo C('COOKIE_PATH');?>", // cookie 保存路径
        "domain":"<?php echo C('COOKIE_DOMAIN');?>" // cookie 有效域名
    }
    var Config={
        'GET_INFORMATION':<?php echo modC('GET_INFORMATION',1,'Config');?>,
        'GET_INFORMATION_INTERNAL':<?php echo modC('GET_INFORMATION_INTERNAL',10,'Config');?>*1000,
        'WEBSOCKET_ADDRESS':"<?php echo modC('WEBSOCKET_ADDRESS',gethostbyname($_SERVER['SERVER_NAME']),'Config');?>",
        'WEBSOCKET_PORT':<?php echo modC('WEBSOCKET_PORT',8000,'Config');?>
    }
    var weibo_comment_order = "<?php echo modC('COMMENT_ORDER',0,'WEIBO');?>";
</script>

<script src="/testsns/Public/lang.php?module=<?php echo strtolower(MODULE_NAME);?>&lang=<?php echo LANG_SET;?>"></script>

<script src="/testsns/Public/expression.php"></script>

<!-- Bootstrap库 -->
<!--
<?php $js[]=urlencode('/static/bootstrap/js/bootstrap.min.js'); ?>

&lt;!&ndash; 其他库 &ndash;&gt;
<script src="/testsns/Public/static/qtip/jquery.qtip.js"></script>
<script type="text/javascript" src="/testsns/Public/Core/js/ext/slimscroll/jquery.slimscroll.min.js"></script>
<script type="text/javascript" src="/testsns/Public/static/jquery.iframe-transport.js"></script>
-->
<!--CNZZ广告管家，可自行更改-->
<!--<script type='text/javascript' src='http://js.adm.cnzz.net/js/abase.js'></script>-->
<!--CNZZ广告管家，可自行更改end-->
<!-- 自定义js -->
<!--<script src="/testsns/Public/js.php?get=<?php echo implode(',',$js);?>"></script>-->

<?php D('Pushing')->doRun(); $key = C('DATA_AUTH_KEY'); $timestamp = time(); $signature = md5(is_login().$timestamp.$key); ?>
<script>
    //全局内容的定义
    var _ROOT_ = "/testsns";
    var MID = "<?php echo is_login();?>";
    var SIGNATURE = "<?php echo ($signature); ?>";
    var TIMESTAMP = "<?php echo ($timestamp); ?>";
    var MODULE_NAME="<?php echo MODULE_NAME; ?>";
    var ACTION_NAME="<?php echo ACTION_NAME; ?>";
    var CONTROLLER_NAME ="<?php echo CONTROLLER_NAME; ?>";
    var initNum = "<?php echo modC('WEIBO_NUM',140,'WEIBO');?>";
    function adjust_navbar(){
        $('#sub_nav').css('top',$('#nav_bar').height());
        $('#main-container').css('padding-top',$('#nav_bar').height()+$('#sub_nav').height()+20)
    }
</script>

<audio id="music" src="" autoplay="autoplay"></audio>
<!-- 页面header钩子，一般用于加载插件CSS文件和代码 -->
<?php echo hook('pageHeader');?>
</head>
<body>
	<!-- 头部 -->
	<script src="/testsns/Public/js/com/com.talker.class.js"></script>
<?php if((is_login()) ): ?><div id="talker">

    </div><?php endif; ?>


<?php D('Common/Member')->need_login(); ?>

<!--[if lt IE 8]>

<div class="alert alert-danger" style="margin-bottom: 0"><?php echo L('_TIP_BROWSER_DEPRECATED_1_');?> <strong><?php echo L('_TIP_BROWSER_DEPRECATED_2_');?></strong>

    <?php echo L('_TIP_BROWSER_DEPRECATED_3_');?> <a target="_blank"

                                          href="http://browsehappy.com/"><?php echo L('_TIP_BROWSER_DEPRECATED_4_');?></a>

    <?php echo L('_TIP_BROWSER_DEPRECATED_5_');?>

</div>

<![endif]-->

<script src="/testsns/Public/js/canvas.js"></script>

<script>

    $(document).ready(function () {

        $('[data-role="show_hide"]').click(function () {

            $("#search_box").slideToggle("slow");

        });

        $('[data-role="close"]').click(function () {

            $("#search_box").slideToggle("slow");

        });

        });



</script>

<div class="container-fluid topp-box" style="background-color:white;height:60px;">
<div class="container" style="max-width:1440px;height:0">
    <div>

        <div class="img-wrap">

            <?php $logo = get_cover(modC('LOGO',0,'Config'),'path'); $logo = $logo?$logo:'/testsns/Public/images/logo.png'; ?>

            <a class="navbar-brand logo" href="<?php echo U('Home/Index/index');?>" style="padding-left:0px;"><img src="<?php echo ($logo); ?>"/></a>

        </div>

    </div>

    <div>

    </div>

    <div class="box c-b-right" style="text-align: right">

        <?php if(is_login()): ?><li class="dropdown li-hover self-info">

                <?php $uid = is_login(); $reg_time = D('member')->where(array('uid' => $uid))->getField('reg_time'); $reg_date = date('Y-m-d', $reg_time); $self = query_user(array('title', 'avatar128', 'nickname', 'uid', 'space_url', 'score', 'title', 'fans', 'following', 'weibocount', 'rank_link')); $map = getUserConfigMap('user_cover'); $map['role_id'] = 0; $model = D('Ucenter/UserConfig'); $cover = $model->findData($map); $self['cover_id'] = $cover['value']; $self['cover_path'] = getThumbImageById($cover['value'], 273, 80); ?>
            </li>
                <style type="text/css">
                    #nickname{
                        color: #000;
                    }
                    #nick{
                        color: #000;
                    }
                    #nickname:hover {
                        color: #fff !important; 
                    }
                    #nick:hover {
                        color: #fff !important; 
                    }
                </style>

                <li class="li-hover">
                    <a id="nick" href="<?php echo U('ucenter/message/message');?>" style="margin-top:7px" target="_blank">
                        <i class="os-icon-bell"></i>
                    </a>
                    <a id="nick" href="javascript:" id="show_box" data-role="show_hide">
                        <i class="icon-search"></i>
                    </a>
                </li>


                <li class="dropdown-toggle dropdown-toggle-avatar li-hover show-hide-ul" style="margin-top:7px">
                    <a id="nickname" title="<?php echo L('_EDIT_INFO_');?>" href="#" data-toggle="dropdown" >
                        <span><?php echo ($self["nickname"]); ?></span>
                    </a>
                    <ul class="dropdown-menu  drop-self nav-menu" role="menu">
                        <div style="cursor: pointer" event-node="logout" >
                            <center>
                                    <i class="os-icon-logout"></i>
                                    退出登录
                            </center>
                        </div>
                    </ul>
                </li>

                

            <?php else: ?>

            <?php $open_quick_login=modC('OPEN_QUICK_LOGIN', 0, 'USERCONFIG'); $register_type=modC('REGISTER_TYPE','normal','Invite'); $register_type=explode(',',$register_type); $only_open_register=0; if(in_array('invite',$register_type)&&!in_array('normal',$register_type)){ $only_open_register=1; } ?>

            <script>

                var OPEN_QUICK_LOGIN = "<?php echo ($open_quick_login); ?>";

                var ONLY_OPEN_REGISTER = "<?php echo ($only_open_register); ?>";

            </script>

                <!-- <a class="top-btn" data-login="do_login"><?php echo L('_LOGIN_');?></a>

                <a class="top-btn" data-role="do_register" data-url="<?php echo U('Ucenter/Member/register');?>"><?php echo L('_REGISTER_');?></a> --><?php endif; ?>

    </div>

    <div class="container-fluid search-box" id="search_box" style="display: none">

        <canvas width="1835" height="374"></canvas>

        <div class="text-wrap">

            <div class="container text-box" style="margin: 0 auto!important;">

                <h1>无处不在,搜你所想</h1>

                <form class="navbar-form " action="<?php echo U('Home/Index/search');?>" method="post"

                      role="search" id="search">

                    <div class="search">

                        <span class="pull-left"><input type="text" name="keywords" class="input" placeholder="搜索同校好友"></span>

                        <a data-role="search"><i class="icon icon-search pull-right"></i></a>

                    </div>



                    </span>

                </form>



            </div>

            <div class="close-box" data-role="close">X</div>

        </div>

    </div>
</div>
</div>

<div style="height:12px;">
    
</div>










<script>

    $(function() {

        $('[data-role="search"]').click(function() {

            $("#search").submit();

        })

    })



    function displaySubMenu(li) {

        var subMenu = li.getElementsByTagName("ul")[0];

        subMenu.style.display = "block";

    }

    function hideSubMenu(li) {

        var subMenu = li.getElementsByTagName("ul")[0];

        subMenu.style.display = "none";

    }

</script>
	<!-- /头部 -->
	
	<!-- 主体 -->
	<div class="main-wrapper">
    
    <!--顶部导航之后的钩子，调用公告等-->
<?php echo hook('afterTop');?>
<!--顶部导航之后的钩子，调用公告等 end-->
    <div id="main-container" class="container">
        <div class="row">
            
    <style>
        .font{
            font-size: 25px;;
        }
    </style>

    <?php
 $img_id = modC('JUMP_BACKGROUND','','config'); if($img_id){ $background =get_cover($img_id,'path'); }else{ $background = '/testsns/Public/images/jump_background.jpg'; } ?>

    <div class="" style="padding:300px 100px 0 100px;height: 650px; background: url(<?php echo($background); ?>)">

<div class="text-center " style="margin: 0 auto; ">

<?php if(isset($success_message)) {?>

<div class="alert alert-success with-icon">
        <i class="icon-ok-sign"></i>
        <div class="content">

<p class="font"><?php echo($success_message); ?></p>


</div>

</div>

<?php }else{?>

<div class="alert alert-danger with-icon">
    <i class="icon-remove-sign"></i>
    <div class="content">

        <p class="font"> <?php echo($error_message); ?></p>


</div>
</div>

<?php }?>


    <p class="jump">
        页面自动 <a id="href" style="color: green" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></b>。

        或 <a href="http://<?php echo ($_SERVER['HTTP_HOST']); ?>/testsns" style="color: green">返回首页</a>
    </p>


    </div>

    </div>
<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait'),href = document.getElementById('href').href;
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                location.href = href;
                clearInterval(interval);
            };
        }, 1000);
    })();
</script>


        </div>
    </div>
</div>
	<!-- /主体 -->

	<!-- 底部 -->
	

<!-- jQuery (ZUI中的Javascript组件依赖于jQuery) -->


<!-- 为了让html5shiv生效，请将所有的CSS都添加到此处 -->
<link type="text/css" rel="stylesheet" href="/testsns/Public/static/qtip/jquery.qtip.css"/>


<!--<script type="text/javascript" src="/testsns/Public/js/com/com.notify.class.js"></script>-->

<!-- 其他库-->
<!--<script src="/testsns/Public/static/qtip/jquery.qtip.js"></script>
<script type="text/javascript" src="/testsns/Public/js/ext/slimscroll/jquery.slimscroll.min.js"></script>
<script type="text/javascript" src="/testsns/Public/static/jquery.iframe-transport.js"></script>

<script type="text/javascript" src="/testsns/Public/js/ext/magnific/jquery.magnific-popup.min.js"></script>-->

<!--<script type="text/javascript" src="/testsns/Public/js/ext/placeholder/placeholder.js"></script>
<script type="text/javascript" src="/testsns/Public/js/ext/atwho/atwho.js"></script>
<script type="text/javascript" src="/testsns/Public/zui/js/zui.js"></script>-->
<link type="text/css" rel="stylesheet" href="/testsns/Public/js/ext/atwho/atwho.css"/>

<script src="/testsns/Public/js.php?t=js&f=js/com/com.notify.class.js,static/qtip/jquery.qtip.js,js/ext/slimscroll/jquery.slimscroll.min.js,js/ext/magnific/jquery.magnific-popup.min.js,js/ext/placeholder/placeholder.js,js/ext/atwho/atwho.js,zui/js/zui.js&v=<?php echo ($site["sys_version"]); ?>.js"></script>
<script type="text/javascript" src="/testsns/Public/static/jquery.iframe-transport.js"></script>

<script src="/testsns/Public/js/ext/lazyload/lazyload.js"></script>

<script src="/testsns/Public/js/socket.io.js"></script>

<!-- 用于加载js代码 -->
<script>
    $(document).ready(function () {
        $('[data-role="add_more"]').click(function () {
            $(".footer-bar").fadeToggle();
            $("#add_more").hide();
        });
        $('[data-role="close_more"]').click(function () {
            $(".footer-bar").fadeToggle();
            $("#add_more").show("slow");
        });
    });
</script>
<!-- 页面footer钩子，一般用于加载插件JS文件和JS代码 -->
<?php echo hook('pageFooter', 'widget');?>
<!-- 调用全站公告部件-->
<?php echo W('Common/Announce/render');?>

<!-- 调用消息部件-->
<?php echo W('Common/Message/render');?>
<div class="hidden"><!-- 用于加载统计代码等隐藏元素 -->
    <?php echo ($count_code); ?>
    
</div>

<script>
    // VERSION_NAME 替换为项目的版本，VERSION_CODE 替换为项目的子版本
  //  new Bugtags('d6023daa6c7467634636c87b3f16213e','8.12','VERSION_CODE');
</script>

	<!-- /底部 -->
</body>
</html>