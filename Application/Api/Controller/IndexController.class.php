<?php
namespace Api\Controller;
use Think\Controllerr;
class IndexController extends Controllerr {


    public function weibo_topic_hot($username = ""){//微博热门话题
    	header("Access-Control-Allow-Origin:*");
    	if (I("get.username")) {
    		$username = I("get.username");
    		$map['nickname'] = $username;
    		$message = M("member")->where($map)->field("uid,type")->find();
    	}
    	if ($message) {
    		if ($message['type'] == 0) {
    			$cs = M("member")->where("uid=".$message['uid'])->field("class_id,school_id")->find();
    			$class_id = $cs['class_id'];//找到班级id
	            $school_id = $cs['school_id'];//找到学校id
	            $mapclass["class_id"] = $class_id;
	            $mapclass["school_id"] = $school_id;
	            $arr_class = M("member")->where($mapclass)->getField("uid",true);//找到同一个班级的uid
	            $a1 = implode(",",$arr_class);

	            //所有的代课老师
	            $teacher_id = M("ref_teacher_class")->where("class_id=".$class_id)->getField("teacher_id",true);//所有任课老师的id
	            $mtea["teacher_id"] = array("in",$teacher_id);
	            $arr_teacher = M("member")->where($mtea)->getField("uid",true);
	            $a2 = implode(",",$arr_teacher);

	            //同班同学所有家长
	            $student_id = M("user_student")->where("class_id=".$class_id)->getField("id",true);
	            $mapj["student_id"] = array("in",$student_id);
	            $parent_id = M("user_patriarch")->where($mapj)->getField('id',true);
	            $mappar["parent_id"] = array("in",$parent_id);
	            $arr_parent = M("member")->where($mappar)->getField("uid",true);
	            $a3 = implode(",",$arr_parent);

	            //所关注的人
	            $mapp["who_follow"] = $message['uid'];
	            $arr = M("follow")->where($mapp)->getField("follow_who",true);//找到关注者的uid
	            $a4 = implode(",",$arr);
	            $result = $a1.",".$a2.",".$a3.",".$a4;
	            $result = str_replace(",,",",",$result);
	            $result = str_replace(",,,",",",$result);
	            $result = str_replace(",,,,",",",$result);
	            $result = rtrim($result,",");
    		}
    		if ($message['type'] == 1) {
    			//所带班级所有学生
	            $teacher_id = M("member")->where("uid=".$message['uid'])->getField('teacher_id');//老师id
	            $class_id = M("ref_teacher_class")->where("teacher_id=".$teacher_id)->getField("class_id",true);//所带班级id
	            $school_id = M("member")->where("uid=".$message['uid'])->getField('school_id');//找到学校id
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

	            //所关注的人
	            $mapp["who_follow"] = $uid;
	            $arr = M("follow")->where($mapp)->getField("follow_who",true);//找到关注者的uid
	            $a4 = implode(",",$arr);
	            $result = $a1.",".$a2.",".$a3.",".$a4;
	            $result = str_replace(",,",",",$result);
	            $result = str_replace(",,,",",",$result);
	            $result = str_replace(",,,,",",",$result);
	            $result = rtrim($result,",");
    		}
    		if ($message['type'] == 2) {
    			//班级所有家长
	            $parent_id = M("member")->where("uid=".$message['uid'])->getField("parent_id");
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

	            //所关注的人
	            $mapp["who_follow"] = $uid;
	            $arr = M("follow")->where($mapp)->getField("follow_who",true);//找到关注者的uid
	            $a4 = implode(",",$arr);
	            $result = $a1.",".$a2.",".$a3.",".$a4;
	            $result = str_replace(",,",",",$result);
	            $result = str_replace(",,,",",",$result);
	            $result = str_replace(",,,,",",",$result);
	            $result = rtrim($result,",");
    		}
    	}
    	$hot = M("")->query("SELECT
								ocenter_weibo_topic.id,
								ocenter_weibo_topic.name,
								ocenter_weibo_topic.weibo_num
							FROM
								ocenter_weibo_topic
							WHERE
								ocenter_weibo_topic.id IN (
									SELECT
										ocenter_weibo_topic_link.topic_id
									FROM
										ocenter_weibo_topic_link
									WHERE
										ocenter_weibo_topic_link.weibo_id IN (
											SELECT
												ocenter_weibo.id
											FROM
												ocenter_weibo
											WHERE
												ocenter_weibo.content LIKE '[topic:%'
											AND ocenter_weibo.uid IN ($result)
										)
								)"
							);
    	$num = count($hot);
        if ($hot) {
	        for($i=0;$i<$num;$i++){
	        	$hot[$i]["url"] = __ROOT__."/index.php/weibo/topic/index?topk=".$hot[$i]["id"];//跳转URL
	        }
	        dump($hot);
	        //echo json_encode($hot);
        } else {
        	echo json_encode($hot);
        }
    }

