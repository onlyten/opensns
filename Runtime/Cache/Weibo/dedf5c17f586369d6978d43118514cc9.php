<?php if (!defined('THINK_PATH')) exit();?><div style="max-width: 680px;margin-bottom: 10px;" data-role="id_weibo" id="weibo_48" class="" >
    <div class="all-wrap">
                <div class="weibo-content">
            <div class="content-head">
                <div class="avat-box pull-left">
                    <a href="/testsns/index.php?s=/ucenter/index/index/uid/100.html" ucard="100">
                        <img src="/testsns/Uploads/Avatar/100/new_128_128.jpg" class="avatar-img"/>
                    </a>
                    <div class="show-follow pull-right">
                                                <div class="follow-btn" style="display: none;">
                                
                            </div>                    </div>
                </div>
                <div class="op-box pull-right">
                    <div class="op-tb op-top">
                        <a ucard="100" href="/testsns/index.php?s=/ucenter/index/index/uid/100.html" class="user_name">
                                                            donkey                        </a>
                        <small class="font_grey">Lv1 实习</small>                        &nbsp;
                        <!--隐藏操作列表-->
                        <div class="pull-right show-operate-wrap">
                            <a href="javascript:" class="show-operate pull-right icon-angle-down"></a>
                            <div class="operate-box" >
                                                                <li data-weibo-id="48" title="删除" data-role="del_weibo">
                                        删除
                                    </li>                                                                <li><a data-type="ajax" data-url="/testsns/index.php?s=/home/addons/execute/_addons/report/_controller/report/_action/eject/param/type%3D%25E5%258A%25A8%25E6%2580%2581%252F%25E5%258A%25A8%25E6%2580%2581%26url%3DWeibo%252FIndex%252FweiboDetail%253Fid%253D48%26data%255Bweibo-id%255D%3D48.html" data-title="举报"  data-toggle="modal" >举报</a></li>
                            </div>
                        </div>
                    </div>
                    <div class="op-tb op-bottom">
                        <a data-hover="查看详情" class="wb-time" href="/testsns/index.php?s=/weibo/index/weibodetail/id/48.html">
                                                            2016-12-09 11:07                        </a>
                    </div>
                </div>
                            </div>
            <div class="content-info row">
                <p class='word-wrap'>我创建了一个新的群组【ceshi】：<a class="label label-badge badge-label" href="http://hugo.cn/testsns/index.php?s=/group/index/group/id/9.html" target="_blank"><i class="icon-link" title="http://hugo.cn/testsns/index.php?s=/group/index/group/id/9.html"></i></a></p>                 <div class="form-where">
                    <div class="where w-left">
                        <span>来自 网站端 </span>
                        <span></span>
                    </div>
                    <div class="where w-right  bottom-operate" data-weibo-id="48">
                                                <div class="col-xs-3" style="min-height: 1px"></div>
<div class="col-xs-3 operate-color">
     <a title="喜欢"
     class="support_btn" table="weibo" row="48" uid="100" jump="weibo/index/weibodetail">

            <i id="ico_like" class="icon-heart-empty"></i>

    <span id="support_Weibo_weibo_48_pos"><span id="support_Weibo_weibo_48">0</span> </span>
</a>
<script>
    bind_support();
</script></div>
<div class=" col-xs-3 operate-color" data-role="weibo_comment_btn"  data-weibo-id="48">
   <i class="os-icon-bubbles"></i> 0</div>
<div class="col-xs-3 operate-color">
        <a title="转发"  data-role="send_repost"  href="/testsns/index.php?s=/weibo/index/sendrepost/sourceId/48/weiboId/48.html"><i class="os-icon-share-alt"></i> 0</a>
</div>
                    </div>
                </div>
            </div>
        </div>

        </div>
    <div class="weibo-comment-list row" style="display: block;margin:0;"  data-weibo-id="48">
    <div class=" weibo-comment-block">
            <div class="weibo-comment-container">
                <div class="weibo_post_box">
            <p class="comment-area">
                <input type="hidden"  name="reply_id" value="0"/>

                <input type="text" placeholder="评论（Ctrl+Enter）" id="text_48" rows="2" data-weibo-id="48"
                       class="comment-input  weibo-comment-content comment_text_inputor">

                <a style="margin-right: 10px" href="javascript:" class="" onclick="insertFace($(this))"><i style="color: #999;" class="os-icon-emoticon-smile"></i> </a>
                <a  data-role="do_comment" id="btn_48" data-weibo-id="48">
                    <i class="os-icon-paper-plane"></i> </a>
            </p>

    <div id="emot_content" class="emot_content" style="position: absolute;    right: 425px;
    top: 45px;"></div>
    <!--评论列表-->
</div>
<div id="show_comment_48" class="weibo_comment_list" data-comment-count="0">
    <div class="pager" style="display: none!important;">
    </div>
</div>


<script>
    $(function () {
        var weiboid = '48';
        $('#text_' + weiboid + '').keypress(function (e) {
            if (e.ctrlKey && e.which == 13 || e.which == 10) {
                $('#btn_' + weiboid + '').click();
            }
        });
    });
</script>            </div>
        </div>    </div>
    </div>
</if>
<style>
    .suofang {MARGIN: auto;WIDTH: 200px;}
    .suofang img{MAX-WIDTH: 100%!important;HEIGHT: auto!important;width:expression(this.width > 300 ? "300px" :this.width)!important;}
</style>
<script>
    $(function(){
        $('[data-role="id_weibo"]').mouseover(function(){
            var id = $(this).attr('id');
            $(this).find(".follow-btn").css("display", "inline-block");
        })
        $('[data-role="id_weibo"]').mouseout(function(){
            $(".follow-btn").css("display", "none");
        })
    });
    // alert($('.weibo-comment-container').text());
</script>