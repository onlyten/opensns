<?php if (!defined('THINK_PATH')) exit();?><div style="max-width: 680px;margin-bottom: 10px;" data-role="id_weibo" id="weibo_80" class="" >
    <div class="all-wrap">
                <div class="weibo-content">
            <div class="content-head">
                <div class="avat-box pull-left">
                    <a href="/testsns/index.php?s=/ucenter/index/index/uid/101.html" ucard="101">
                        <img src="/testsns/Public/images/default_avatar_128_128.jpg" class="avatar-img"/>
                    </a>
                    <div class="show-follow pull-right">
                                                <div class="follow-btn" style="display: none;">
                                
                            </div>                    </div>
                </div>
                <div class="op-box pull-right">
                    <div class="op-tb op-top">
                        <a ucard="101" href="/testsns/index.php?s=/ucenter/index/index/uid/101.html" class="user_name">
                                                            test                        </a>
                        <small class="font_grey">Lv1 实习</small>                        &nbsp;
                        <!--隐藏操作列表-->
                        <div class="pull-right show-operate-wrap">
                            <a href="javascript:" class="show-operate pull-right icon-angle-down"></a>
                            <div class="operate-box" >
                                                                                                                                <li><a data-type="ajax" data-url="/testsns/index.php?s=/home/addons/execute/_addons/report/_controller/report/_action/eject/param/type%3D%25E5%258A%25A8%25E6%2580%2581%252F%25E5%258A%25A8%25E6%2580%2581%26url%3DWeibo%252FIndex%252FweiboDetail%253Fid%253D80%26data%255Bweibo-id%255D%3D80.html" data-title="举报"  data-toggle="modal" >举报</a></li>
                            </div>
                        </div>
                    </div>
                    <div class="op-tb op-bottom">
                        <a data-hover="查看详情" class="wb-time" href="/testsns/index.php?s=/weibo/index/weibodetail/id/80.html">
                                                            2016-12-29 11:35                        </a>
                    </div>
                </div>
                            </div>
            <div class="content-info row">
                <p class='word-wrap'>接口发表</p>                 <div class="form-where">
                    <div class="where w-left">
                        <span>来自 <strong>接口</strong></span>
                        <span></span>
                    </div>
                    <div class="where w-right  bottom-operate" data-weibo-id="80">
                                                <div class="col-xs-3" style="min-height: 1px"></div>
<div class="col-xs-3 operate-color">
     <a title="喜欢"
     class="support_btn" table="weibo" row="80" uid="101" jump="weibo/index/weibodetail">

            <i id="ico_like" class="icon-heart-empty"></i>

    <span id="support_Weibo_weibo_80_pos"><span id="support_Weibo_weibo_80">0</span> </span>
</a>
<script>
    bind_support();
</script></div>
<div class=" col-xs-3 operate-color" data-role="weibo_comment_btn"  data-weibo-id="80">
   <i class="os-icon-bubbles"></i> 0</div>
<div class="col-xs-3 operate-color">
        <a title="转发"  data-role="send_repost"  href="/testsns/index.php?s=/weibo/index/sendrepost/sourceId/80/weiboId/80.html"><i class="os-icon-share-alt"></i> 0</a>
</div>
                    </div>
                </div>
            </div>
        </div>

        </div>
    <div class="weibo-comment-list row" style="display: block;margin:0;"  data-weibo-id="80">
    <div class=" weibo-comment-block">
            <div class="weibo-comment-container">
                <div class="weibo_post_box">
            <p class="comment-area">
                <input type="hidden"  name="reply_id" value="0"/>

                <input type="text" placeholder="评论（Ctrl+Enter）" id="text_80" rows="2" data-weibo-id="80"
                       class="comment-input  weibo-comment-content comment_text_inputor">

                <a style="margin-right: 10px" href="javascript:" class="" onclick="insertFace($(this))"><i style="color: #999;" class="os-icon-emoticon-smile"></i> </a>
                <a  data-role="do_comment" id="btn_80" data-weibo-id="80">
                    <i class="os-icon-paper-plane"></i> </a>
            </p>

    <div id="emot_content" class="emot_content" style="position: absolute;    right: 425px;
    top: 45px;"></div>
    <!--评论列表-->
</div>
<div id="show_comment_80" class="weibo_comment_list" data-comment-count="0">
    <div class="pager" style="display: none!important;">
    </div>
</div>


<script>
    $(function () {
        var weiboid = '80';
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