    public function  plate_hot($school_id=0){//论坛热门板块
		header("Access-Control-Allow-Origin:*"); 
		if (I("get.school_id")) {
			$school_id = I("get.school_id");
		}
		//$school_id = I("get.school_id");   	
	    	$hot = M("")->query("SELECT
				    				ocenter_forum.id,
									ocenter_forum.title,
									ocenter_forum.post_count,
									ocenter_picture.path
								FROM
									ocenter_forum
								left JOIN ocenter_picture ON ocenter_forum.logo = ocenter_picture.id
								WHERE
									ocenter_forum.status = 1 and ocenter_forum.school_id = $school_id or ocenter_forum.school_id = 0
								ORDER BY
									ocenter_forum.id DESC
									LIMIT 10"
								);

	    	$hot_num = count($hot);
	    	for($i=0;$i<$hot_num;$i++){
	    		if ($hot[$i]["path"] == null){
	    			$hot[$i]["path"] = "";
	    		} else {
	    			$hot[$i]["path"] = __ROOT__.$hot[$i]["path"];
	    		}
	    		$hot[$i]["url"] = __ROOT__."/index.php/forum/index/forum?id=".$hot[$i]["id"];//跳转URL
	    	}
	        dump($hot);
	        //echo json_encode($hot);
    }

