<?php if (!defined('THINK_PATH')) exit();?><a title="喜欢"
<?php if($supported): endif; ?> data-role="support_btn" table="<?php echo ($table); ?>" row="<?php echo ($row); ?>" uid="<?php echo ($uid); ?>" jump="<?php echo ($jump); ?>" weibo_id="<?php echo ($weibo_id); ?>">

<?php if($supported): ?><i id="ico_like1" class="icon-thumbs-up"></i>
    <?php else: ?>
    <i id="ico_like1" class="icon-thumbs-o-up"></i><?php endif; ?>


<span id="support_<?php echo ($app); ?>_<?php echo ($table); ?>_<?php echo ($row); ?>_pos"><span id="support_<?php echo ($app); ?>_<?php echo ($table); ?>_<?php echo ($row); ?>"><?php echo ($count); ?></span> </span>
</a>