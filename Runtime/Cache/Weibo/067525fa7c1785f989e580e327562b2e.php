<?php if (!defined('THINK_PATH')) exit();?><div style="max-width: 680px;margin-bottom: 10px;<?php if($top_hide): ?>display:none<?php endif; ?>" data-role="id_weibo" id="weibo_<?php echo ($weibo["id"]); ?>" <?php if($can_hide): ?>class="top_can_hide"<?php else: ?>class=""<?php endif; ?> >
    <div class="all-wrap">
        <?php if($weibo['is_hot'] == 1): ?><div class="hot-comment-weibo"></div>
            <?php elseif($weibo['is_first'] == 1): ?>
            <div class="new-user-first-weibo"></div><?php endif; ?>
        <div class="weibo-content">
            <div class="content-head">
                <div class="avat-box pull-left">
                    <a href="<?php echo ($weibo["user"]["space_url"]); ?>" ucard="<?php echo ($weibo["user"]["uid"]); ?>">
                        <img src="<?php echo ($weibo["user"]["avatar128"]); ?>" class="avatar-img"/>
                    </a>
                    <div class="show-follow pull-right">
                        <?php if($can_hide && !$sign): ?><div class="follow-btn" style="display: none;">
                                <?php echo W('Common/Follow/follow',array('follow_who'=>$weibo['uid']));?>
                            </div><?php endif; ?>
                        <?php if(!$can_hide): ?><div class="follow-btn" style="display: none;">
                                [follow:<?php echo ($weibo['uid']); ?>]
                            </div><?php endif; ?>
                    </div>
                </div>
                <div class="op-box pull-right">
                    <div class="op-tb op-top">
                        <a ucard="<?php echo ($weibo["user"]["uid"]); ?>" href="<?php echo ($weibo["user"]["space_url"]); ?>" class="user_name">
                            <?php if($can_hide): echo ($weibo["user"]["nickname"]); ?>
                                <?php else: ?>
                                [nickname:<?php echo ($weibo['uid']); ?>]<?php endif; ?>
                        </a>
                        <?php if(modC('SHOW_TITLE',1)): ?><small class="font_grey"><?php echo ($weibo["user"]["title"]); ?></small><?php endif; ?>
                        <?php echo W('Common/UserRank/render',array($weibo['uid']));?>
                        <!--隐藏操作列表-->
                        <div class="pull-right show-operate-wrap">
                            <a href="javascript:" class="show-operate pull-right icon-angle-down"></a>
                            <div class="operate-box" >
                                <?php if(check_auth('Weibo/Index/setTop')): if(($weibo["is_top"]) == "0"): ?><li data-weibo-id="<?php echo ($weibo["id"]); ?>" title="<?php echo L('_SET_TOP_');?>" data-role="weibo_set_top">
                                            置顶
                                        </li>
                                        <?php else: ?>
                                        <li data-weibo-id="<?php echo ($weibo["id"]); ?>" title="<?php echo L('_CANCEL_TOP_');?>" data-role="weibo_set_top">
                                            取消置顶
                                        </li><?php endif; endif; ?>
                                <?php if($weibo['can_delete']): ?><li data-weibo-id="<?php echo ($weibo["id"]); ?>" title="<?php echo L('_DELETE_');?>" data-role="del_weibo">
                                        删除
                                    </li><?php endif; ?>
                                <?php if($can_hide): ?><li data-weibo-id="<?php echo ($weibo["id"]); ?>" title="<?php echo L('_HIDE_TOP_');?>" data-role="hide_top_weibo">
                                        隐藏
                                    </li><?php endif; ?>
                                <li><?php echo hook('report',array('type'=>$MODULE_ALIAS.'/'.$MODULE_ALIAS,'url'=>"Weibo/Index/weiboDetail?id=$weibo[id]",'data'=>array('weibo-id'=>$weibo['id'])));?></li>
                            </div>
                        </div>
                    </div>
                    <div class="op-tb op-bottom">
                        <a data-hover="查看详情" class="wb-time" href="<?php echo U('Weibo/Index/weiboDetail',array('id'=>$weibo['id']));?>">
                            <?php if($can_hide): echo (friendlydate($weibo["create_time"])); ?>
                                <?php else: ?>
                                [time:<?php echo ($weibo["create_time"]); ?>]<?php endif; ?>
                        </a>
                    </div>
                </div>
                <?php if(($weibo["is_top"]) == "1"): ?><div class="ribbion-green"></div><?php endif; ?>
            </div>
            <div class="content-info row">
                <?php echo ($weibo["fetchContent"]); ?>
                <div class="form-where">
                    <div class="where w-left">
                        <span><?php echo L('_FROM_');?> <?php if($weibo['from'] == ''): echo L('_PC_');?> <?php else: ?><strong><?php echo ($weibo["from"]); ?></strong><?php endif; ?></span>
                        <span><?php echo hook('giveReward',array('type'=>$MODULE_ALIAS.'/'.$MODULE_ALIAS,'url'=>"Weibo/Index/weiboDetail?id=$weibo[id]",'data'=>array('user-id'=>$weibo['user']['uid'])));?></span>
                    </div>
                    <div class="where w-right  bottom-operate" data-weibo-id="<?php echo ($weibo["id"]); ?>">
                        <?php $weiboCommentTotalCount = $weibo['comment_count']; ?>
                        <div class="col-xs-3" style="min-height: 1px"></div>
<div class="col-xs-3 operate-color">
    <?php echo Hook('support',array('table'=>'weibo','row'=>$weibo['id'],'app'=>'Weibo','uid'=>$weibo['uid'],'jump'=>'weibo/index/weibodetail'));?>
</div>
<div class=" col-xs-3 operate-color" data-role="weibo_comment_btn"  data-weibo-id="<?php echo ($weibo["id"]); ?>">
   <i class="os-icon-bubbles"></i> <?php echo ($weiboCommentTotalCount); ?>
</div>
<div class="col-xs-3 operate-color">
    <?php $sourceId =$weibo['data']['sourceId']?$weibo['data']['sourceId']:$weibo['id']; ?>
    <a title="<?php echo L('_REPOST_');?>"  data-role="send_repost"  href="<?php echo U('Weibo/Index/sendrepost',array('sourceId'=>$sourceId,'weiboId'=>$weibo['id']));?>"><i class="os-icon-share-alt"></i> <?php echo ($weibo["repost_count"]); ?></a>
</div>
                    </div>
                </div>
            </div>
        </div>

        </div>
    <div class="weibo-comment-list row" <?php if(modC('SHOW_COMMENT',1)): ?>style="display: block;margin:0;" <?php else: ?> style="display: none;"<?php endif; ?> data-weibo-id="<?php echo ($weibo["id"]); ?>">
    <?php if(modC('SHOW_COMMENT',1)): ?><div class=" weibo-comment-block">
            <div class="weibo-comment-container">
                <?php echo W('Weibo/Comment/someComment',array('weibo_id'=>$weibo['id'],'un_prase_comment'=>$un_prase_comment));?>
            </div>
        </div><?php endif; ?>
    </div>
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