<?php


namespace Event\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function _initialize()
    {
        $tree = D('EventType')->where(array('status' => 1))->select();
        $this->assign('tree', $tree);

        $sub_menu =
            array(
                'left' =>
                    array(
                        array('tab' => 'home', 'title' => L('_INDEX_'), 'href' => U('event/index/index')),
                        array('tab' => 'myevent', 'title' => L('_MY_EVENT_'), 'href' => U('event/index/myevent')),
                    ),
            );
        $this->assign('sub_menu', $sub_menu);
        $this->assign('current', 'home');
    }

    // public function GrabImage($url,$filename,$uid) {
    //    if($url==""):return false;endif;

    //    if($filename=="") {
    //      $ext=strrchr($url,".");
    //      if($ext!=".gif" && $ext!=".jpg"):return false;endif;
    //      $filename=date("Ymdhis").$ext;
    //    }

    //    ob_start();
    //    readfile($url);
    //    $img = ob_get_contents();
    //    ob_end_clean();
    //    $size = strlen($img);

    //    $fp2=@fopen($filename, "a");
    //    fwrite($fp2,$img);
    //    fclose($fp2);

    //    $file="./".$filename; //旧目录
    //   $newFile="./Uploads/Avatar/".$uid."/new.jpg"; //新目录
    //   copy($file,$newFile); //拷贝到新目录
    //   //unlink($file);

    //    return $filename;
    // }

    /**
     * 活动首页
     * @param int $page
     * @param int $type_id
     * @param string $norh
     * autor:xjw129xjt
     */
    public function index($page = 1, $type_id = 0, $norh = 'new')
    {
        if(I("get.")){//拿到get参数
            $arr = I("get.");
            $all_key = array_keys($arr);//所有的key值
            //echo $all_key[1];
            $str = "sns!@#$%".$all_key[0];//需要加密的字符串
            $md_str = md5($str);
            if($md_str == $arr[$all_key[0]]){//判断密钥是否一致
                $username = $all_key[0];
                $map["nickname"] = $username;
                $num = M("member")->where($map)->count();
                if($num == 0){
                    $data["nickname"] = $username;   
                    $data["status"] = 1;
                    $data["last_login_role"] = 1;
                    $data["show_role"] = 1;
                    M("member")->add($data);

                    $dat["username"] = $username;
                    $dat["status"] = 1;
                    $dat["type"] = 2;
                    M("ucenter_member")->add($dat);
                }
                session('username',$username);
                $uid = M("member")->where($map)->getField("uid");
                //echo $uid;
                $get_url = I("get.picUrl");
                if(is_dir("./Uploads/Avatar/".$uid)){
                    $mapx["uid"] = $uid;
                    $url = M("avatar")->where($mapx)->getField("get_url");
                    if($url != $get_url){
                        $dada["get_url"] = $get_url;
                        M("avatar")->where($mapx)->save($dada);
                        GrabImage($get_url,$pic_name,$uid);
                    }
                }else{
                    mkdir("./Uploads/Avatar/".$uid);
                    $dats["uid"] = $uid;
                    $dats["path"] = "/".$uid."/new.jpg";
                    $dats["driver"] = "local";
                    $dats["create_time"] = time();
                    $dats["status"] = 1;
                    $dats["is_temp"] = 0;
                    $dats["get_url"] = $get_url;
                    M("avatar")->add($dats);
                    $pic_name = $uid.".jpg";
                    GrabImage($get_url,$pic_name,$uid);
                }
            } 
        }
        $type_id = intval($type_id);
        if ($type_id != 0) {
            $map['type_id'] = $type_id;
        }
        $map['status'] = 1;
        /****zouj add start****/
            $username = session("username");
            $mapaa["nickname"] = $username;
            $school_id = M("member")->where($mapaa)->getField("school_id");
            $user_type = M("member")->where($mapaa)->getField("type");
            $this->assign("user_type",$user_type);
            $map['school_id'] = $school_id;
        /****zouj add end****/
        $order = 'create_time desc';
        $norh == 'hot' && $order = 'signCount desc';
        $content = D('Event')->where($map)->order($order)->page($page, 10)->select();

        $totalCount = D('Event')->where($map)->count();
        foreach ($content as &$v) {
            $v['user'] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar128', 'rank_html'), $v['uid']);
            $v['type'] = $this->getType($v['type_id']);
            $v['check_isSign'] = D('event_attend')->where(array('uid' => is_login(), 'event_id' => $v['id']))->select();
        }
        unset($v);
        $this->assign('type_id', $type_id);
        $this->assign('contents', $content);
        $this->assign('norh', $norh);
        $this->assign('totalPageCount', $totalCount);
        $this->getRecommend();
        $this->setTitle(L('_EVENT_HOME_PAGE_'));
        $this->setKeywords(L('_EVENT_'));
        $this->display();
    }

    /**
     * 获取推荐活动数据
     * autor:xjw129xjt
     */
    public function getRecommend()
    {
        $rec_event = D('Event')->where(array('is_recommend' => 1))->limit(2)->order('rand()')->select();
        foreach ($rec_event as &$v) {
            $v['user'] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar128', 'rank_html'), $v['uid']);
            $v['type'] = $this->getType($v['type_id']);
            $v['check_isSign'] = D('event_attend')->where(array('uid' => is_login(), 'event_id' => $v['id']))->select();
        }
        unset($v);

        $this->assign('rec_event', $rec_event);
    }

    /**
     * 我的活动页面
     * @param int $page
     * @param int $type_id
     * @param string $norh
     * autor:xjw129xjt
     */
    public function myevent($page = 1, $type_id = 0, $lora = '')
    {

        $type_id = intval($type_id);
        if ($type_id != 0) {
            $map['type_id'] = $type_id;
        }

        $map['status'] = 1;
        $order = 'create_time desc';
        if ($lora == 'attend') {
            $attend = D('event_attend')->where(array('uid' => is_login()))->select();
            $enentids = getSubByKey($attend, 'event_id');
            $map['id'] = array('in', $enentids);
        } else {
            $map['uid'] = is_login();
        }
        $content = D('Event')->where($map)->order($order)->page($page, 10)->select();

        $totalCount = D('Event')->where($map)->count();
        foreach ($content as &$v) {
            $v['user'] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar128', 'rank_html'), $v['uid']);
            $v['type'] = $this->getType($v['type_id']);

            $v['check_isSign'] = D('event_attend')->where(array('uid' => is_login(), 'event_id' => $v['id']))->select();
        }
        unset($v);
        $this->assign('type_id', $type_id);
        $this->assign('contents', $content);
        $this->assign('lora', $lora);
        $this->assign('totalPageCount', $totalCount);
        $this->getRecommend();
        $this->setTitle(L('_MY_EVENT_').L('_DASH_').L('_MODULE_'));
        $this->assign('current', 'myevent');
        $this->display();
    }

    /**
     * 获取活动类型
     * @param $type_id
     * @return mixed
     * autor:xjw129xjt
     */
    private function getType($type_id)
    {
        $type = D('EventType')->where('id=' . $type_id)->find();
        return $type;
    }

    /**
     * 发布活动
     * @param int $id
     * @param int $cover_id
     * @param string $title
     * @param string $explain
     * @param string $sTime
     * @param string $eTime
     * @param string $address
     * @param int $limitCount
     * @param string $deadline
     * autor:xjw129xjt
     */
    public function doPost($id = 0, $cover_id = 0, $title = '', $explain = '', $sTime = '', $eTime = '', $address = '', $limitCount = 0, $deadline = '',$type = '', $type_id = 0)
    {
        if (!is_login()) {
            $this->error(L('_ERROR_LOGIN_'));
        }
        if (!$cover_id) {
            $this->error(L('_ERROR_COVER_'));
        }
        if (trim(op_t($title)) == '') {
            $this->error(L('_ERROR_TITLE_'));
        }
        if ($type_id == 0) {
            $this->error(L('_ERROR_CATEGORY_'));
        }
        if (trim(op_h($explain)) == '') {
            $this->error(L('_ERROR_CONTENT_'));
        }
        if (trim(op_h($address)) == '') {
            $this->error(L('_ERROR_SITE_'));
        }
        if ($eTime < $deadline) {
            $this->error(L('_ERROR_TIME_DEADLINE_'));
        }
        if ($deadline == '') {
            $this->error(L('_ERROR_DEADLINE_'));
        }
        if ($sTime > $eTime) {
            $this->error(L('_ERROR_TIME_START_'));
        }
        $content = D('Event')->create();
        $content['explain'] = filter_content($content['explain']);
        $content['title'] = op_t($content['title']);
        $content['sTime'] = strtotime($content['sTime']);
        $content['eTime'] = strtotime($content['eTime']);
        $content['deadline'] = strtotime($content['deadline']);
        $content['type_id'] = intval($type_id);
        if ($id) {
            $content_temp = D('Event')->find($id);
            $this->checkAuth('Event/Index/edit', $content_temp['uid'], L('_INFO_EVENT_EDIT_LIMIT_'));
            $this->checkActionLimit('add_event', 'event', $id, is_login(), true);
            $content['uid'] = $content_temp['uid']; //权限矫正，防止被改为管理员
            $rs = D('Event')->save($content);
            if (D('Common/Module')->isInstalled('Weibo')) { //安装了微博模块
                $postUrl = "http://$_SERVER[HTTP_HOST]" . U('Event/Index/detail', array('id' => $id));
                $weiboModel = D('Weibo/Weibo');
                $weiboModel->addWeibo(L('_EVENT_CHANGED_')."【" . $title . "】：" . $postUrl);
            }
            if ($rs) {
                action_log('add_event', 'event', $id, is_login());
                $this->success(L('_SUCCESS_DELETE_').L('_EXCLAMATION_'), U('detail', array('id' => $content['id'])));
            } else {
                $this->error(L('_ERROR_OPERATION_FAIL_').L('_EXCLAMATION_'),'');
            }
        } else {
            //$this->checkAuth('Event/Index/add', -1, L('_EVENT_PRIORITY_START_NOT_').L('_EXCLAMATION_')); zouj add
            $this->checkActionLimit('add_event', 'event', 0, is_login(), true);
            if (modC('NEED_VERIFY', 0) && !is_administrator()) //需要审核且不是管理员
            {
                $content['status'] = 2;
                $tip = L('_PLEASE_WAIT_').L('_PERIOD_');
                $user = query_user(array('username', 'nickname'), is_login());
                D('Common/Message')->sendMessage(explode(',', C('USER_ADMINISTRATOR')), $title = L('_EVENT_SPONSOR_1_'), "{$user['nickname']}".L('_EVENT_SPONSOR_2_'), 'Admin/Event/verify', array(), is_login(), 2);
            }

            $content['attentionCount'] = 1;
            $content['signCount'] = 1;
            /****zouj add start****/
            $username = session("username");
            $mapaa["nickname"] = $username;
            $school_id = M("member")->where($mapaa)->getField("school_id");
            $content['school_id'] = $school_id;
            if (I("post.teac") && I("post.stu")) {
                $content['person_type'] = 2;
            } else if(I("post.teac")){
                $content['person_type'] = 1;
            } else {
                $content['person_type'] = 0;
            }
            /****zouj add end****/
            $rs = D('Event')->add($content);


            $data['uid'] = is_login();
            $data['event_id'] = $rs;
            $data['create_time'] = time();
            $data['status'] = 1;
            D('event_attend')->add($data);


            if (D('Common/Module')->isInstalled('Weibo')) { //安装了微博模块
                //同步到微博
                $postUrl = "http://$_SERVER[HTTP_HOST]" . U('Event/Index/detail', array('id' => $rs));

                $weiboModel = D('Weibo/Weibo');
                $weiboModel->addWeibo(L('_EVENT_I_SPONSOR_')."【" . $title . "】：" . $postUrl);
            }

            if ($rs) {
                action_log('add_event', 'event', $rs, is_login());
                $this->success(L('_SUCCESS_POST_').L('_EXCLAMATION_'). $tip, U('index'));
            } else {
                $this->error(L('_ERROR_OPERATION_FAIL_').L('_EXCLAMATION_'));
            }

        }
    }

    /**
     * 活动详情
     * @param int $id
     * autor:xjw129xjt
     */
    public function detail($id = 0)
    {

        $check_isSign = D('event_attend')->where(array('uid' => is_login(), 'event_id' => $id))->select();

        $this->assign('check_isSign', $check_isSign);

        $event_content = D('Event')->where(array('id' => $id))->find();

        if (!$event_content ) {
            $this->error(L('_NOT_FOUND_'));
        }else{
            if((!check_auth('Admin/Event/verify',array($event_content['uid']))) && $event_content['status']==2){
                $this->error(L('_NOT_AUTH_'));
            }
        }
        D('Event')->where(array('id' => $id))->setInc('view_count');

        $event_content['user'] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar64', 'rank_html', 'signature'), $event_content['uid']);
        $event_content['type'] = $this->getType($event_content['type_id']);


        $menber = D('event_attend')->where(array('event_id' => $id, 'status' => 1))->select();
        foreach ($menber as $k => $v) {
            $event_content['member'][$k] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar64', 'rank_html', 'signature'), $v['uid']);

        }

        $this->assign('content', $event_content);
        $this->setTitle('{$content.title|op_t}' . L('_DASH_').L('_MODULE_'));
        $this->setKeywords('{$content.title|op_t}' . L('_COMMA_').L('_MODULE_'));
        $this->getRecommend();
        $this->display();
    }

    /**
     * 活动成员
     * @param int $id
     * @param string $tip
     * autor:xjw129xjt
     */
    public function member($id = 0, $tip = 'all')
    {
        if ($tip == 'sign') {
            $map['status'] = 0;
        }
        if ($tip == 'attend') {
            $map['status'] = 1;
        }
        $check_isSign = D('event_attend')->where(array('uid' => is_login(), 'event_id' => $id))->select();
        $this->assign('check_isSign', $check_isSign);

        $event_content = D('Event')->where(array('status' => 1, 'id' => $id))->find();
        if (!$event_content) {
            $this->error('404 not found');
        }
        $map['event_id'] = $id;
        $event_content['user'] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar64', 'rank_html', 'signature'), $event_content['uid']);
        $menber = D('event_attend')->where($map)->select();
        foreach ($menber as $k => $v) {
            $event_content['member'][$k] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar64', 'avatar128', 'rank_html', 'signature'), $v['uid']);
            $event_content['member'][$k]['name'] = $v['name'];
            $event_content['member'][$k]['phone'] = $v['phone'];
            $event_content['member'][$k]['status'] = $v['status'];
        }

        $this->assign('all_count', D('event_attend')->where(array('event_id' => $id))->count());
        $this->assign('sign_count', D('event_attend')->where(array('event_id' => $id, 'status' => 0))->count());
        $this->assign('attend_count', D('event_attend')->where(array('event_id' => $id, 'status' => 1))->count());

        $this->assign('content', $event_content);
        $this->assign('tip', $tip);
        $this->setTitle('{$content.title|op_t}' . '——活动');
        $this->setKeywords('{$content.title|op_t}' . ',活动');
        $this->display();
    }

    /**
     * 编辑活动
     * @param $id
     * autor:xjw129xjt
     */
    public function edit($id)
    {
        $event_content = D('Event')->where(array('status' => 1, 'id' => $id))->find();
        if (!$event_content) {
            $this->error('404 not found');
        }
        $this->checkAuth('Event/Index/edit', $event_content['uid'], L('_INFO_EVENT_EDIT_LIMIT_').L('_EXCLAMATION_'));
        $event_content['user'] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar64', 'rank_html', 'signature'), $event_content['uid']);
        $this->assign('content', $event_content);
        $this->setTitle(L('') . L('_DASH_').L('_MODULE_'));
        $this->setKeywords(L('_EDIT_') . L('_COMMA_').L('_MODULE_'));
        $this->display();
    }

    public function add()
    {
        /****zouj add start**只有老师才能发起活动**/
        $map['type'] = 1;
        $map['uid'] = is_login();
        $num = M("member")->where($map)->count();
        if(!$num){
            $this->error("您没有发起活动的权限！");
        }
        /****zouj add end****/
//        $this->checkAuth('Event/Index/add', -1, L('_EVENT_PRIORITY_START_NOT_').L('_PERIOD_'));  zouj add
        $this->setTitle(L('_EVENT_ADD_') . L('_DASH_').L('_MODULE_'));
        $this->setKeywords(L('_MODULE_') . L('_COMMA_').L('_MODULE_'));
        $this->display();
    }

    /**
     * 报名参加活动
     * @param $event_id
     * @param $name
     * @param $phone
     * autor:xjw129xjt
     */
    public function doSign($event_id, $name, $phone)
    {
        if (!is_login()) {
            $this->error(L('_ERROR_REGISTER_AFTER_LOGIN_').L('_PERIOD_'));
        }
        if (!$event_id) {
            $this->error(L('_ERROR_PARAM_').L('_PERIOD_'));
        }
        if (trim(op_t($name)) == '') {
            $this->error(L('_ERROR_NAME_').L('_PERIOD_'));
        }
        if (trim($phone) == '') {
            $this->error(L('_ERROR_PHONE_').L('_PERIOD_'));
        }
        $check = D('event_attend')->where(array('uid' => is_login(), 'event_id' => $event_id))->select();
        $event_content = D('Event')->where(array('status' => 1, 'id' => $event_id))->find();
        //$this->checkAuth('Event/Index/doSign', $event_content['uid'], L('_INFO_LIMIT_').L('_EXCLAMATION_')); zouj add
        $this->checkActionLimit('event_do_sign', 'event', $event_id, is_login());
        if (!$event_content) {
            $this->error(L('_EVENT_NOT_EXIST_').L('_EXCLAMATION_'));
        }
        /*      if ($event_content['attentionCount'] + 1 > $event_content['limitCount']) {
                  $this->error('超过限制人数，报名失败');
              }*/
        if (time() > $event_content['deadline']) {
            $this->error(L('_REGISTRATION_HAS_OVER_'));
        }
        if (!$check) {
            $data['uid'] = is_login();
            $data['event_id'] = $event_id;
            $data['name'] = $name;
            $data['phone'] = $phone;
            $data['create_time'] = time();
            $res = D('event_attend')->add($data);
            if ($res) {
                D('Message')->sendMessageWithoutCheckSelf($event_content['uid'], L('_TOAST_SIGN_1_'), get_nickname(is_login()) . L('_TOAST_SIGN_2_') . $event_content['title'] . L('_TOAST_SIGN_3_'), 'Event/Index/member', array('id' => $event_id));

                D('Event')->where(array('id' => $event_id))->setInc('signCount');
                action_log('event_do_sign', 'event', $event_id, is_login());
                $this->success(L('_SUCCESS_SIGN_').L('_PERIOD_'), 'refresh');
            } else {
                $this->error(L('_FAIL_SIGN_').L('_PERIOD_'), '');
            }
        } else {
            $this->error(L('_SIGN_ED_').L('_PERIOD_'), '');
        }
    }

    /**
     * 审核
     * @param $uid
     * @param $event_id
     * @param $tip
     * autor:xjw129xjt
     */
    public function shenhe($uid, $event_id, $tip)
    {
        $event_content = D('Event')->where(array('status' => 1, 'id' => $event_id))->find();
        if (!$event_content || $event_content['deadline'] < time()) {
            $this->error(L('_LIMIT_YOU_AUDIT_NOT_').L('_EXCLAMATION_'));
        }
        $this->checkAuth('Event/Index/shenhe', $event_content['uid'], L('_EVENT_NOT_EXIST_OR_OVER_').L('_EXCLAMATION_'));
        $res = D('event_attend')->where(array('uid' => $uid, 'event_id' => $event_id))->setField('status', $tip);
        if ($tip) {
            if ($event_content['attentionCount'] + 1 == $event_content['limitCount']) {
                $data['deadline'] = time();
                $data['attentionCount'] = $event_content['limitCount'];
                D('Event')->where(array('id' => $event_id))->setField($data);
            } else {
                D('Event')->where(array('id' => $event_id))->setInc('attentionCount');
            }
            D('Message')->sendMessageWithoutCheckSelf($uid, L('_MESSAGE_AUDIT_APPLY_1_'), get_nickname( is_login()) . L('_MESSAGE_AUDIT_APPLY_2_') . $event_content['title'] . L('_MESSAGE_AUDIT_APPLY_3_'), 'Event/Index/detail', array('id' => $event_id));
        } else {
            D('Event')->where(array('id' => $event_id))->setDec('attentionCount');
            D('Message')->sendMessageWithoutCheckSelf($uid, L('_MESSAGE_AUDIT_CANCEL_1_'), get_nickname( is_login()) . L('_MESSAGE_AUDIT_CANCEL_2_') . $event_content['title'] . L('_MESSAGE_AUDIT_CANCEL_3_'), 'Event/Index/member', array('id' => $event_id));
        }
        if ($res) {
            $this->success(L('_SUCCESS_DELETE_').L('_EXCLAMATION_'));
        } else {
            $this->error(L('_ERROR_OPERATION_FAIL_').L('_EXCLAMATION_'));
        }
    }

    /**
     * 取消报名
     * @param $event_id
     * autor:xjw129xjt
     */
    public function unSign($event_id)
    {

        $event_content = D('Event')->where(array('status' => 1, 'id' => $event_id))->find();
        if (!$event_content) {
            $this->error(L('_EVENT_NOT_EXIST_').L('_EXCLAMATION_'));
        }

        $check = D('event_attend')->where(array('uid' => is_login(), 'event_id' => $event_id))->find();

        $res = D('event_attend')->where(array('uid' => is_login(), 'event_id' => $event_id))->delete();
        if ($res) {
            if ($check['status']) {
                D('Event')->where(array('id' => $event_id))->setDec('attentionCount');
            }
            D('Event')->where(array('id' => $event_id))->setDec('signCount');

            D('Message')->sendMessageWithoutCheckSelf($event_content['uid'], L('_TOAST_CANCEL_1_'), get_nickname(is_login()) . L('_TOAST_CANCEL_2_') . $event_content['title'] . L('_TOAST_CANCEL_3_'), 'Event/Index/detail', array('id' => $event_id));

            $this->success(L('_SUCCESS_SIGN_CANCEL_'));
        } else {
            $this->error(L('_ERROR_OPERATION_FAIL_'));
        }
    }

    /**
     * 报名弹出框页面
     * @param $event_id
     * autor:xjw129xjt
     */
    public function ajax_sign($event_id)
    {

        $event_content = D('Event')->where(array('status' => 1, 'id' => $event_id))->find();
        if (!$event_content) {
            $this->error(L('_EVENT_NOT_EXIST_').L('_EXCLAMATION_'));
        }
        //$this->checkAuth('Event/Index/doSign', $event_content['uid'], L('_INFO_LIMIT_').L('_EXCLAMATION_')); //zouj add
        D('Event')->where(array('id' => $event_id))->setInc('view_count');
        $event_content['user'] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar64', 'rank_html', 'signature'), $event_content['uid']);
        $event_content['type'] = $this->getType($event_content['type_id']);

        $menber = D('event_attend')->where(array('event_id' => $event_id, 'status' => 1))->select();
        foreach ($menber as $k => $v) {
            $event_content['member'][$k] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar64', 'rank_html', 'signature'), $v['uid']);

        }

        $this->assign('content', $event_content);
        $this->display();
    }

    /**
     * ajax删除活动
     * @param $event_id
     * autor:xjw129xjt
     */
    public function doDelEvent($event_id)
    {

        $event_content = D('Event')->where(array('status' => 1, 'id' => $event_id))->find();
        if (!$event_content) {
            $this->error(L('_EVENT_NOT_EXIST_').L('_EXCLAMATION_'));
        }
        $this->checkAuth('Event/Index/doDelEvent', $event_content['uid'],L('_INFO_DELETE_LIMIT_').L('_EXCLAMATION_'));
        $res = D('Event')->where(array('status' => 1, 'id' => $event_id))->setField('status', 0);
        if ($res) {
            $this->success(L('_SUCCESS_DELETE_').L('_EXCLAMATION_'), U('Event/Index/index'));
        } else {
            $this->error(L('_ERROR_OPERATION_FAIL_').L('_EXCLAMATION_'));
        }
    }

    /**
     * ajax提前结束活动
     * @param $event_id
     * autor:xjw129xjt
     */
    public function doEndEvent($event_id)
    {

        $event_content = D('Event')->where(array('status' => 1, 'id' => $event_id))->find();
        if (!$event_content) {
            $this->error(L('_EVENT_NOT_EXIST_').L('_EXCLAMATION_'));
        }
        $this->checkAuth('Event/Index/doEndEvent', $event_content['uid'], L('_INFO_OVER_LIMIT_').L('_EXCLAMATION_'));
        $data['eTime'] = time();
        $data['deadline'] = time();
        $res = D('Event')->where(array('status' => 1, 'id' => $event_id))->setField($data);
        if ($res) {
            $this->success(L('_SUCCESS_DELETE_').L('_EXCLAMATION_'));
        } else {
            $this->error(L('_ERROR_OPERATION_FAIL_').L('_EXCLAMATION_'));
        }

    }

}