<?php if (!defined('THINK_PATH')) exit();?><div class="weibo_post_box">
            <p class="comment-area">
                <input type="hidden"  name="reply_id" value="0"/>

                <input type="text" placeholder="评论（Ctrl+Enter）" id="text_<?php echo ($weiboId); ?>" rows="2" data-weibo-id="<?php echo ($weiboId); ?>"
                       class="comment-input  weibo-comment-content comment_text_inputor">

                <a style="margin-right: 10px" href="javascript:" class="" onclick="insertFace($(this))"><i style="color: #999;" class="os-icon-emoticon-smile"></i> </a>
                <a  data-role="do_comment" id="btn_<?php echo ($weiboId); ?>" data-weibo-id="<?php echo ($weiboId); ?>">
                    <i class="os-icon-paper-plane"></i> </a>
            </p>

    <div id="emot_content" class="emot_content" style="position: absolute;    right: 425px;
    top: 45px;"></div>
    <!--评论列表-->
</div>
<div id="show_comment_<?php echo ($weiboId); ?>" class="weibo_comment_list" data-comment-count="<?php echo ($weiboCommentTotalCount); ?>">
    <?php if(is_array($comments)): $i = 0; $__LIST__ = $comments;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$comment): $mod = ($i % 2 );++$i;?><div <?php if($i>5){ ?> style="display: none"  <?php } ?> >
        <?php echo W('Weibo/Comment/detail',array('comment_id'=>$comment['id'],'un_prase_comment'=>$un_prase_comment));?>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
<?php $pageCount = ceil($weiboCommentTotalCount / 10); ?>
<div class="pager" style="display: none!important;">
    <?php echo getPageHtml('weibo_page',$pageCount,array('weibo_id'=>$weiboId),$page);?>
</div>
</div>
<?php if(count($comments)>5){ ?>
<div style="width: 100%;height: 40px;text-align: center;line-height: 40px;">
    <a id="show_all_comment_<?php echo ($weiboId); ?>" href="javascript:" onclick="show_comment('<?php echo ($weiboId); ?>');"><?php echo L('_REPLY_VIEW_MORE_'); echo L('_GREATER_'); echo L('_GREATER_');?></a>
</div>
<?php } ?>


<script>
    $(function () {
        var weiboid = '<?php echo ($weiboId); ?>';
        $('#text_' + weiboid + '').keypress(function (e) {
            if (e.ctrlKey && e.which == 13 || e.which == 10) {
                $('#btn_' + weiboid + '').click();
            }
        });
    });
</script>