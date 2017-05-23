<?php if (!defined('THINK_PATH')) exit();?><link type="text/css" rel="stylesheet" href="/testsns/Public/zui/lib/calendar/zui.calendar.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo getRootUrl();?>Addons/CheckIn/Static/css/check.css">

<div class="checkdiv">
    <div class="row">
        <div class="col-xs-4 text-center">
            <?php if(!$check){ ?>
            <a href="javascript:void(0)" data-role="do_checkin" class="btn-sign show-check"><?php echo L('_CHECKIN_');?></a>
            <?php }else{ ?>
            <a href="javascript:void(0)" class="btn-sign show-check">已签</a>
            <?php } ?>
        </div>

        <div class="col-xs-8 time-box">
            <span><?php echo ($day); ?></span>
            <span class="pull-right"><?php echo ($week); ?></span>
            <p>
                <span class="pull-left">累签 <span data-role="total_check"><?php echo ($user_info["total_check"]); ?></span> 天</span>
                <span class="pull-right">连签 <span data-role="con_check"><?php echo ($user_info["con_check"]); ?></span> 天</span>
            </p>
        </div>
    </div>
</div>
<div id="sign-calendar" class="calendar sign-calendar"></div>
<script type="text/javascript" src="/testsns/Public/zui/lib/calendar/zui.calendar.js"></script>
<script>
    $(function () {
        do_checkin();
        check_in_calender();
    })
    var do_checkin = function () {
        $('[data-role="do_checkin"]').unbind();
        $('[data-role="do_checkin"]').click(function () {
            var $this = $(this);
            $.post("<?php echo addons_url('CheckIn://CheckIn/doCheckIn');?>", {}, function (res) {
                if (res.status) {
                    $this.replaceWith('<a href="javascript:void(0)" class="btn-sign show-check">已签</a>');
                    $('#sign-calendar .month-days .week-days .cell-day.current').addClass('signed');
                    $('[data-role=total_check]').text(res.total_check);
                    $('[data-role=con_check]').text(res.con_check);
                    toast.success(res.info);
                } else {
                    handleAjax(res);
                }
            });
        })
    }
    var check_in_calender=function(){
        $('#sign-calendar').calendar({
            display:function(event){
                $('#sign-calendar .month-days .cell-day.signed').removeClass('signed');
                $.post("<?php echo addons_url('CheckIn://CheckIn/signList');?>",{show_month:parseInt(Date.parse(event.date))/1000},function(list){
                    if(list){
                        for(var i in list){
                            $('#sign-calendar .month-days .cell-day').eq(list[i]).addClass('signed');
                        }
                    }
                });
            }
        });
    }
</script>