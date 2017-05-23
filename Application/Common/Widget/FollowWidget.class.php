<?php
namespace Common\Widget;

use Think\Controller;

/**
 * Class FollowWidget
 * @package Common\Widget
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
class FollowWidget extends Controller
{
    /**
     * follow  关注按钮
     * @param int $follow_who
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function follow($follow_who = 0, $before, $after)
    {
        $follow_who = intval($follow_who);
        $who_follow = is_login();
        $is_following = D('Follow')->isFollow($who_follow, $follow_who);
        /********zouj add start***********/
        $type = M("member")->where("uid = ".$who_follow)->getField("type");
        $mapz["uid"] = $who_follow;
        if ($type == 0) {//学生
            $class_id = M("member")->where($mapz)->getField('class_id');//找到班级id
            $school_id = M("member")->where($mapz)->getField('school_id');//找到学校id
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
            
            $arr = array_merge($arr_class,$arr_teacher,$arr_parent);//三个角色合并
            if (in_array($follow_who, $arr)) {//本身就在班级圈内
                $this->assign("nei",1);
            } else {
                $this->assign("nei",0);
                $a = D('Follow')->isFollow($who_follow, $follow_who);
                $b = D('Follow')->isFollow($follow_who, $who_follow);
                if ($a && $b) {//两人互相关注
                    $this->assign("hx",2);
                } else if($a){//单方面关注
                    $this->assign("hx",1);
                } else {//互相没有关注
                    $this->assign("hx",0);
                }
            }
            
        } else if ($type == 1) {//老师
            $teacher_id = M("member")->where($mapz)->getField('teacher_id');//老师id
            $class_id = M("ref_teacher_class")->where("teacher_id=".$teacher_id)->getField("class_id",true);//所带班级id
            $school_id = M("member")->where($mapz)->getField('school_id');//找到学校id
            $mclass["class_id"] = array("in",$class_id);
            $mclass["school_id"] = $school_id;
            $arr_class = M("member")->where($mclass)->getField("uid",true);//找到同一个班级的uid

            //学生家长
            $arr_stu = M("member")->where($mclass)->getField("student_id",true);
            $parent['student_id'] = array("in",$arr_stu);
            $arr_parent = M("user_patriarch")->where($parent)->getField("id",true);
            $arr_a["parent_id"] = array("in",$arr_parent);
            $arr_jia = M("member")->where($arr_a)->getField("uid",true);

            //同校的老师
            $scmap["school_id"] =$school_id;
            $scmap["type"] = 1;
            $arr_school =  M("member")->where($scmap)->getField("uid",true);//找到同校老师的uid

            $arr = array_merge($arr_class,$arr_jia,$arr_school);//三个角色合并
            if (in_array($follow_who, $arr)) {//本身就在班级圈内
                $this->assign("nei",1);
            } else {
                $this->assign("nei",0);
                $a = D('Follow')->isFollow($who_follow, $follow_who);
                $b = D('Follow')->isFollow($follow_who, $who_follow);
                if ($a && $b) {//两人互相关注
                    $this->assign("hx",2);
                } else if($a){//单方面关注
                    $this->assign("hx",1);
                } else {//互相没有关注
                    $this->assign("hx",0);
                }
            }
        } else{//家长
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

            $arr = array_merge($arr_par,$arr_stu,$arr_tea);//三个角色合并
            if (in_array($follow_who, $arr)) {//本身就在班级圈内
                $this->assign("nei",1);
            } else {
                $this->assign("nei",0);
                $a = D('Follow')->isFollow($who_follow, $follow_who);
                $b = D('Follow')->isFollow($follow_who, $who_follow);
                if ($a && $b) {//两人互相关注
                    $this->assign("hx",2);
                } else if($a){//单方面关注
                    $this->assign("hx",1);
                } else {//互相没有关注
                    $this->assign("hx",0);
                }
            }
        }

        /********zouj add end***********/
        $this->assign('after', $after);
        $this->assign('before', $before);
        $this->assign('is_following', $is_following ? 1 : 0);
        $this->assign('is_self', $who_follow == $follow_who);
        $this->assign('follow_who', $follow_who);
        $this->display(T('Application://Common@Widget/follow'));
    }
}