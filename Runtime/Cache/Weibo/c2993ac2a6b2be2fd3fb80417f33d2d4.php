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

            <a class="navbar-brand logo" href="<?php echo U('Home/Index/index');?>" style="margin-left:52px"><img src="<?php echo ($logo); ?>"/></a>

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
                    <a id="nick" href="<?php echo U('ucenter/message/message');?>" target="_blank" style="margin-top:7px;">
                        <div class="message-num" data-role="now-message-num"  style="display: none;"></div>
                        <i class="os-icon-bell"></i>
                    </a>
                    <a id="nick" href="javascript:" id="show_box" data-role="show_hide">
                        <i class="icon-search"></i>
                    </a>
                </li>

                <li class="dropdown-toggle dropdown-toggle-avatar li-hover show-hide-ul" style="margin-top:7px;margin-right:60px">
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
                <!-- <li class="li-hover">
                    <a href="javascript:" id="show_box" data-role="show_hide">
                        <i class="icon-search"></i>
                    </a>
                </li> -->


            <?php else: ?>

            <?php $open_quick_login=modC('OPEN_QUICK_LOGIN', 0, 'USERCONFIG'); $register_type=modC('REGISTER_TYPE','normal','Invite'); $register_type=explode(',',$register_type); $only_open_register=0; if(in_array('invite',$register_type)&&!in_array('normal',$register_type)){ $only_open_register=1; } ?>

            <script>

                var OPEN_QUICK_LOGIN = "<?php echo ($open_quick_login); ?>";

                var ONLY_OPEN_REGISTER = "<?php echo ($only_open_register); ?>";

            </script>

                <!-- <a class="top-btn" data-login="do_login"><?php echo L('_LOGIN_');?></a>

                <a class="top-btn" data-role="do_register" data-url="<?php echo U('Ucenter/Member/register');?>"><?php echo L('_REGISTER_');?></a> --><?php endif; ?>

    </div>

    <div class="container-fluid search-box" id="search_box" style="display: none;">

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
        #main-container {
            width: 1000px;
            margin-top: 70px;
        }
    </style>
    <script type="text/javascript" src="/testsns/Public/js/ajaxfileupload.js"></script>
    <link href="/testsns/Application/Weibo/Static/css/weibo.css" type="text/css" rel="stylesheet"/>
    <link href="//at.alicdn.com/t/font_1476322193_997997.css" type="text/css" rel="stylesheet"/>
    <link rel="stylesheet" href="<?php echo getRootUrl();?>Addons/InsertXiami/_static/css/xiami.css">
    <!--微博内容列表部分-->
    <div class="weibo_middle pull-left">
        <?php if($show_post): ?><div style="display: none" class="weibo_content weibo-content-post weibo_post_box" id="send_box">
        <div class="weibo_content_p">
            <div class="send-top  row" id="input_tip">
                <p class="pull-right limit-num">
                    <span><?php echo modC('WEIBO_NUM',140,'WEIBO');?></span> / <span><?php echo modC('WEIBO_NUM',140,'WEIBO');?></span>
                </p>
            </div>
            <div class="tare">
                 <textarea class="form-control weibo-word" id="weibo_content" placeholder="<?php echo modC('WEIBO_INFO',L('_TIP_SOMETHING_TO_SAY_'));?>" onfocus="startCheckNum_quick($(this))"
                           onblur="endCheckNum_quick()"></textarea>
            </div>
            <div class="op-wrap">
                <!--插件-->
                <div class="pull-left row addons-wrap">
                    <a title="插入表情" href="javascript:" onclick="insertFace($(this))" data-role="insert_face">
                        <i class="iconfont icon-biaoqing i-bq" ></i>
                    </a>
                    <?php if(modC('CAN_IMAGE',1)): ?><a title="插入图片" href="javascript:" id="insert_image" onclick="insert_image.insertImage(this)" data-role="hook_show">
                            <i class="iconfont icon-tupian i-tp" ></i>
                        </a><?php endif; ?>
                    <?php if(modC('CAN_TOPIC',1)): ?><a title="插入话题" href="javascript:" onclick="insert_topic.InsertTopic(this)">
                            <i class="iconfont icon-tianjiahuati i-ht"></i>
                        </a><?php endif; ?>
                    <?php echo hook('weiboType');?>
                    <div id="emot_content" class="emot_content"></div>
                    <div id="hook_show" class="emot_content"></div>
                </div>

                <div class="pull-right right-wrong">
                    <a href="javascript:" class="send-right" data-role="send_weibo" data-url="<?php echo U('Weibo/Index/doSend');?>"><i class="iconfont icon-dui"></i></a>
                    <a href="javascript:" class="send-wrong" data-role="change_back"><i class="iconfont icon-cuo"></i></a>
                </div>
                <!--话题-->
                <div class="pull-right"><?php echo use_topic();?></div>
            </div>
        </div>
    </div>
    <script>
        var ID_setInterval;
        function checkNum_quick(obj) {
            var value = obj.val();
            var value_length = value.length;
            var can_in_num = initNum - value_length;
            if (can_in_num < 0) {
                value = value.substr(0, initNum);
                obj.val(value);
                can_in_num = 0;
            }
            var html =  can_in_num + " / "+initNum;
            $('.limit-num').html(html);
        }
        function startCheckNum_quick(obj) {
            ID_setInterval = setInterval(function () {
                checkNum_quick(obj);
            }, 250);
        }
        function endCheckNum_quick() {
            clearInterval(ID_setInterval);
        }

        $('[data-role="insert_face"]').click(function() {
            $("#hook_show").css("display", "none");
            $("#emot_content").css("display", "block");
        });
        $('[data-role="hook_show"]').click(function() {
            $("#emot_content").css("display", "none");
            $("#hook_show").css("display", "block");
        });
        $('[data-role="change_back"]').click(function() {
           $("#send_box").hide();
           $(".black-filter").show();
           $.cookie("wb_type","");
        })
    </script>
    <script type="text/javascript" charset="utf-8" src="/testsns/Public/js/ext/webuploader/js/webuploader.js"></script>
    <link href="/testsns/Public/js/ext/webuploader/css/webuploader.css" type="text/css" rel="stylesheet"><?php endif; ?>



        <!--  筛选部分-->
        <div class="black-filter row">
            <div class="s-wb-box" data-role="show-sendBox">
                <div class="s-wb-icon">
                    <i class="icon-zs"></i>
                </div>
                <p>发动态</p>
            </div>
        </div>
        <div class="weibo-filter-wrap">
            <div class="add-weibo" data-role="switch_sendBox">
                <span><?php echo modC('WEIBO_INFO',L('_TIP_SOMETHING_TO_SAY_'));?></span><i class="send-icon"></i>
            </div>
            <?php if(!is_login()) $style='margin-top:0' ?>
            <div id="weibo_filter">
                <div class="weibo_icon">
                    <?php $show_icon_eye_open=0; if(count($top_list)){ $hide_ids=cookie('Weibo_index_top_hide_ids'); if(mb_strlen($hide_ids,'utf-8')){ $hide_ids=explode(',',$hide_ids); foreach($top_list as $val){ if(in_array($val,$hide_ids)){ $show_icon_eye_open=1; break; } }}} if(count($top_list)){ if($show_icon_eye_open){ ?>
                    <li data-weibo-id="<?php echo ($weibo["id"]); ?>" title="<?php echo L('_SHOW_ALL_TOP_'); echo ($MODULE_ALIAS); ?>"
                        data-role="show_all_top_weibo">
                        <i class="icon icon-eye-open"></i>
                    </li>
                    <?php }else{ ?>
                    <li data-weibo-id="<?php echo ($weibo["id"]); ?>" title="<?php echo L('_SHOW_ALL_TOP_'); echo ($MODULE_ALIAS); ?>"
                        data-role="show_all_top_weibo" style="display: none;">
                        <i class="icon icon-eye-open"></i>
                    </li>
                    <?php }} ?>
                </div>
                <?php if(is_array($tab_config)): $i = 0; $__LIST__ = $tab_config;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tab): $mod = ($i % 2 );++$i;?><li class="a-wrap">
                        <a id="<?php echo ($tab); ?>"
                        <?php if(in_array($tab,$need_login_tab)): ?>href="javascript:toast.error('请先登录！');"
                            <?php else: ?>
                            href="<?php echo U('Weibo/Index/index',array('type'=>$tab));?>"<?php endif; ?>
                        >
                        <?php switch($tab): case "concerned": ?><i class="icon icon-flow"></i>
                                <span><?php echo L('_MY_FOLLOWING_');?></span><?php break;?>
                            <?php case "hot": ?><i class="icon icon-hot"></i>
                                <span><?php echo L('_HOT_WEIBO_');?></span><?php break;?>
                            <?php case "all": ?><i class="icon icon-all"></i>
                                <span><?php echo L('_ALL_WEBSITE_WEIBO_');?></span><?php break;?>
                            <?php case "fav": ?><i class="icon icon-my"></i>
                                <span><?php echo L('_MY_FAV_');?></span><?php break; endswitch;?>
                        </a>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
            <script>
                $('#weibo_filter #<?php echo ($filter_tab); ?>').addClass('active');
            </script>
        </div>
        <input type="hidden" value="<?php echo ($smallnav); ?>" id="smallnav">
        <div class="small-nav" >
            <li class="list-type select" data-role="select-li">
                <a id="all_" href="<?php echo U('Weibo/Index/index',array('select'=>'all_'));?>">
                    <p class="mg-bt0">全部</p>
                </a>
            </li>
            <li class="list-type" data-role="select-li">
                <a id="image" href="<?php echo U('Weibo/Index/index',array('select'=>'image'));?>">
                    <p class="mg-bt0">多图</p>
                </a>
            </li>

            <li class="list-type" data-role="select-li">
                <a id="video" href="<?php echo U('Weibo/Index/index',array('select'=>'video'));?>">
                    <p class="mg-bt0">视频</p>
                </a>
            </li>
            <li class="list-type" data-role="select-li">
                <a id="musics" href="<?php echo U('Weibo/Index/index',array('select'=>'musics'));?>">
                    <p class="mg-bt0">音乐</p>
                </a>
            </li>
            <li class='small-nav-search'>
                <div class="search-wrap">
                    <form style="margin-right: -24px;display: none;" id="search-form" action="<?php echo U('Weibo/Index/search');?>" method="post" role="search">
                        <input class="wb-search" id="search-text" type="text" placeholder="输入关键字" name="keywords" value="">
                        <i class="icon-search" style="left: -25px;cursor: pointer;" data-role="do-search" ></i>
                    </form>
                </div>
                <div class="animate-wrap" data-role="search-btn">
                    <i class="icon-search" ></i>
                </div>
            </li>
        </div>
        <!--筛选部分结束-->
        <div id="top_list">
            <?php if(is_array($top_list)): $i = 0; $__LIST__ = $top_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$top): $mod = ($i % 2 );++$i; echo W('WeiboDetail/detail',array('weibo_id'=>$top,'can_hide'=>1)); endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <div id="weibo_list">
            <?php if($page != 1){ ?>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$weibo): $mod = ($i % 2 );++$i; echo W('Weibo/WeiboDetail/detail',array('weibo_id'=>$weibo)); endforeach; endif; else: echo "" ;endif; ?>