    public function weibo_test(){//全部微博   测试接口
    	header("Access-Control-Allow-Origin:*");
    	$all = M("")->query("SELECT
    				ocenter_weibo.id,
					ocenter_weibo.content,
					ocenter_weibo.comment_count,
					ocenter_weibo.repost_count,
					ocenter_weibo.create_time,
					ocenter_weibo.data,
					ocenter_weibo.type,
					ocenter_member.uid,
					ocenter_member.nickname
				FROM
					ocenter_weibo
				INNER JOIN ocenter_member ON ocenter_weibo.uid = ocenter_member.uid
				WHERE
					ocenter_weibo.status = 1
				ORDER BY
					ocenter_weibo.id DESC");
    	// AND char_length(ocenter_weibo.content) > 5
    	$num = count($all);
    	for($i=0;$i<$num;$i++){
    			$map["row"] = $all[$i]["id"];
	    		$map["appname"] = "weibo";
	    		$all[$i]["likes"] = M("support")->where($map)->count();//support表中查找点赞数量
	    		$all[$i]["url"] = __ROOT__."/index.php?s=/weibo/index/weibodetail?id=".$all[$i]["id"];//跳转URL
	    		$head_pic = M("avatar")->where("uid=".$all[$i]["uid"])->getField("path");
	    		if ($head_pic != ""){//上传过头像
	    			$all[$i]["path"] = __ROOT__."/Uploads/Avatar".$head_pic;
	    		} else {//没有上传过头像，显示系统默认头像
	    			$all[$i]["path"] = __ROOT__."/Public/images/default_avatar.jpg";
	    		}


	    		if ($all[$i]["type"] == "repost"){//转发的微博
	    			$t1 = strpos($all[$i]["data"],';i:');
	    			$t2 = strpos($all[$i]["data"], ';}');
	    			$id = substr($all[$i]["data"],$t1+3,$t2-$t1-3);//找到原博的id
	    			$mapp["id"] = $id;
	    			$weiboo = M("weibo")->where($mapp)->field("uid,content,type")->find();
	    			$all[$i]["old_content"] = $weiboo["content"];
	    			if ($weiboo["type"] == "image"){//判断原微博是否含有图片
	    				$all[$i]["type"] = "image";
	    			}
	    			$all[$i]["old_nickname"] = M("member")->where('uid='.$weiboo["uid"])->getField("nickname");

	    			//dump($weiboo);
	    			//die();
	    		}
	    		/*if($all[$i]["type"] == "image"){//发的图片微博（原创）
	    			$pic_id = explode('"',$all[$i]["data"]);//用"切割，找出图片id
		    		$map['id'] = array("in",$pic_id[3]);//用,把图片id切割
		    		$pic_path = M("picture")->where($map)->field('path')->select();
		    		$pic_num = count($pic_path);
		    		for($j=0;$j<$pic_num;$j++){//用*把图片路径拼接起来
		    			if($j == 0){
		    				$all[$i]["pic_path"] = $pic_path[$j]["path"];
		    			}else{
		    				$all[$i]["pic_path"] = $all[$i]["pic_path"]."*".$pic_path[$j]["path"];
		    			}
		    		}
	    		}*/
    	}
    	//dump($all);
    	echo json_encode($all);
    }

    public function weibo_img(){//图片微博
    	header("Access-Control-Allow-Origin:*");
    	$all = M("")->query("SELECT
    				ocenter_weibo.id,
					ocenter_weibo.content,
					ocenter_weibo.comment_count,
					ocenter_weibo.repost_count,
					ocenter_weibo.create_time,
					ocenter_weibo.data,
					ocenter_member.nickname
				FROM
					ocenter_weibo
				INNER JOIN ocenter_member ON ocenter_weibo.uid = ocenter_member.uid
				WHERE
					ocenter_weibo.status = 1
				 AND ocenter_weibo.type = 'image'
				ORDER BY
					ocenter_weibo.id DESC");

    	$num = count($all);
    	for($i=0;$i<$num;$i++){
    		$map["row"] = $all[$i]["id"];
    		$map["appname"] = "weibo";
    		$all[$i]["likes"] = M("support")->where($map)->count();

    		$pic_id = explode('"',$all[$i]["data"]);//用"切割，找出图片id
    		//dump(explode(',',$pic_id[3]));
    		
    		$map['id'] = array("in",$pic_id[3]);//用,把图片id切割
    		$pic_path = M("picture")->where($map)->field('path')->select();
    		$pic_num = count($pic_path);
    		for($j=0;$j<$pic_num;$j++){//用*把图片路径拼接起来
    			if ($j == 0){
    				$all[$i]["pic_path"] = $pic_path[$j]["path"];
    			} else {
    				$all[$i]["pic_path"] = $all[$i]["pic_path"]."*".$pic_path[$j]["path"];
    			}
    		}	
    	}
    	//dump($all);
    	echo json_encode($all);
    }

    public function forum_all($school_id = ""){//所有的帖子
    	header("Access-Control-Allow-Origin:*");
    	if (I("get.school_id")){
			$school_id = I("get.school_id");
		}
    	$all = M("")->query("SELECT
					ocenter_forum_post.id,
					ocenter_forum_post.title,
					ocenter_forum_post.create_time,
					ocenter_forum_post.view_count,
					ocenter_forum_post.reply_count,
					ocenter_forum_post.last_reply_time,
					ocenter_forum.title AS big_title,
					ocenter_member.uid,
					ocenter_member.nickname
				FROM
					ocenter_forum_post
				INNER JOIN ocenter_forum ON ocenter_forum_post.forum_id = ocenter_forum.id
				INNER JOIN ocenter_member ON ocenter_forum_post.uid = ocenter_member.uid
				WHERE ocenter_forum_post.status = 1 and ocenter_forum_post.school_id = $school_id
				ORDER BY
				ocenter_forum_post.id
				DESC
				LIMIT 10");

	    	$all_num = count($all);
	    	for($i=0;$i<$all_num;$i++){//根据uid找到用户头像 
	    		$head_pic = M("avatar")->where("uid=".$all[$i]["uid"])->getField("path");
	    		if ($head_pic != ""){//上传过头像
	    			$all[$i]["path"] = __ROOT__."/Uploads/Avatar".$head_pic;
	    		} else {//没有上传过头像，显示系统默认头像
	    			$all[$i]["path"] = __ROOT__."/Public/images/default_avatar.jpg";
	    		}
	    		$all[$i]["url"] = __ROOT__."/index.php/forum/index/detail?id=".$all[$i]["id"];//跳转URL
	    	}
	    	dump($all);
	    	//echo json_encode($all);	
    }

    public function fans(){//查找用户的粉丝数、关注量、微博数
    	if (I("get.")) {//拿到get参数
            $arr = I("get.");
            $all_key = array_keys($arr);//所有的key值
            //echo $all_key[0];
            $str = "sns!@#$%".$all_key[0];//需要加密的字符串
            $md_str = md5($str);
            if ($md_str == $arr[$all_key[0]]) {//判断密钥是否一致
                $username = $all_key[0];
                $map["nickname"] = $username;
                $num = M("member")->where($map)->count();
                if ($num == 0) {//数据库没有的话写入
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
                //$username = "zhangsan";
		    	$map["nickname"] = $username;
		    	$uid = M("member")->where($map)->getField("uid");

		    	$mapone["follow_who"] = $uid;
		    	$fans = M("follow")->where($mapone)->count();//粉丝数
		    	$arrr["fans"] = $fans;
		    	$arrr["fans_url"] = __ROOT__."/index.php/ucenter/index/fans/uid/".$uid;

		    	$maptwo["who_follow"] = $uid;
		    	$follow = M("follow")->where($maptwo)->count();//关注量
		    	$arrr["follow"] = $follow;
		    	$arrr["follow_url"] = __ROOT__."/index.php/ucenter/index/following/uid/".$uid;

		    	$mapw["uid"] = $uid;
		    	$mapw["status"] = 1;
		    	$wb_num = M("weibo")->where($mapw)->count();//微博数
		    	$arrr["wb_num"] = $wb_num;
		    	$arrr["wb_url"] = __ROOT__."/index.php/ucenter/index/applist/uid/".$uid."/type/Weibo";

		    	echo json_encode($arrr);
		    	//dump($arr);
            } 
        }
    }

    public function weibo_all($username = ""){//全部微博
    	header("Access-Control-Allow-Origin:*");
    	if(I("get.username")){
	    	$username = I("get.username");
	    	$zoj["nickname"] = $username;
	    	$type = M("member")->where($zoj)->getField("type");
	    	$uid = M("member")->where($zoj)->getField("uid");
	    	$mapz["uid"] = $uid;
	    	if($type == 0){
	    		$class_id = M("member")->where($mapz)->getField('class_id');//找到班级id
	            $school_id = M("member")->where($mapz)->getField('school_id');//找到学校id
	            $mapclass["class_id"] = $class_id;
	            $mapclass["school_id"] = $school_id;
	            $arr_class = M("member")->where($mapclass)->getField("uid",true);//找到同一个班级的uid
	            $a1 = implode(",",$arr_class);

	            //所有的代课老师
	            $teacher_id = M("ref_teacher_class")->where("class_id=".$class_id)->getField("teacher_id",true);//所有任课老师的id
	            $mtea["teacher_id"] = array("in",$teacher_id);
	            $arr_teacher = M("member")->where($mtea)->getField("uid",true);
	            $a2 = implode(",",$arr_teacher);

	            //同班同学所有家长
	            $student_id = M("user_student")->where("class_id=".$class_id)->getField("id",true);
	            $mapj["student_id"] = array("in",$student_id);
	            $parent_id = M("user_patriarch")->where($mapj)->getField('id',true);
	            $mappar["parent_id"] = array("in",$parent_id);
	            $arr_parent = M("member")->where($mappar)->getField("uid",true);
	            $a3 = implode(",",$arr_parent);

	            //所关注的人
	            $mapp["who_follow"] = $uid;
	            $arr = M("follow")->where($mapp)->getField("follow_who",true);//找到关注者的uid
	            $a4 = implode(",",$arr);
	            $result = $a1.",".$a2.",".$a3.",".$a4;
	            $result = str_replace(",,",",",$result);
	            $result = str_replace(",,,",",",$result);
	            $result = str_replace(",,,,",",",$result);
	            $result = rtrim($result,",");
	           
	    	}
	    	if($type == 1){
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

	            //所关注的人
	            $mapp["who_follow"] = $uid;
	            $arr = M("follow")->where($mapp)->getField("follow_who",true);//找到关注者的uid
	            $a4 = implode(",",$arr);
	            $result = $a1.",".$a2.",".$a3.",".$a4;
	            $result = str_replace(",,",",",$result);
	            $result = str_replace(",,,",",",$result);
	            $result = str_replace(",,,,",",",$result);
	            $result = rtrim($result,",");
	    	}
	    	if($type == 2){
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

	            //所关注的人
	            $mapp["who_follow"] = $uid;
	            $arr = M("follow")->where($mapp)->getField("follow_who",true);//找到关注者的uid
	            $a4 = implode(",",$arr);
	            $result = $a1.",".$a2.",".$a3.",".$a4;
	            $result = str_replace(",,",",",$result);
	            $result = str_replace(",,,",",",$result);
	            $result = str_replace(",,,,",",",$result);
	            $result = rtrim($result,",");
	    	}
	    	$all = M("")->query("SELECT
	    				ocenter_weibo.id,
						ocenter_weibo.content,
						ocenter_weibo.comment_count,
						ocenter_weibo.repost_count,
						ocenter_weibo.create_time,
						ocenter_weibo.data,
						ocenter_weibo.type,
						ocenter_member.uid,
						ocenter_member.nickname
					FROM
						ocenter_weibo
					INNER JOIN ocenter_member ON ocenter_weibo.uid = ocenter_member.uid
					WHERE
						ocenter_weibo.status = 1 and ocenter_weibo.uid in ($result)
					ORDER BY
						ocenter_weibo.id DESC
						LIMIT 10");
	    	// AND char_length(ocenter_weibo.content) > 5
	    	$num = count($all);
	    	for($i=0;$i<$num;$i++){
	    			$map["row"] = $all[$i]["id"];
		    		$map["appname"] = "weibo";
		    		$all[$i]["likes"] = M("support")->where($map)->count();//support表中查找点赞数量
		    		$all[$i]["url"] = __ROOT__."/index.php/weibo/index/weibodetail?id=".$all[$i]["id"];//跳转URL
		    		$head_pic = M("avatar")->where("uid=".$all[$i]["uid"])->getField("path");
		    		if ($head_pic != ""){//上传过头像
		    			$all[$i]["path"] = __ROOT__."/Uploads/Avatar".$head_pic;
		    		} else {//没有上传过头像，显示系统默认头像
		    			$all[$i]["path"] = __ROOT__."/Public/images/default_avatar.jpg";
		    		}

		    		if (strstr($all[$i]["content"],"[topic:")){//查看微博是否带有话题
		    			$str = explode(']',$all[$i]["content"]);
		    			$numm = count($str);
		    			$all[$i]["content"] = "";
		    			for($z=1;$z<$numm;$z++){
		    				$all[$i]["content"] = $all[$i]["content"].$str[$z];
		    			}
		    			$topic = explode('[topic:',$str[0]);
		    			$topic_id = $topic[1];
		    			$all[$i]["topic_name"] = M("weibo_topic")->where("id=".$topic_id)->getField("name");
		    		}

		    		if ($all[$i]["type"] == "repost"){//转发的微博
		    			$t1 = strpos($all[$i]["data"],';i:');
		    			$t2 = strpos($all[$i]["data"], ';}');
		    			$id = substr($all[$i]["data"],$t1+3,$t2-$t1-3);//找到原博的id
		    			$mappp["id"] = $id;
		    			$weiboo = M("weibo")->where($mappp)->field("uid,content,type,create_time")->find();
		    			$all[$i]["old_content"] = $weiboo["content"];
		    			$all[$i]["orignal_time"] = $weiboo["create_time"];

		    			if (strstr($all[$i]["old_content"],"[topic:")){//查看微博是否带有话题
			    			$str = explode(']',$all[$i]["old_content"]);
			    			$nuum = count($str);
			    			$all[$i]["old_content"] = "";
			    			for($z=1;$z<$nuum;$z++){
			    				$all[$i]["old_content"] = $all[$i]["old_content"].$str[$z];
			    			}
			    			$topic = explode('[topic:',$str[0]);
			    			$topic_id = $topic[1];
			    			$all[$i]["old_topic_name"] = M("weibo_topic")->where("id=".$topic_id)->getField("name");
			    		}

		    			if ($weiboo["type"] == "image"){//判断原微博是否含有图片
		    				$all[$i]["type"] = "image";
		    			}
		    			$all[$i]["old_nickname"] = M("member")->where('uid='.$weiboo["uid"])->getField("nickname");

		    			//dump($weiboo);
		    			//die();
		    		}
		    		/*if($all[$i]["type"] == "image"){//发的图片微博（原创）
		    			$pic_id = explode('"',$all[$i]["data"]);//用"切割，找出图片id
			    		$map['id'] = array("in",$pic_id[3]);//用,把图片id切割
			    		$pic_path = M("picture")->where($map)->field('path')->select();
			    		$pic_num = count($pic_path);
			    		for($j=0;$j<$pic_num;$j++){//用*把图片路径拼接起来
			    			if($j == 0){
			    				$all[$i]["pic_path"] = $pic_path[$j]["path"];
			    			}else{
			    				$all[$i]["pic_path"] = $all[$i]["pic_path"]."*".$pic_path[$j]["path"];
			    			}
			    		}
		    		}*/
	    	}
	    	dump($all);
	    	//echo json_encode($all);
	    }
	}
}