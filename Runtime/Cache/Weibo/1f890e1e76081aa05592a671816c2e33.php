<?php if (!defined('THINK_PATH')) exit();?>
<div id="comment_<?php echo ($comment["id"]); ?>" class="row weibo_comment" data-weibo-id="<?php echo ($comment["weibo_id"]); ?>"
     data-comment-id="<?php echo ($comment["id"]); ?>">
    <div class="clearfix">
        <div class="col-xs-1" style="width: 8%">
            <div class="" style="overflow: hidden;  padding-top: 5px;">
                <a href="<?php echo ($comment["user"]["space_url"]); ?>" ucard="<?php echo ($comment["user"]["uid"]); ?>">
                    <img src="<?php echo ($comment["user"]["avatar64"]); ?>" class="avatar-img"/></a>
            </div>
        </div>
        <div class="col-xs-11  comment-content" style="width: 92%;padding-left: 5px;padding-right: 0">
            <div> <a href="<?php echo ($comment["user"]["space_url"]); ?>"
                     ucard="<?php echo ($comment["user"]["uid"]); ?>">[nickname:<?php echo ($comment["user"]["uid"]); ?>]</a>：<span class="weibo-comment text-muted"><?php echo ($comment["content"]); ?></span></div>

           <div class="clearfix text-muted">
               <div class="pull-left ctime">
                   [time:<?php echo ($comment["create_time"]); ?>]
               </div>
               <div class="pull-right operate-buttons text-muted">&nbsp;&nbsp;&nbsp;
                   <?php echo W('Weibo/CommentSupport/support', array(array('table'=>'weibo_comment','row'=>$comment['id'],'weibo_id'=>$comment['weibo_id'],'app'=>'Weibo','uid'=>$comment['uid'],'jump'=>'weibo/index/weibodetail')));?>
                   <?php if($comment['can_delete']): ?>&nbsp;<a href="javascript:" data-role="comment_del"><?php echo L('_DELETE_');?></a><?php endif; ?>
                   <?php echo hook('report',array('type'=>$MODULE_ALIAS.'/'.L('_COMMENTS_'),'url'=>"Weibo/Index/weiboDetail?id=$comment[weibo_id]#comment_$comment[id]",'data'=>array('comment_id'=>$comment['id'],'weibo-id'=>$weibo['id'])));?>
                   &nbsp;<a href="javascript:" data-role="weibo_reply" data-user-nickname="<?php echo ($comment["user"]["real_nickname"]); ?>"><?php echo L('_REPLY_');?></a>

               </div>
           </div>
        </div>

    </div>


</div>