<?php if(empty($lastId) == false): ?><script>
        weibo.lastId = '<?php echo ($lastId); ?>';
    </script><?php endif; ?>


            <?php } ?>

        </div>
        <div id="load_more" class="text-center text-muted"
        <?php if($page != 1): ?>style="display:none"<?php endif; ?>
        >
        <div id="load_more_text">
            <div class="sk-cube-grid">
                <div class="sk-cube sk-cube1"></div>
                <div class="sk-cube sk-cube2"></div>
                <div class="sk-cube sk-cube3"></div>
                <div class="sk-cube sk-cube4"></div>
                <div class="sk-cube sk-cube5"></div>
                <div class="sk-cube sk-cube6"></div>
                <div class="sk-cube sk-cube7"></div>
                <div class="sk-cube sk-cube8"></div>
                <div class="sk-cube sk-cube9"></div>
            </div>
        </div>
    </div>

    <!--分页-->
    <div id="index_weibo_page" style=" <?php if($page == 1): ?>display:none<?php endif; ?>">
        <div class="text-right">
            <?php echo getPagination($total_count,10);?>
        </div>
    </div>
    </div>

    <!--微博内容列表部分结束-->
    <!--首页右侧部分-->
    <div class="weibo_right">
    <!--我的社群stat-->
    <?php if(is_login()): ?><div class="my-community">
            <div class="ck-sign">
                <?php if(!$check){ ?>
                <img src="/testsns/Application/Weibo/Static/images/checking.png" alt="">
                <?php }else{ ?>
                <img src="/testsns/Application/Weibo/Static/images/checked.png" alt="">
                <?php } ?>
            </div>
            <p class="notice-head">我的社群</p>
            <div class="mc-grade row">
                <div class="col-xs-3 mc-box left-box">
                    <div class="mc-icon-wrap">
                        <i class="i-heart"></i>
                    </div>
                </div>
                <div class="col-xs-4 mc-box center-box">
                    <span><?php echo ($today_score); ?></span>
                    <p>今日经验</p>
                </div>
                <div class="col-xs-5 mc-box text-ellipsis">
                    <span style="color: #666"><?php echo (intval($title["left"])); ?></span>
                    <p style="margin: 0 25px">剩余经验</p>
                </div>
            </div>
            <div class="grade-box">
                <?php $title=D('Ucenter/Title')->getCurrentTitleInfo(is_login()); ?>
                <script>
                    $(function () {
                        $('#upgrade').tooltip({
                                    html: true,
                                    title: '<?php echo L("_CURRENT_LEVEL_");?>：<?php echo ($self["title"]); ?> <br/><?php echo L("_NEXT_LEVEL_");?>：<?php echo ($title["next"]); ?><br/><?php echo L("_NOW_");?>：<?php echo ($self["score"]); ?><br/><?php echo L("_NEED_");?>：<?php echo ($title["upgrade_require"]); ?><br/><?php echo L("_LAST_");?>： <?php echo ($title["left"]); ?><br/><?php echo L("_PROGRESS_");?>：<?php echo ($title["percent"]); ?>% <br/> '
                                }
                        );
                    })
                </script>
                <div class="row">
                    <div class="col-xs-3 l-box"><span>当前等级</span></div>
                    <div class="col-xs-9 r-box">
                        <div id="upgrade" class="upgrade" data-toggle="tooltip" data-placement="bottom" title="">
                            <div class="grade-bottom"></div>
                            <div class="grade-top" style="width:<?php echo ($title["percent"]); ?>%;"></div>
                        </div>
                    </div>
                </div>

                <div class="row gg-wrap">
                    <p class="pull-left"><?php echo ($self["title"]); ?></p>
                    <p class="pull-right" style="color: #999"><?php echo ($title["next"]); ?></p>
                </div>
            </div>
            <div class="gg-check row">
                <div class="col-xs-3 c-box" data-role="open_checkBox" style="cursor: pointer">
                    <?php if(!$check){ ?>
                    <i class="c-icon c-icon-checking"></i>
                    <p style="color:red;">签到</p>
                    <?php }else{ ?>
                    <i class="c-icon c-icon-checked"></i>
                    <p>签到</p>
                    <?php } ?>
                </div>
                <a href="<?php echo U('Ucenter/index/ranking');?>">
                    <div class="col-xs-3 c-box">
                        <i class="c-icon c-icon-rank"></i>
                        <p class="pc6">排行</p>
                    </div>
                </a>
                <?php if($ping || $Charge): ?><a href="<?php echo ($ping?U('Pingxx/index/index'):U('Recharge/index/index')); ?>">
                        <div class="col-xs-3 c-box">
                            <i class="c-icon c-icon-ping"></i>
                            <p class="pc6">充值</p>
                        </div>
                    </a><?php endif; ?>

                <?php if($shop): ?><a href="<?php echo U('Shop/index/index');?>">
                        <div class="col-xs-3 c-box">
                            <i class="c-icon c-icon-change"></i>
                            <p class="pc6">兑换</p>
                        </div>
                    </a><?php endif; ?>
            </div>
            <div class="show-more" data-role="show_more_link">
                <i class="icon-angle-down" style="font-size: 20px"></i>
            </div>
            <div class="close-more" data-role="close_more_link">
                <i class="icon-angle-up" style="font-size: 20px"></i>
            </div>
        </div><?php endif; ?>
    <!--我的社群end-->
    <!--签到日历stat-->
    <div class="hide-check-box" style="display: none">
        <h5>
            <a href="javascript:">我的签到日历</a>
            <a href="javascript:" class="pull-right" data-role="close_checkBox">X</a>
        </h5>
        <?php echo W('Ucenter/signCalendar/render');?>
        <div class="checkin">
            <?php echo hook('checkIn');?>
        </div>
    </div>
    <!--签到日历end-->
    <!--热门话题排行start-->
    <div class="wb-topic">
        <p class="topic-head">话题排行</p>
        <div class="topic-content">
            <ul>
                <?php if(is_array($hot_topic_list)): $one_topic_key = 0; $__LIST__ = $hot_topic_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$one_topic): $mod = ($one_topic_key % 2 );++$one_topic_key; if($one_topic_key<=3){ $one_topic_class='num-top'; }else{ $one_topic_class=''; } ?>
                    <li><a href="<?php echo U('Topic/index',array('topk'=>$one_topic['id']));?>" title="<?php echo ($one_topic['name']); ?>">
                        <div class="num <?php echo ($one_topic_class); ?>"><?php echo ($one_topic_key); ?></div>
                        <div><?php echo ($one_topic['name']); ?></div>
                        <div><?php echo ($one_topic['weibo_num']); ?></div>
                    </a></li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
    <!--热门话题排行end-->
    <?php echo W('Common/Adv/render',array(array('name'=>'below_self_info','type'=>1,'width'=>'280px','height'=>'100px','margin'=>'0
    0 10px 0','title'=>'个人资料下方')));?>
    <div style="position: relative">
        <?php echo hook('weiboSide');?>
        <!--广告位-->
        <?php echo W('Common/Adv/render',array(array('name'=>'below_checkrank','type'=>1,'width'=>'280px','height'=>'100px','title'=>'签到下方广告')));?>
        <!--广告位end-->
        <?php echo hook('Advs',array('pos'=>'weibo_right_below_all','type'=>1,'width'=>'280px','height'=>'100px','title'=>'微博右侧底部广告'));?>
    </div>
</div>
    <!--首页右侧部分结束-->

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


    <script src="/testsns/Application/Weibo/Static/js/weibo.js"></script>
    <script>
        var tag_id="<?php echo ($smallnav); ?>";
        $('.list-type').removeClass('select');
        $('#'+tag_id).parents('li').addClass('select');

        var SUPPORT_URL = "<?php echo addons_url('Support://Support/doSupport');?>";
        weibo.page = '<?php echo ($page); ?>';
        weibo.loadCount = 0;
        weibo.lastId = parseInt('<?php echo (reset($list)); ?>') + 1;
        weibo.url = "<?php echo ($loadMoreUrl); ?>";
        weibo.type = "<?php echo ($type); ?>";
        $(function () {
            weibo_bind();
            //当屏幕滚动到底部时
            if (weibo.page == 1) {
                $(window).on('scroll', function () {
                    if (weibo.noMoreNextPage) {
                        return;
                    }
                    if (weibo.isLoadingWeibo) {
                        return;
                    }
                    if (weibo.isLoadMoreVisible()) {
                        weibo.loadNextPage();
                    }
                });
                $(window).trigger('scroll');
            }
        });
        $(document).ready(function () {
            $('[data-role="switch_sendBox"]').click(function () {
                if (is_login()) {
                    var wb_cookie = $.cookie("wb_type");
                    $(".add-weibo").hide();
                    if (wb_cookie == 'tp_value'){
                        $("#send_box").show();
                    }
                    else{
                        $(".black-filter").slideToggle();
                    }
                } else {
                    toast.error('请先登录！');
                }
            });
            $('[data-role="show-sendBox"]').click(function () {
                $("#send_box").show();
                $("#weibo_content").focus();
                $(".black-filter").hide();
                $.cookie('wb_type', 'tp_value',{ expires: 7 });
            });
            $('[data-role="open_checkBox"]').click(function () {
                $(".hide-check-box").fadeToggle("slow");
            });
            $('[data-role="close_checkBox"]').click(function () {
                $(".hide-check-box").fadeToggle("slow");
            });

            var divNum = $(".c-box").size();
            if(divNum>4){
                $('.show-more').css('display','block');
            }
            $('[data-role="show_more_link"]').click(function () {
                $('.gg-check').addClass('c-class');
                $('.show-more').hide();
                $('.close-more').show()
            });
            $('[data-role="close_more_link"]').click(function () {
                $('.gg-check').removeClass('c-class');
                $('.show-more').show();
                $('.close-more').hide()
            });
        });
    </script>
    <link rel="stylesheet" href="/testsns/Application/Weibo/Static/css/photoswipe.css">
    <link rel="stylesheet" href="/testsns/Application/Weibo/Static/css/default-skin/default-skin.css">
    <script src="/testsns/Application/Weibo/Static/js/photoswipe.min.js"></script>
    <script src="/testsns/Application/Weibo/Static/js/photoswipe-ui-default.min.js"></script>

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