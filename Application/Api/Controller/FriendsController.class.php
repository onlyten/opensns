<?php
namespace Api\Controller;
use Think\Controllerr;
class FriendsController extends Controllerr {
	private $page;//页数
	private $page_num = 10;//每一页显示的数量
	public function __construct(){
		$token = I("get.accessToken");
		if ($this->check($token)){//判断登录态
			if (I("get.page")) {//分页显示
	    		$this->page = I("get.page") > 1 ? (I("get.page")-1)*$this->page_num : 0;
	    	} else {
	    		$this->page = 0;
	    	}
		} else {
			$parent['code'] = 8;
			$parent['msg'] = "需登录";
			echo json_encode($parent);
			die();
		}
	}

	public function check($y){//验证access_token是否有效
		$url = "http://114.215.29.39:10010/open/login/check?accessToken=".$y;
		$pan = file_get_contents($url);
		if ($pan == "false") {
			return 0;
		} else {
			return 1;
		}		
	}

	public function addFriend(){//web添加好友
		//echo is_login();
		$str = I("get.uid")."*sns*10086";
		$strone = md5($str);
		if (I("get.uid") && I("get.key") && ($strone == I("get.key"))) {//验证参数是否正确
			$map['follow_who'] = I("get.uid");
			$map['who_follow'] = is_login();
			$id = M("follow")->where($map)->getField("id");
			if ($id) {
				echo "<SCRIPT language=JavaScript>alert('你们已经是好友了，无需重复添加！');location.href='javascript:history.go(-2);';</SCRIPT>";
			} else {
				$data['follow_who'] = I("get.uid");
				$data['who_follow'] = is_login();
				$data['create_time'] = time();
				M("follow")->data($data)->add();
				echo "<SCRIPT language=JavaScript>alert('添加成功！');location.href='javascript:history.go(-2);';</SCRIPT>";
			}
		} else {
			echo "<SCRIPT language=JavaScript>alert('参数错误！');location.href='javascript:history.go(-1);';</SCRIPT>";
		}
	}

