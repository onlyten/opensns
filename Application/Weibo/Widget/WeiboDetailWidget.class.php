<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Weibo\Widget;

use Think\Controller;

/**
 * 分类widget
 * 用于动态调用分类信息
 */
class WeiboDetailWidget extends Controller
{

    /* 显示指定分类的同级分类或子分类列表 */
    public function detail($weibo_id,$can_hide=0)
    {
        if(!$can_hide){
            $weiboCacheModel=D('Weibo/WeiboCache');
            $html=$weiboCacheModel->getCacheHtml($weibo_id);//获取weibo html缓存
            $weibo = D('Weibo/Weibo')->getWeiboDetail($weibo_id);
            if($html===null){
                $weibo = D('Weibo/Weibo')->getWeiboDetail($weibo_id);
                $this->assign('weibo', $weibo);
                $this->_initAssign();
                $this->assign('un_prase_comment', 1);
                $html=$this->fetch(T('Weibo@Widget/detail'));
                $weiboCacheModel->setCacheHtml($weibo_id,$html);//设置weibo html缓存
            }
            $html=replace_weibo_html($html,$weibo_id);
            session("username");
            $map["nickname"] = session("username");
            $user_type = M("member")->where($map)->getField('type');//当前用户身份  0学生  1老师
            $uid = M("member")->where($map)->getField('uid');//当前用户uid

            if($user_type == 0){//学生
                //同班同学
                $class_id = M("member")->where($map)->getField('class_id');//找到班级id
                $school_id = M("member")->where($map)->getField('school_id');//找到学校id
                $mapclass["class_id"] = $class_id;
                $mapclass["school_id"] = $school_id;
                $arr_class = M("member")->where($mapclass)->getField("uid",true);//找到同一个班级的uid

                //所有的代课老师
                $teacher_id = M("ref_teacher_class")->where("class_id=".$class_id)->getField("teacher_id",true);//所有任课老师的id
                $mtea["teacher_id"] = array("in",$teacher_id);
                $arr_teacher = M("member")->where($mtea)->getField("uid",true);

                //同班同学所有家长
                $student_id = M("user_student")->where("class_id=".$class_id)->getField("id",true);
                $mapj["student_id"] = array("in",$student_id);
                $parent_id = M("user_patriarch")->where($mapj)->getField('id',true);
                $mappar["parent_id"] = array("in",$parent_id);
                $arr_parent = M("member")->where($mappar)->getField("uid",true);

                //互相关注的人
                $mapp["who_follow"] = $uid;
                $arr = M("follow")->where($mapp)->getField("follow_who",true);//找到被关注者的uid
                $humap["who_follow"] = array("in",$arr);
                $humap["follow_who"] = $uid;
                $ar = M("follow")->where($humap)->getField("who_follow",true);//找到互相关注的

                if(in_array($weibo["uid"],$ar) or in_array($weibo["uid"],$arr_class) or in_array($weibo["uid"],$arr_teacher) or in_array($weibo["uid"],$arr_parent)){
                    $this->show($html);
                }
            }
            if($user_type == 1){//老师
                //所带班级所有学生
                $teacher_id = M("member")->where($map)->getField('teacher_id');//老师id
                $class_id = M("ref_teacher_class")->where("teacher_id=".$teacher_id)->getField("class_id",true);//所带班级id
                $school_id = M("member")->where($map)->getField('school_id');//找到学校id
                $mclass["class_id"] = array("in",$class_id);
                $mclass["school_id"] = $school_id;
                $arr_class = M("member")->where($mclass)->getField("uid",true);//找到同一个班级的uid

                //学生家长
                $arr_stu = M("member")->where($mclass)->getField("student_id",true);
                $parent['student_id'] = array("in",$arr_stu);
                $arr_parent = M("user_patriarch")->where($parent)->getField("id",true);
                $arr_a["parent_id"] = array("in",$arr_parent);
                $a = M("member")->where($arr_a)->getField("uid",true);

                //同校的老师
                $scmap["school_id"] =$school_id;
                $scmap["type"] = 1;
                $arr_school =  M("member")->where($scmap)->getField("uid",true);//找到同校老师的uid

                //互相关注的人
                $mapp["who_follow"] = $uid;
                $arr = M("follow")->where($mapp)->getField("follow_who",true);//找到关注者的uid
                $humap["who_follow"] = array("in",$arr);
                $humap["follow_who"] = $uid;
                $ar = M("follow")->where($humap)->getField("who_follow",true);//找到互相关注的

                if(in_array($weibo["uid"],$ar) or in_array($weibo["uid"],$arr_class) or in_array($weibo["uid"],$arr_school) or in_array($weibo["uid"],$a)){
                    $this->show($html);
                }
            }
            if($user_type == 2){//家长
                //班级所有家长
                $parent_id = M("member")->where("uid=".$uid)->getField("parent_id");
                $student_id = M("user_patriarch")->where("id=".$parent_id)->getField("student_id");
                $aa = M("user_student")->where("id=".$student_id)->field("school_id,class_id")->find();
                $mapa['class_id'] = $aa["class_id"];
                $mapa['school_id'] = $aa["school_id"];
                $all_student = M("user_student")->where($mapa)->getField("id",true);
                $mapb["student_id"] = array("in",$all_student);
                $parent_id = M("user_patriarch")->where($mapb)->getField("id",true);
                $mapc["parent_id"] = array("in",$parent_id);
                $arr_par = M("member")->where($mapc)->getField("uid",true);

                //班级所有学生
                $arr_stu = M("member")->where($mapb)->getField("uid",true);

                //班级所有老师
                $mapd["id"] = array("in",$all_student);
                $class_id_all = M("user_student")->where($mapd)->getField("class_id",true);
                $mape["class_id"] = array("in",$class_id_all);
                $teacher_id_all = M("ref_teacher_class")->where($mape)->getField("teacher_id",true);
                $mapf["teacher_id"] = array("in",$teacher_id_all);
                $arr_tea = M("member")->where($mapf)->getField("uid",true);

                //互相关注的人
                $mapp["who_follow"] = $uid;
                $arr = M("follow")->where($mapp)->getField("follow_who",true);//找到关注者的uid
                $humap["who_follow"] = array("in",$arr);
                $humap["follow_who"] = $uid;
                $ar = M("follow")->where($humap)->getField("who_follow",true);//找到互相关注的

                if(in_array($weibo["uid"],$arr_par) or in_array($weibo["uid"],$arr_stu) or in_array($weibo["uid"],$arr_tea) or in_array($weibo["uid"],$ar)){
                    $this->show($html);
                }
            }
            //$this->show($html);
        }else{
            $weibo = D('Weibo/Weibo')->getWeiboDetail($weibo_id);

            $map['follow_who'] = $weibo['uid'];
            $map['who_follow'] = is_login();
            if($map['follow_who'] != $map['who_follow']) {
                $res = M('follow')->where($map)->find();
                if($res) {
                    $sign = 1;
                }
            } else {
                $sign = 1;
            }
            $this->assign('sign', $sign);

            //置顶动态隐藏显示
            $this->assign('can_hide',$can_hide);
            $top_hide=0;
            if($can_hide){
                $hide_ids=cookie('Weibo_index_top_hide_ids');
                $hide_ids=explode(',',$hide_ids);
                $top_hide=in_array($weibo_id,$hide_ids);
            }
            $this->assign('top_hide',$top_hide);

            $this->assign('weibo', $weibo);
            $this->assign('un_prase_comment', 0);
            $this->display(T('Weibo@Widget/detail'));
        }
    }

    public function weibo_html($weibo_id)
    {
        $weibo = D('Weibo/Weibo')->getWeiboDetail($weibo_id);
        $this->assign('weibo', $weibo);
        $this->_initAssign();
        $html=$this->fetch(T('Application://Weibo@Widget/detail'));
        $weiboCacheModel=D('Weibo/WeiboCache');
        $weiboCacheModel->setCacheHtml($weibo_id,$html);//设置weibo html缓存
        $html=replace_weibo_html($html,$weibo_id);
        return $html;
    }

    /**
     * 覆盖必要数据，防止出错
     * @author:zzl(郑钟良) zzl@ourstu.com
     */
    private function _initAssign()
    {
        $this->assign('can_hide',false);
        $this->assign('top_hide',false);
    }
}