	public function search_friend(){//搜索同校好友和班级圈的微博  (uid username)
		if (I("get.uid") && I("get.username")) {
			$uid = I("get.uid");
			$name = I("get.username");
			$school_id = M("member")->where('uid = '.$uid)->getField("school_id");
			if (!$school_id) {//查不到school_id 代表是家长身份
				$parent_id = M("member")->where('uid = '.$uid)->getField("parent_id");
				$school_id = M("")->query("SELECT
											ocenter_user_student.school_id
										FROM
											ocenter_user_student
										INNER JOIN ocenter_user_patriarch ON ocenter_user_student.id = ocenter_user_patriarch.student_id
										WHERE
											ocenter_user_patriarch.id = $parent_id
											LIMIT 1
										");

			}
			$map['school_id'] = $school_id;
			$map['nickname'] = array('like','%'.$name.'%');
			$other = M("member")->where($map)->field("uid,nickname")->select();//老师和学生
			$jia = M("")->query("SELECT
										ocenter_member.uid,ocenter_member.nickname
									FROM
										ocenter_member
									INNER JOIN ocenter_user_patriarch ON ocenter_member.parent_id = ocenter_user_patriarch.id
									INNER JOIN ocenter_user_student ON ocenter_user_patriarch.student_id = ocenter_user_student.id
									WHERE
										ocenter_user_student.school_id = 1
									AND ocenter_member.nickname LIKE '%".$name."%'
								");
			$all = array_merge($other,$jia);
			//dump($all);
			foreach ($all as $key => $value) {//循环获取用户的头像及头像更新时间
				$all[$key]['photoUrl'] = $this->head_pic($value['uid']); 
				$all[$key]['updateDate'] = $this->update_time($value['uid']);
				//查询是否已经是好友（互相关注）
				$maxp['follow_who'] = $value['uid'];
				$maxp['who_follow'] = $uid;
				$one = M("follow")->where($maxp)->getField("id");
				$manp['follow_who'] = $uid;
				$manp['who_follow'] = $value['uid'];
				$two = M("follow")->where($manp)->getField("id");
				if ($one && $two) {
					$all[$key]['status'] = 1;//已经是好友（互相关注）
				} else {
					$all[$key]['status'] = 0;//单方面关注
				}
			}
			if (count($all)) {
				$all = $this->mymArrsort($all,'status');
			} else {
				$all = null;
			}


			//根据关键字搜索班级圈微博
			$result = $this->goodFriends($uid);//找到班级圈的uid和同校好友的uid
			$mazp['uid'] = array("in",$result);
			$mazp['content'] = array("like","%$name%");
			$content = M("weibo")->where($mazp)->field('id,uid,content,create_time,comment_count,repost_count')->limit($this->page,$this->page_num)->select();
			foreach ($content as $key => $value) {
				$majp["row"] = $content[$key]["id"];
	    		$majp["appname"] = "Weibo";
	    		$content[$key]["likes"] = M("support")->where($majp)->count();//support表中查找点赞数量
	    		$content[$key]['likes_status'] = A('Snews')->likes_status($content[$key]['id'],$uid);
				$content[$key]['photoUrl'] = $this->head_pic($value['uid']);
				$content[$key]['updateDate'] = $this->update_time($value['uid']);
				if (strstr($content[$key]["content"],"[topic:")) {//查看微博是否带有话题
					$str = explode(']',$content[$key]["content"]);
	    			$first = strpos($content[$key]["content"],"]");
					$content[$key]["content"] = substr($content[$key]["content"],$first+1);//去掉微博内容前面[topic:]
	    			$topic = explode('[topic:',$str[0]);
	    			$topic_id = $topic[1];
	    			$topic_name = M("weibo_topic")->where("id=".$topic_id)->getField("name");
	    			$content[$key]["content"] = "#".$topic_name."#".$content[$key]["content"];
	    		}
			}
			//dump($content);
			
			//根据关键字搜索话题（话题以校为单位）
			$maptopic['school_id'] = $school_id;
			$maptopic['name'] = array("like","%$name%");
			//$topic_all = M("weibo_topic")->where($maptopic)->getField('name',true);
			$topic_all = M("weibo_topic")->where($maptopic)->field('id,name,logo')->select();
			$num = count($topic_all);
			for ($i=0; $i < $num; $i++) { 
				$topic_all[$i]['logo'] = C('MULU').'/Application/Weibo/Static/images/'.$topic_all[$i]['logo'];
			}

			$parents['data']['user'] = $all;
			$parents['data']['weibo'] = $content;
			$parents['data']['topic'] = $topic_all;
			$parents['code'] = 0;
			$parents['msg'] = "成功";
			//dump($parents);
		} else {
			$parents['code'] = 1;
			$parents['msg'] = "网络繁忙";
		}
		echo json_encode($parents);
	}

	public function apply_friend(){//申请好友 uid(自己id)  friend_id(被申请加为好友的id)
		if (I("get.uid") && I("get.friend_id")) {
			$uid = I("get.uid");
			$friend_id = I("get.friend_id");
			$nickname = M("member")->where("uid=".$uid)->getField('nickname');
			$a['uid'] = (string)$uid;
			//$s = 'http://'.$_SERVER['HTTP_HOST'].__ROOT__;
			$str = $friend_id."*sns*10086";
			$key = md5($str);

			//向follow表中插入（先查找是否有记录）
			$map['follow_who'] = $friend_id;
			$map['who_follow'] = $uid;
			$pan = M("follow")->where($map)->count();
			if (!$pan) {//之前没有关注过（follow表中没有记录）
				$dat['follow_who'] = $friend_id;
				$dat['who_follow'] = $uid;
				$dat['create_time'] = time();
				$dat['group_id'] = 0;
				M("follow")->data($dat)->add();
			}


			//向message_content表中插入
			$data['from_id'] = $uid;
			$data['title'] = "好友申请";
			$data['content'] = $nickname."申请加你好友,点击链接同意！";
			$data['url'] = C('MULU')."/index.php/Api/Friends/addFriend?uid=".$friend_id."&key=".$key;
			$data['args'] = json_encode($a);
			$data['type'] = "Ucenter";
			$data['create_time'] = time();
			$data['status'] = 1;
			$id = M("message_content")->data($data)->add();//向message_content表中插入并返回最新id

			//向message表中插入
			$dataone['content_id'] = $id;
			$dataone['from_uid'] = $uid;
			$dataone['to_uid'] = $friend_id;
			$dataone['create_time'] = time();
			$dataone['is_read'] = 0;
			$dataone['last_toast'] = time();
			$dataone['status'] = 1;
			$dataone['type'] = 'Ucenter';
			M("message")->data($dataone)->add();

			$parent['code'] = 0;
			$parent['msg'] = '成功';
		} else {
			$parent['code'] = 1;
			$parent['msg'] = '网络繁忙';
		}
	}

	public function add_friend(){//移动端添加好友（点击同意按钮调用） uid(自己id)  friend_id(被添加为好友的id)
		if (I("get.uid") && I("get.friend_id")) {
			$map['who_follow'] = $uid;
			$map['follow_who'] = $friend_id;
			$pan = M("follow")->where($map)->count();
			if (!$pan) {//follow表中没有记录
				$data['who_follow'] = $uid;
				$data['follow_who'] = $friend_id;
				$data['create_time'] = time();
				$data['who_follow'] = 0;
				M("follow")->data($data)->add();
			}
			//向message_content表中插入
			$nickname = M("member")->where("uid=".$uid)->getField('nickname');
			$a['uid'] = (string)$uid;
			$dat['from_id'] = $uid;
			$dat['title'] = "同意好友申请";
			$dat['content'] = $nickname."同意了你的好友申请！";
			$dat['url'] = '';
			$dat['args'] = json_encode($a);
			$dat['type'] = "Ucenter";
			$dat['create_time'] = time();
			$dat['status'] = 1;
			$id = M("message_content")->data($dat)->add();//向message_content表中插入并返回最新id

			//向message表中插入
			$dataone['content_id'] = $id;
			$dataone['from_uid'] = $uid;
			$dataone['to_uid'] = $friend_id;
			$dataone['create_time'] = time();
			$dataone['is_read'] = 0;
			$dataone['last_toast'] = time();
			$dataone['status'] = 1;
			$dataone['type'] = 'Ucenter';
			M("message")->data($dataone)->add();

			$parent['code'] = 0;
			$parent['msg'] = '成功';
		} else {
			$parent['code'] = 1;
			$parent['msg'] = '网络繁忙';
		}
		echo json_encode($parent);
	}

	public function remove_friend(){//解除好友  uid(自己id)  friend_id(被删除好友的id)
		if (I("get.uid") && I("get.friend_id")) {
			//删除我关注的她的
			$map['who_follow'] = $uid;
			$map['follow_who'] = $friend_id;
			M("follow")->where($map)->delete();

			//删除他关注我的
			$mapp['who_follow'] = $friend_id;
			$mapp['follow_who'] = $uid;
			M("follow")->where($mapp)->delete();

			$parent['code'] = 0;
			$parent['msg'] = '成功';
		} else {
			$parent['code'] = 1;
			$parent['msg'] = '网络繁忙';
		}
		echo json_encode($parent);
	}

	public function info(){//个人资料  uid(自己id)
		if (I("get.uid")) {
			$uid = I("get.uid");
			$type = M('member')->where('uid='.$uid)->getField('type');
			if ($type == '0') {
				$message = M("")->query("SELECT
											ocenter_member.nickname,
											ocenter_org_school.name AS school_name,
											concat(ocenter_org_grade_class.nj,'年级',ocenter_org_grade_class.name) AS class_name
										FROM
											ocenter_member
										INNER JOIN ocenter_org_school ON ocenter_member.school_id = ocenter_org_school.id
										INNER JOIN ocenter_org_grade_class ON ocenter_member.class_id = ocenter_org_grade_class.id
										WHERE
											ocenter_member.uid = $uid");
				$message[0]['role'] = '学生';
				$parent['code'] = 0;
				$parent['msg'] = '成功';
				$parent['data'] = $message[0];
			} else if ($type == '1') {
				$message = M("")->query("SELECT
											ocenter_member.nickname,
											ocenter_org_school.name AS school_name,
											ocenter_org_department.name AS depart_name
										FROM
											ocenter_member
										INNER JOIN ocenter_org_school ON ocenter_member.school_id = ocenter_org_school.id
										INNER JOIN ocenter_user_teacher ON ocenter_member.teacher_id = ocenter_user_teacher.id
										INNER JOIN ocenter_org_department ON ocenter_user_teacher.department_id = ocenter_org_department.id
										WHERE
											ocenter_member.uid = $uid");
				$message[0]['role'] = '老师';
				$parent['code'] = 0;
				$parent['msg'] = '成功';
				$parent['data'] = $message[0];
			} else if ($type == '2') {
				$message = M("")->query("SELECT
											ocenter_member.nickname,
											ocenter_user_student.xsxm AS child_name,
											ocenter_org_school.name AS school_name,
											concat(ocenter_org_grade_class.nj,'年级',ocenter_org_grade_class.name) AS class_name
											
										FROM
											ocenter_member
										INNER JOIN ocenter_user_patriarch ON ocenter_member.parent_id = ocenter_user_patriarch.id
										INNER JOIN ocenter_user_student ON ocenter_user_patriarch.student_id = ocenter_user_student.id
										INNER JOIN ocenter_org_school ON ocenter_user_student.school_id = ocenter_org_school.id
										INNER JOIN ocenter_org_grade_class ON ocenter_user_student.class_id = ocenter_org_grade_class.id
										WHERE
											ocenter_member.uid = $uid");
				$message[0]['role'] = '家长';
				$parent['code'] = 0;
				$parent['msg'] = '成功';
				$parent['data'] = $message[0];
			} else {
				$parent['code'] = -1;
				$parent['msg'] = '查无此人';
			}
		} else {
			$parent['code'] = 1;
			$parent['msg'] = '网络繁忙';
		}
		echo json_encode($parent);
	}

	public function role(){//通讯录好友
		$s = C('MULU');
		// $dd = $this->head_pic(1000);
		if (I("get.uid")) {
			$uid = I("get.uid");
		    $map["uid"] = $uid;
		    $type = M("member")->where($map)->getField("type");
		    if ($type != ''){//type不为空，说明该用户存在
		    	if ($type == 0) {//学生
		    		//我的同学
			    	$class_id = M("member")->where($map)->getField('class_id');//找到班级id
			        $school_id = M("member")->where($map)->getField('school_id');//找到学校id
			        $mapclass["class_id"] = $class_id;
			        $mapclass["school_id"] = $school_id;
			        $classmate = M("member")->where($mapclass)->getField("uid,nickname,account",true);//找到同班同学
			        $uid_t = M("member")->where($mapclass)->getField("uid",true);
			        $num = count($classmate);
			        for ($i=0; $i < $num; $i++) {
			        	$parent['data']['classmate'][$i]['id'] = $uid_t[$i];
			        	$parent['data']['classmate'][$i]['tt_id'] = $this->hh($classmate[$uid_t[$i]]['account']);
			        	$parent['data']['classmate'][$i]['username'] = $classmate[$uid_t[$i]]['nickname'];
			        	$parent['data']['classmate'][$i]['photoUrl'] = $this->head_pic($uid_t[$i]);
			        	$parent['data']['classmate'][$i]['updateDate'] = $this->update_time($uid_t[$i]);
			        }

			        //我的老师
			        $teacher_id = M("ref_teacher_class")->where("class_id=".$class_id)->getField("teacher_id",true);//所有任课老师的id
			        $mtea["teacher_id"] = array("in",$teacher_id);
			        $my_teacher = M("member")->where($mtea)->getField("uid,nickname,account",true);
			        $uid_l = M("member")->where($mtea)->getField("uid",true);
			        $num = count($my_teacher);
			        for ($i=0; $i < $num; $i++) { 
			        	$parent['data']['teacher'][$i]['id'] = $uid_l[$i];
			        	$parent['data']['teacher'][$i]['tt_id'] = $this->hh($my_teacher[$uid_l[$i]]['account']);
			        	$parent['data']['teacher'][$i]['username'] = $my_teacher[$uid_l[$i]]['nickname'];
			        	$parent['data']['teacher'][$i]['photoUrl'] = $this->head_pic($uid_l[$i]);
			        	$parent['data']['teacher'][$i]['updateDate'] = $this->update_time($uid_l[$i]);
			        }

			        //我的家长
			        $student_id = M("member")->where($map)->getField("student_id");//找到学生id
			        $mapp['student_id'] = $student_id;
			        $my_parent = M("user_patriarch")->where($mapp)->getField('id',true);
			        $num = count($my_parent);
			        for ($i=0; $i < $num; $i++) { 
			        	$my_parents = M("member")->where("parent_id=".$my_parent[$i])->getField("uid,nickname,account",true);
			        	$uid_j = M("member")->where("parent_id=".$my_parent[$i])->getField("uid",true);
			        }
			        $num = count($my_parents);
			        for ($i=0; $i < $num; $i++) { 
			        	$parent['data']['mypatriarch'][$i]['id'] = $uid_j[$i];
			        	$parent['data']['mypatriarch'][$i]['tt_id'] = $this->hh($my_parents[$uid_j[$i]]['account']);
			        	$parent['data']['mypatriarch'][$i]['username'] = $my_parents[$uid_j[$i]]['nickname'];
			        	$parent['data']['mypatriarch'][$i]['photoUrl'] = $this->head_pic($uid_j[$i]);
			        	$parent['data']['mypatriarch'][$i]['updateDate'] = $this->update_time($uid_j[$i]);
			        }

			        //我的好友
			        $parent['data']['friends'] = $this->my_friends($uid);

			        $parent['data']['type'] = 0;
		    	}

			    if ($type == 1) {//老师
			    	//我的学生
			    	$school_id = M("member")->where($map)->getField("school_id");
			    	$teacher_id = M("member")->where($map)->getField("teacher_id");
			    	$class_id = M("ref_teacher_class")->where("teacher_id=".$teacher_id)->getField("class_id",true);
			    	$mapp['id'] = array("in",$class_id);
			    	$mapp['school_id'] = $school_id;
			    	$class_name = M("org_grade_class")->where($mapp)->getField("name",true);
			    	$class_num = count($class_name);
			    	for ($i=0; $i < $class_num; $i++) { 
			    		$p = M("member")->where("class_id=".$class_id[$i])->getField("uid",true);
			    		$num = count($p);
			    		$parent['data']['student'][$i]['name'] = $class_name[$i];
				        for ($j=0; $j < $num; $j++) {
			    			$parent['data']['student'][$i]['data'][$j]['id'] = $p[$j];
			    			$parent['data']['student'][$i]['data'][$j]['tt_id'] = $this->hh(M("member")->where("uid=".$p[$j])->getField("account"));
			    			$parent['data']['student'][$i]['data'][$j]['username'] = M("member")->where("uid=".$p[$j])->getField("nickname");
			    			$parent['data']['student'][$i]['data'][$j]['photoUrl'] = $this->head_pic($p[$j]);
			    			$parent['data']['student'][$i]['data'][$j]['updateDate'] = $this->update_time($p[$j]);
				        }
			    	}

			    	//我的同事
			    	$maap["school_id"] = $school_id;
			    	$maap["type"] = 1;
			    	$pare = M("member")->where($maap)->getField("uid,teacher_id",true);
			    	
			    	$bumap["school_id"] = $school_id;
			    	$bumap["parent_id"] = 0;
			    	$part_name = M("org_department")->where($bumap)->getField("id,name",true);
			    	$j = 0;
			    	foreach ($part_name as $key => $value) {
			    		$zi_name = M("org_department")->where("parent_id = ".$key)->getField("id,name",true);
			    		if ($zi_name) {//包含子部门
			    			$part_nam[$value] = $zi_name;
			    			$i = 0;
			    			foreach ($part_nam[$value] as $k => $v) {
			    				$a_id = M("user_teacher")->where("department_id = ".$k)->getField('id',true);
			    				$buumap["teacher_id"] = array("in",$a_id);
			    				$all_uid = M("member")->where($buumap)->getField("uid",true);
			    				//dump($all_uid);
			    				$parent['data']['workmate'][$j]['sub'] = 1;
			    				$parent['data']['workmate'][$j]['name'] = $value;
			    				$parent['data']['workmate'][$j]['data'][$i]['name'] = $part_nam[$value][$k];
			    				$mm = 0;
			    				foreach ($all_uid as $ke => $valu) {
			    					$parent['data']['workmate'][$j]['data'][$i]['users'][$mm]['id'] = $valu;
						    		$parent['data']['workmate'][$j]['data'][$i]['users'][$mm]['tt_id'] = $this->hh(M("member")->where("uid=".$valu)->getField("account"));
						    		$parent['data']['workmate'][$j]['data'][$i]['users'][$mm]['username'] = M("member")->where("uid=".$valu)->getField("nickname");
						    		$parent['data']['workmate'][$j]['data'][$i]['users'][$mm]['photoUrl'] = $this->head_pic($valu);
						    		$parent['data']['workmate'][$j]['data'][$i]['users'][$mm]['updateDate'] = $this->update_time($valu);
						    		$mm++;
			    				}
			    				$i++;
			    			}
			    		} else {//不包含子部门
			    			$bu_id = M("user_teacher")->where("department_id = ".$key)->getField("id",true);
			    			if (count($bu_id)) {
			    				$buzmap["teacher_id"] = array("in",$bu_id);
				    			$bu_uid = M("member")->where($buzmap)->getField("uid",true);
				    			$i = 0;
				    			$parent['data']['workmate'][$j]['sub'] = 0;
				    			$parent['data']['workmate'][$j]['name'] = $value;
				    			foreach ($bu_uid as $k => $v) {
				    				$parent['data']['workmate'][$j]['users'][$i]['id'] = $v;
						    		$parent['data']['workmate'][$j]['users'][$i]['tt_id'] = $this->hh(M("member")->where("uid=".$v)->getField("account"));
						    		$parent['data']['workmate'][$j]['users'][$i]['username'] = M("member")->where("uid=".$v)->getField("nickname");
						    		$parent['data']['workmate'][$j]['users'][$i]['photoUrl'] = $this->head_pic($v);
						    		$parent['data']['workmate'][$j]['users'][$i]['updateDate'] = $this->update_time($v);
						    		++$i;
				    			}
			    			} else {
			    				# code...
			    			}
			    		}
			    		$j++;
			    	}
			    	// echo json_encode($parent);
			    	// dump($parent['data']['workmate']);
			    	// die();

			    	//dump($part_nam);
			    	// die();
			    	//$parent['data']['我的同事'] = M("member")->where($maap)->getField("nickname",true);
			    	// $i = 0;
			    	// foreach ($pare as $key => $value) {
			    	// 	$parent['data']['workmate'][$i]['id'] = $key;
			    	// 	$parent['data']['workmate'][$i]['tt_id'] = $this->hh(M("member")->where("uid=".$key)->getField("account"));
			    	// 	$parent['data']['workmate'][$i]['username'] = M("member")->where("uid=".$key)->getField("nickname");
			    	// 	$parent['data']['workmate'][$i]['photoUrl'] = $this->head_pic($key);
			    	// 	$parent['data']['workmate'][$i]['updateDate'] = $this->update_time($key);
			    	// 	$i++;
			    	// }
			    	// dump($parent['data']['workmate']);
			    	// die();
			    	// $num = count($pare);
			    	// for ($i=0; $i < $num; $i++) { 
			    	// 	$parent['data']['workmate'][$i]['id'] = $pare[$i];
			    	// 	$parent['data']['workmate'][$i]['tt_id'] = $this->hh(M("member")->where("uid=".$pare[$i])->getField("account"));
			    	// 	$parent['data']['workmate'][$i]['username'] = M("member")->where("uid=".$pare[$i])->getField("nickname");
			    	// 	$parent['data']['workmate'][$i]['photoUrl'] = $this->head_pic($pare[$i]);
			    	// 	$parent['data']['workmate'][$i]['updateDate'] = $this->update_time($pare[$i]);
			    	// }

			    	//学生家长
			    	for ($i=0; $i < $class_num; $i++) { 
			    		$parent['data']['patriarch'][$i]['name'] = $class_name[$i];
			    		$parent['data']['patriarch'][$i]['users'] = M("")->query("SELECT
											ocenter_member.uid AS id,
											ocenter_member.nickname AS username,
											CASE
								        	WHEN ISNULL(unix_timestamp(ocenter_avatar.update_time)) THEN
								        	0
								        	ELSE
								        	unix_timestamp(ocenter_avatar.update_time)
								        	END AS updateDate,

											CASE
											WHEN ISNULL(ocenter_avatar.path) THEN
											CONCAT(
												'$s','/Public/images/default_avatar.jpg'
											)
											ELSE
											CONCAT(
												'$s','/Uploads/Avatar',
												ocenter_avatar.path
											)
											END AS photoUrl

										FROM
											ocenter_user_patriarch
										INNER JOIN ocenter_user_student ON ocenter_user_student.id = ocenter_user_patriarch.student_id
										INNER JOIN ocenter_member ON ocenter_user_patriarch.id = ocenter_member.parent_id
										LEFT JOIN ocenter_avatar ON ocenter_member.uid = ocenter_avatar.uid
										WHERE
											ocenter_user_student.class_id = $class_id[$i]");
			    	}
			    	/****************返回tt_id start****************/
			    	$numone = count($parent['data']['patriarch']);
			    	for ($i=0; $i < $numone; $i++) { 
			    		$numtwo = count($parent['data']['patriarch'][$i]['users']);
			    		for ($j=0; $j < $numtwo; $j++) { 
			    			$jiauid = $parent['data']['patriarch'][$i]['users'][$j]['id'];
			    			$parent['data']['patriarch'][$i]['users'][$j]['tt_id'] = $this->hh(M("member")->where("uid=".$jiauid)->getField("account"));
			    		}
			    	}
			    	/****************返回tt_id end*****************/

			    	//我的好友
			        $parent['data']['friends'] = $this->my_friends($uid);

			    	$parent['data']['type'] = 1;
			    }

			    if ($type == 2) {//家长
			    	//授课教师
			    	$parent_id = M("member")->where($map)->getField('parent_id');//找到家长id
			        $student_id = M("user_patriarch")->where("id=".$parent_id)->getField('student_id');
			        $class_id = M("member")->where("student_id=".$student_id)->getField("class_id");
			    	$teacher_id = M("ref_teacher_class")->where("class_id=".$class_id)->getField("teacher_id",true);//所有任课老师的id
			        $mtea["teacher_id"] = array("in",$teacher_id);
			        $all_uid = M("member")->where($mtea)->getField("uid",true);
			        $str = implode(",",$all_uid);
			        $str = "(".$str.")";
			       
			        $parent['data']['teacher'] = M("")->query("SELECT 
											        	ocenter_member.uid AS id,ocenter_member.nickname as username,
											        	CASE
											        	WHEN ISNULL(unix_timestamp(ocenter_avatar.update_time)) THEN
											        	0
											        	ELSE
											        	unix_timestamp(ocenter_avatar.update_time)
											        	END AS updateDate,
											        	
														CASE
														WHEN ISNULL(ocenter_avatar.path) THEN
															CONCAT(
																'$s','/Public/images/default_avatar.jpg'
															)
														ELSE
															CONCAT(
																'$s','/Uploads/Avatar',
																ocenter_avatar.path
															)
														END AS photoUrl
														FROM
															ocenter_member
														LEFT JOIN ocenter_avatar ON ocenter_member.uid = ocenter_avatar.uid
														WHERE ocenter_member.uid in $str");
			        /***返回tt_id***/
			        $numone = count($parent['data']['teacher']);
			        for ($i=0; $i < $numone; $i++) { 
			        	$tuid = $parent['data']['teacher'][$i]['id'];
			        	$parent['data']['teacher'][$i]['tt_id'] = $this->hh(M("member")->where("uid=".$tuid)->getField("account"));
			        }

			        //我的孩子
			        $mappp['student_id'] = $student_id;
			        $my_kids = M("member")->where($mappp)->getField('uid');
			        $parent['data']['children'] = M("")->query("SELECT 
											        	ocenter_member.uid AS id,ocenter_member.nickname as username,
											        	CASE
											        	WHEN ISNULL(unix_timestamp(ocenter_avatar.update_time)) THEN
											        	0
											        	ELSE
											        	unix_timestamp(ocenter_avatar.update_time)
											        	END AS updateDate,

														CASE
														WHEN ISNULL(ocenter_avatar.path) THEN
															CONCAT(
																'$s','/Public/images/default_avatar.jpg'
															)
														ELSE
															CONCAT(
																'$s','/Uploads/Avatar',
																ocenter_avatar.path
															)
														END AS photoUrl
														FROM
															ocenter_member
														LEFT JOIN ocenter_avatar ON ocenter_member.uid = ocenter_avatar.uid
														WHERE ocenter_member.uid = $my_kids");
			        $numtwo = count($parent['data']['children']);
			        for ($i=0; $i < $numtwo; $i++) { 
			        	$cuid = $parent['data']['children'][$i]['id'];
			        	$parent['data']['children'][$i]['tt_id'] = $this->hh(M("member")->where("uid=".$cuid)->getField("account"));
			        }

			        //我的好友
			        $parent['data']['friends'] = $this->my_friends($uid);

			        $parent['data']['type'] = 2;
			    }

			    $parent['code'] = 0;
				$parent['msg'] = "成功";
		    } else {
		    	$parent['code'] = 2;
				$parent['msg'] = "该用户不存在";
		    }
		} else {
			$parentz['code'] = 1;
		    $parentz['msg'] = "网络繁忙";
		}
		echo json_encode($parent);
	}

	private function head_pic($uid){//获取用户的头像
		//$s = 'http://'.$_SERVER['HTTP_HOST'].__ROOT__;
		$head_pi = M("avatar")->where("uid=".$uid)->getField("path");
		if ($head_pi != "") {//上传过头像
			$head_pic = C('MULU')."/Uploads/Avatar".$head_pi;
			//$head_pic = "/Uploads/Avatar".$head_pi;
		} else {//没有上传过头像，显示系统默认头像
			$head_pic = C('MULU')."/Public/images/default_avatar.jpg";
		}
		return $head_pic;
	}

	private function update_time($uid){//用户头像更新时间
		$update_time = M("avatar")->where("uid=".$uid)->getField("update_time");
		if ($update_time) {
			$time = strtotime($update_time);
			return (string)$time;
		} else {
			return "0";
		}
	}

	public function hh($username){
		$url = "http://120.27.46.200/auth/login";
		$post = array("submit" => "submit","account" => "admin","password" => "admin");
		$cookie = './a.txt';
		//模拟登录
		$this->login_post($url, $cookie, $post);
		$d = $this->find_id($cookie,$username);
		return $d;
		@ unlink($cookie);
	}

	public function find_id($cookie,$username){//去TT查找该用户的id（tt_id）
	  $url2 = "http://120.27.46.200/user/find";
	  $data2 = array("username" => $username);
	  $content = $this->get_content($url2, $data2, $cookie);
	  return $content;
	  //echo $content;
	}

	public function select_uid($type,$ref_id){//根据用户类型（type）和 对应id（ref_id）找到与之相对应的uid
		if ($type == 1) {
			$mapref["teacher_id"] = $ref_id;
		} else if($type == 2){
			$mapref["student_id"] = $ref_id;
		} else{
			$mapref["parent_id"] = $ref_id;
		}
		$uid = M("member")->where($mapref)->getField("uid");
		return $uid;
	}

	public function login_post($url, $cookie, $post) {
	    $curl = curl_init();//初始化curl模块
	    curl_setopt($curl, CURLOPT_URL, $url);//登录提交的地址
	    curl_setopt($curl, CURLOPT_HEADER, 0);//是否显示头信息
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息
	    curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie); //设置Cookie信息保存在指定的文件中
	    curl_setopt($curl, CURLOPT_POST, 1);//post方式提交
	    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));//要提交的信息
	    curl_exec($curl);//执行cURL
	    curl_close($curl);//关闭cURL资源，并且释放系统资源
	} 

	private function my_friends($uid){//我的好友(互相关注)  uid(自己id)
		//$uid = I("get.uid");
		$followed = M('follow')->where('who_follow='.$uid)->getField('follow_who',true);//找到被关注的人
		$map['who_follow'] = array("in",$followed);
		$map['follow_who'] = $uid;
		$user = M('follow')->where($map)->getField('who_follow',true);//互相关注的人
		$mapp['uid'] = array("in",$user);
		$info = M('member')->where($mapp)->field('account,nickname')->select();

		$num = count($user);
		if ($num) {
			for ($i=0; $i < $num; $i++) { 
				$parent['data']['friends'][$i]['id'] = $user[$i];
	        	$parent['data']['friends'][$i]['tt_id'] = $this->hh($info[$i]['account']);
	        	$parent['data']['friends'][$i]['username'] = $info[$i]['nickname'];
	        	$parent['data']['friends'][$i]['photoUrl'] = $this->head_pic($user[$i]);
	        	$parent['data']['friends'][$i]['updateDate'] = $this->update_time($user[$i]);
			}
			return $parent['data']['friends'];
		} else {
			return null;
		}
	}

	public function goodFriends($uid){//根据uid查找班级圈的uid和自己好友的uid
    	$mapz["uid"] = $uid;
    	$type = M("member")->where($mapz)->getField("type");
    	if ($type == 0) {
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

            //互相关注
            $mapp["who_follow"] = $uid;
            $arr = M("follow")->where($mapp)->getField("follow_who",true);//找到被关注者的uid
            $humap["who_follow"] = array("in",$arr);
            $humap["follow_who"] = $uid;
            $ar = M("follow")->where($humap)->getField("who_follow",true);//找到互相关注的

            if (count($arr_class) && count($arr_teacher)) {
            	$arr_all = array_merge($arr_class,$arr_teacher);
            } 
            if(count($arr_parent)) {
            	$arr_all = array_merge($arr_all,$arr_parent);
            }
            if(count($ar)) {
            	$arr_all = array_merge($arr_all,$ar);
            }
            $result = implode(',', $arr_all);
    	}
    	if ($type == 1) {
    		//所带班级所有学生
            $teacher_id = M("member")->where($mapz)->getField('teacher_id');//老师id
            $class_id = M("ref_teacher_class")->where("teacher_id=".$teacher_id)->getField("class_id",true);//所带班级id
            $school_id = M("member")->where($mapz)->getField('school_id');//找到学校id
            $mclass["class_id"] = array("in",$class_id);
            $mclass["school_id"] = $school_id;
            $arr_class = M("member")->where($mclass)->getField("uid",true);//找到同一个班级的uid
            $a1 = implode(",",$arr_class);

            //学生家长
            $arr_stu = M("member")->where($mclass)->getField("student_id",true);
            $parent['student_id'] = array("in",$arr_stu);
            $arr_parent = M("user_patriarch")->where($parent)->getField("id",true);
            $arr_a["parent_id"] = array("in",$arr_parent);
            $a = M("member")->where($arr_a)->getField("uid",true);
            $a2 = implode(",",$a);

            //同校的老师
            $scmap["school_id"] =$school_id;
            $scmap["type"] = 1;
            $arr_school =  M("member")->where($scmap)->getField("uid",true);//找到同校老师的uid
            $a3 = implode(",",$arr_school);

            //互相关注
            $mapp["who_follow"] = $uid;
            $arr = M("follow")->where($mapp)->getField("follow_who",true);//找到被关注者的uid
            $humap["who_follow"] = array("in",$arr);
            $humap["follow_who"] = $uid;
            $ar = M("follow")->where($humap)->getField("who_follow",true);//找到互相关注的

            $a4 = implode(",",$ar);
            $result = $a1.",".$a2.",".$a3.",".$a4;
            $result = str_replace(",,",",",$result);
            $result = str_replace(",,,",",",$result);
            $result = str_replace(",,,,",",",$result);
            $result = rtrim($result,",");
    	}
    	if ($type == 2) {
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
            $a1 = implode(",",$arr_par);

            //班级所有学生
            $arr_stu = M("member")->where($mapb)->getField("uid",true);
            $a2 = implode(",",$arr_stu);

            //班级所有老师
            $mapd["id"] = array("in",$all_student);
            $class_id_all = M("user_student")->where($mapd)->getField("class_id",true);
            $mape["class_id"] = array("in",$class_id_all);
            $teacher_id_all = M("ref_teacher_class")->where($mape)->getField("teacher_id",true);
            $mapf["teacher_id"] = array("in",$teacher_id_all);
            $arr_tea = M("member")->where($mapf)->getField("uid",true);
            $a3 = implode(",",$arr_tea);

            //互相关注
            $mapp["who_follow"] = $uid;
            $arr = M("follow")->where($mapp)->getField("follow_who",true);//找到被关注者的uid
            $humap["who_follow"] = array("in",$arr);
            $humap["follow_who"] = $uid;
            $ar = M("follow")->where($humap)->getField("who_follow",true);//找到互相关注的

            $a4 = implode(",",$ar);
            $result = $a1.",".$a2.",".$a3.",".$a4;
            $result = str_replace(",,",",",$result);
            $result = str_replace(",,,",",",$result);
            $result = str_replace(",,,,",",",$result);
            $result = rtrim($result,",");
    	}
    	return $result;
	}

	function get_content($url, $data, $cookie) {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); //读取cookie
	    curl_setopt($ch,CURLOPT_POST,true); //设置为POST请求
	    curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	    $rs = curl_exec($ch); //执行cURL抓取页面内容
	    curl_close($ch);
	    return $rs;
	}

	public function mymArrsort($arr,$var){//二维数组根据某一字段排序
        $tmp=array();
        $rst=array();
        foreach($arr as $key=>$trim){
            $tmp[$key]=$trim[$var];
        }
        arsort($tmp);
        $i=0;
        foreach($tmp as $key1=>$trim1){
            $rst[$i]=$arr[$key1];
            $i=$i+1;
        }
        return $rst;
    }
}