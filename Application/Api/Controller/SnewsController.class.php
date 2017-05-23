<?php
namespace Api\Controller;
use Think\Controllerr;
class SnewsController extends Controllerr {
	private $page;//页数
	private $page_num = 8;//每一页显示的数量
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

	public function my_weibo(){//我的微博  (uid last_id)
		if (I("get.uid")) {
			$uid = I("get.uid");
			$last_id = I("get.last_id");
			$all = $this->wb_find($uid,$last_id);
			if (count($all)) {
				$parent['code'] = 0;
				$parent['msg'] = "成功";
				$parent['last_id'] = $all[count($all)-1]['id'];
				$parent['data'] = $all;
			} else {
				$parent['code'] = -1;
	    		$parent['msg'] = "暂无数据";
			}
		} else {
			$parent['code'] = 1;
			$parent['msg'] = "网络繁忙";
		}
		echo json_encode($parent);
	}

    /*public function weibo_topic_hot(){//微博热门话题  (uid page)
    	header("Access-Control-Allow-Origin:*");
    	if (I("get.uid")) {
    		$result = A('Friends')->goodFriends(I("get.uid"));
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
								)
								ORDER BY
									ocenter_weibo_topic.weibo_num DESC LIMIT $this->page,$this->page_num"
							);
	    	
	        if (count($hot)) {
		        // for ($i=0; $i<$num; $i++) {
		        // 	$hot[$i]["url"] = __ROOT__."/index.php/weibo/topic/index?topk=".$hot[$i]["id"];//跳转URL
		        // }
		        $parentt['code'] = 0;
				$parentt['msg'] = "成功";
		        $parentt['data'] = $hot;
	        } else {
	        	$parentt['code'] = -1;
				$parentt['msg'] = "没有话题";
	        }
        } else {
        	$parentt['code'] = 1;
			$parentt['msg'] = "网络繁忙";
        }
		echo json_encode($parentt);
    }*/

    public function weibo_topic_hot(){//微博热门话题   (uid)
    	header("Access-Control-Allow-Origin:*");
    	if (I("get.uid")) {
    		$result = A('Friends')->goodFriends(I("get.uid"));
	    	$school_id = $this->find_school_id(I("get.uid"));
	    	$map['school_id'] = $school_id;
	    	// $hot = M('weibo_topic')->where($map)->field('id,name,weibo_num')->order('weibo_num desc')->limit($this->page,$this->page_num)->select();
	    	$hot = M('weibo_topic')->where($map)->field('id,name')->select();
	        if (count($hot)) {
	        	$num = count($hot);
	        	for ($i=0; $i < $num; $i++) { 
	        		$a = '[topic:'.$hot[$i]['id'].']%';
	        		$mapi['content'] = array('like',$a);
	        		$mapi['uid'] = array('in',$result);
	        		$hot[$i]['weibo_num'] = M('weibo')->where($mapi)->count();
	        	}
	        	$hot = A('Friends')->mymArrsort($hot,'weibo_num');//按weibo_num倒序排序
	        	$hott = array_slice($hot,0,10);
	        	if (count($hott)) {
	        		$parent['code'] = 0;
					$parent['msg'] = "成功";
			        $parent['data'] = $hott;
	        	} else {
	        		$parent['code'] = -1;
					$parent['msg'] = "没有话题";
	        	}  
	        } else {
	        	$parent['code'] = -1;
				$parent['msg'] = "没有话题";
	        }
        } else {
        	$parent['code'] = 1;
			$parent['msg'] = "网络繁忙";
        }
		echo json_encode($parent);
    }

    public function weibo_all(){//获取全部微博  (uid last_id)
    	header("Access-Control-Allow-Origin:*");
    	$uid = I('get.uid');
		if ($uid) {
	    	$result = A('Friends')->goodFriends($uid);
	    	if ($result) {
	    		if (I("get.last_id")) {
	    			$last_id = I("get.last_id");
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
							ocenter_weibo.status = 1 and ocenter_weibo.uid in ($result) and ocenter_weibo.id < $last_id
						ORDER BY
							ocenter_weibo.id DESC LIMIT $this->page_num");
	    		} else {
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
							ocenter_weibo.id DESC LIMIT $this->page_num");
	    		}
	    		
		    	// AND char_length(ocenter_weibo.content) > 5
		    	$num = count($all);
		    	if ($num) {
		    		for ($i=0; $i<$num; $i++) {
		    			$map["row"] = $all[$i]["id"];
			    		$map["appname"] = "Weibo";
			    		$all[$i]["likes"] = M("support")->where($map)->count();//support表中查找点赞数量
			    		$all[$i]['like_status'] = $this->likes_status($all[$i]['id'],$uid);
			    		// $all[$i]["url"] = __ROOT__."/index.php/weibo/index/weibodetail?id=".$all[$i]["id"];//跳转URL
			    		$head_pic = M("avatar")->where("uid=".$all[$i]["uid"])->getField("path");
			    		if ($head_pic != "") {//上传过头像
			    			$all[$i]["head_pic"] = C('MULU')."/Uploads/Avatar".$head_pic;
			    		} else {//没有上传过头像，显示系统默认头像
			    			$all[$i]["head_pic"] = C('MULU')."/Public/images/default_avatar.jpg";
			    		}

			    		if (strstr($all[$i]["content"],"[topic:")) {//查看微博是否带有话题
    						$str = explode(']',$all[$i]["content"]);
			    			$first = strpos($all[$i]["content"],"]");
    						$all[$i]["content"] = substr($all[$i]["content"],$first+1);//去掉微博内容前面[topic:]
			    			$topic = explode('[topic:',$str[0]);
			    			$topic_id = $topic[1];
			    			$topic_name = M("weibo_topic")->where("id=".$topic_id)->getField("name");
			    			$all[$i]["content"] = "#".$topic_name."#".$all[$i]["content"];
			    		}

			    		if ($all[$i]["type"] == "repost") {//转发的微博
			    			$a = unserialize($all[$i]["data"]);
							$id = $a["sourceId"];//找到原博的id
			    			$mappp["id"] = $id;
			    			$weiboo = M("weibo")->where($mappp)->field("uid,content,type,data,create_time")->find();
			    			$all[$i]["old_content"] = $weiboo["content"];
			    			$all[$i]["orignal_time"] = $weiboo["create_time"];

			    			if (strstr($all[$i]["old_content"],"[topic:")) {//查看微博是否带有话题
				    			$str = explode(']',$all[$i]["old_content"]);
				    			$first = strpos($all[$i]["old_content"],"]");
    							$all[$i]["old_content"] = substr($all[$i]["old_content"],$first+1);//去掉微博内容前面[topic:]
				    			$topic = explode('[topic:',$str[0]);
				    			$topic_id = $topic[1];
				    			$topic_name = M("weibo_topic")->where("id=".$topic_id)->getField("name");
				    			$all[$i]["old_content"] = "#".$topic_name."#".$all[$i]["old_content"];
				    		}

			    			if ($weiboo["type"] == "image") {//判断原微博是否含有图片
			    				$pic_id = unserialize($weiboo["data"]);//反序列化找出图片id
		    					$map['id'] = array("in",$pic_id["attach_ids"]);
					    		$pic_path = M("picture")->where($map)->field('path')->select();
					    		$pic_num = count($pic_path);
					    		for($j=0;$j<$pic_num;$j++){//用*把图片路径拼接起来
					    			if($j == 0){
					    				$all[$i]["pic_path"] = C('MULU').$pic_path[$j]["path"];
					    			}else{
					    				$all[$i]["pic_path"] = C('MULU').$all[$i]["pic_path"]."*".C('MULU').$pic_path[$j]["path"];
					    			}
					    		}
			    				$all[$i]["type"] = "imagereport";
			    			}
			    			$all[$i]["old_nickname"] = M("member")->where('uid='.$weiboo["uid"])->getField("nickname");
			    			$old_head_pic = M("avatar")->where("uid=".$weiboo["uid"])->getField("path");
				    		if ($old_head_pic != "") {//上传过头像
				    			$all[$i]["old_head_pic"] = C('MULU')."/Uploads/Avatar".$old_head_pic;
				    		} else {//没有上传过头像，显示系统默认头像
				    			$all[$i]["old_head_pic"] = C('MULU')."/Public/images/default_avatar.jpg";
				    		}
			    		}
			    		if($all[$i]["type"] == "image"){//发的图片微博（原创）
			    			$pic_id = unserialize($all[$i]["data"]);//反序列化找出图片id
		    				$map['id'] = array("in",$pic_id["attach_ids"]);
				    		$pic_path = M("picture")->where($map)->field('path')->select();
				    		$pic_num = count($pic_path);
				    		for($j=0;$j<$pic_num;$j++){//用*把图片路径拼接起来
				    			if($j == 0){
				    				$all[$i]["pic_path"] = C('MULU').$pic_path[$j]["path"];
				    			}else{
				    				$all[$i]["pic_path"] = C('MULU').$all[$i]["pic_path"]."*".C('MULU').$pic_path[$j]["path"];
				    			}
				    		}
			    		}
			    	}
			    	$parentz['code'] = 0;
		    		$parentz['msg'] = "成功";
		    		$parentz['last_id'] = $all[$num-1]['id'];
		    		$parentz['data'] = $all;
		    	} else {
		    		$parentz['code'] = -1;
	    			$parentz['msg'] = "暂无数据";
		    	}
	    	} else {
	    		$parentz['code'] = -1;
	    		$parentz['msg'] = "暂无数据";
	    	}
	    } else {
	    	$parentz['code'] = 1;
	    	$parentz['msg'] = "网络繁忙";
	    }
	    echo json_encode($parentz);
	}

	public function hot_weibo(){//热门微博 (uid)
		header("Access-Control-Allow-Origin:*");
		$uid = I("get.uid");
		if ($uid) {
	    	$result = A('Friends')->goodFriends($uid);
	    	if ($result) {
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
							ocenter_weibo.comment_count DESC LIMIT $this->page_num");
		    	// AND char_length(ocenter_weibo.content) > 5
		    	$num = count($all);
		    	if ($num) {
		    		for ($i=0; $i<$num; $i++) {
		    			$maps["row"] = $all[$i]["id"];
			    		$maps["appname"] = "Weibo";
			    		$all[$i]["likes"] = M("support")->where($maps)->count();//support表中查找点赞数量
			    		$all[$i]['likes_status'] = $this->likes_status($all[$i]["id"],$uid);
			    					    		
			    		// $all[$i]["url"] = __ROOT__."/index.php/weibo/index/weibodetail?id=".$all[$i]["id"];//跳转URL
			    		$head_pic = M("avatar")->where("uid=".$all[$i]["uid"])->getField("path");
			    		if ($head_pic != "") {//上传过头像
			    			$all[$i]["head_pic"] = C('MULU')."/Uploads/Avatar".$head_pic;
			    		} else {//没有上传过头像，显示系统默认头像
			    			$all[$i]["head_pic"] = C('MULU')."/Public/images/default_avatar.jpg";
			    		}

			    		if (strstr($all[$i]["content"],"[topic:")) {//查看微博是否带有话题
    						$str = explode(']',$all[$i]["content"]);
			    			$first = strpos($all[$i]["content"],"]");
    						$all[$i]["content"] = substr($all[$i]["content"],$first+1);//去掉微博内容前面[topic:]
			    			$topic = explode('[topic:',$str[0]);
			    			$topic_id = $topic[1];
			    			$topic_name = M("weibo_topic")->where("id=".$topic_id)->getField("name");
			    			$all[$i]["content"] = "#".$topic_name."#".$all[$i]["content"];
			    		}

			    		if ($all[$i]["type"] == "repost") {//转发的微博
			    			$a = unserialize($all[$i]["data"]);
							$id = $a["sourceId"];//找到原博的id
			    			$mappp["id"] = $id;
			    			$weiboo = M("weibo")->where($mappp)->field("uid,content,type,data,create_time")->find();
			    			$all[$i]["old_content"] = $weiboo["content"];
			    			$all[$i]["orignal_time"] = $weiboo["create_time"];

			    			if (strstr($all[$i]["old_content"],"[topic:")) {//查看微博是否带有话题
				    			$str = explode(']',$all[$i]["old_content"]);
				    			$first = strpos($all[$i]["old_content"],"]");
    							$all[$i]["old_content"] = substr($all[$i]["old_content"],$first+1);//去掉微博内容前面[topic:]
				    			$topic = explode('[topic:',$str[0]);
				    			$topic_id = $topic[1];
				    			$topic_name = M("weibo_topic")->where("id=".$topic_id)->getField("name");
				    			$all[$i]["old_content"] = "#".$topic_name."#".$all[$i]["old_content"];
				    		}

			    			if ($weiboo["type"] == "image") {//判断原微博是否含有图片
			    				$pic_id = unserialize($weiboo["data"]);//反序列化找出图片id
		    					$map['id'] = array("in",$pic_id["attach_ids"]);
					    		$pic_path = M("picture")->where($map)->field('path')->select();
					    		$pic_num = count($pic_path);
					    		for($j=0;$j<$pic_num;$j++){//用*把图片路径拼接起来
					    			if($j == 0){
					    				$all[$i]["pic_path"] = C('MULU').$pic_path[$j]["path"];
					    			}else{
					    				$all[$i]["pic_path"] = C('MULU').$all[$i]["pic_path"]."*".C('MULU').$pic_path[$j]["path"];
					    			}
					    		}
			    				$all[$i]["type"] = "imagereport";
			    			}
			    			$all[$i]["old_nickname"] = M("member")->where('uid='.$weiboo["uid"])->getField("nickname");
			    			$old_head_pic = M("avatar")->where("uid=".$weiboo["uid"])->getField("path");
				    		if ($old_head_pic != "") {//上传过头像
				    			$all[$i]["old_head_pic"] = C('MULU')."/Uploads/Avatar".$old_head_pic;
				    		} else {//没有上传过头像，显示系统默认头像
				    			$all[$i]["old_head_pic"] = C('MULU')."/Public/images/default_avatar.jpg";
				    		}
			    		}
			    		if($all[$i]["type"] == "image"){//发的图片微博（原创）
			    			$pic_id = unserialize($all[$i]["data"]);//反序列化找出图片id
		    				$map['id'] = array("in",$pic_id["attach_ids"]);
				    		$pic_path = M("picture")->where($map)->field('path')->select();
				    		$pic_num = count($pic_path);
				    		for($j=0;$j<$pic_num;$j++){//用*把图片路径拼接起来
				    			if($j == 0){
				    				$all[$i]["pic_path"] = C('MULU').$pic_path[$j]["path"];
				    			}else{
				    				$all[$i]["pic_path"] = C('MULU').$all[$i]["pic_path"]."*".C('MULU').$pic_path[$j]["path"];
				    			}
				    		}
			    		}
			    	}
			    	$parentz['code'] = 0;
		    		$parentz['msg'] = "成功";
		    		$parentz['data'] = $all;
		    	} else {
		    		$parentz['code'] = -1;
	    			$parentz['msg'] = "暂无数据";
		    	}
	    	} else {
	    		$parentz['code'] = -1;
	    		$parentz['msg'] = "暂无数据";
	    	}
	    } else {
	    	$parentz['code'] = 1;
	    	$parentz['msg'] = "网络繁忙";
	    }
	    echo json_encode($parentz);
	}

	public function write_weibo_comment(){//插入微博评论  (uid, weibo_id, content)
		if (I("post.uid") && I("post.weibo_id") && I("post.content")) {
			$data['uid'] = I("post.uid");
			$data['weibo_id'] = I("post.weibo_id");
			$data['content'] = I("post.content");
			$data['create_time'] = time();
			$data['status'] = 1;
			$data['to_comment_id'] = 0;
			if (M('weibo_comment')->add($data)) {
				$parent['code'] = 0;
				$parent['msg'] = "成功";
			} else {
				$parent['code'] = -1;
				$parent['msg'] = "失败";
			}	
		} else {
			$parent['code'] = 1;
			$parent['msg'] = "网络繁忙";
		}
		echo json_encode($parent);
	}

	public function read_weibo_comment(){//读取微博评论  (weibo_id last_id)
		$s = C('MULU');
		$token = I("get.accessToken");
		if (I("get.weibo_id")) {
			$last_id = (I("get.last_id")) ? I("get.last_id") : 999999999 ;
			$weibo_id = I("get.weibo_id");
			$all = M("")->query("SELECT
					ocenter_weibo_comment.id,
					ocenter_weibo_comment.content,
					ocenter_member.nickname,
					CASE
				WHEN ISNULL(ocenter_avatar.path) THEN
					'$s/Public/images/default_avatar.jpg'
				ELSE
					CONCAT(
						'$s/Uploads/Avatar',
						ocenter_avatar.path
					)
				END AS head_pic,
				 ocenter_weibo_comment.create_time
				FROM
					ocenter_weibo_comment
				INNER JOIN ocenter_member ON ocenter_weibo_comment.uid = ocenter_member.uid
				LEFT JOIN ocenter_avatar ON ocenter_member.uid = ocenter_avatar.uid
				WHERE
					ocenter_weibo_comment.weibo_id = $weibo_id and ocenter_weibo_comment.id < $last_id
				ORDER BY
					ocenter_weibo_comment.create_time DESC LIMIT $this->page_num"
				);
			if ($all) {
				$parent['code'] = 0;
				$parent['msg'] = "成功";
				$parent['last_id'] = $all[count($all)-1]['id'];
				$parent['data'] = $all;
			} else {
				$parent['code'] = -1;
				$parent['msg'] = "暂无评论";
			}
		} else {
			$parent['code'] = 1;
			$parent['msg'] = "网络繁忙";
		}
		echo json_encode($parent);
	}

	public function read_weibo_likes(){//读取微博点赞   (weibo_id last_id)
		$s = C('MULU');
		if (I("get.weibo_id")) {
			$weibo_id = I("get.weibo_id");
			$last_id = (I("get.last_id")) ? I("get.last_id") : 999999999 ;
			$all = M("")->query("SELECT
									ocenter_support.id,
									ocenter_member.nickname,
									ocenter_member.uid,
									CASE
								WHEN ISNULL(ocenter_avatar.path) THEN
									'$s/Public/images/default_avatar.jpg'
								ELSE
									CONCAT(
										'$s/Uploads/Avatar',
										ocenter_avatar.path
									)
								END AS head_pic,
								 ocenter_support.create_time
								FROM
									ocenter_support
								INNER JOIN ocenter_member ON ocenter_support.uid = ocenter_member.uid
								LEFT JOIN ocenter_avatar ON ocenter_member.uid = ocenter_avatar.uid
								WHERE
									ocenter_support.row = $weibo_id AND ocenter_support.appname = 'Weibo' AND ocenter_support.id < $last_id
								ORDER BY
									ocenter_support.create_time DESC LIMIT $this->page,$this->page_num"
								);
			$num = count($all);
			for ($i=0; $i < $num; $i++) { 
				$all[$i]['updateDate'] = $this->update_time($all[$i]['uid']);
			}
			if ($all) {
				$parent['code'] = 0;
				$parent['msg'] = "成功";
				$parent['last_id'] = $all[$num-1]['id'];
				$parent['data'] = $all;
			} else {
				$parent['code'] = -1;
				$parent['msg'] = "暂无点赞";
			}
		} else {
			$parent['code'] = 1;
			$parent['msg'] = "网络繁忙";
		}
		echo json_encode($parent);
	}

	public function read_weibo_repost(){//读取微博转发   (weibo_id last_time)
		if (I('get.weibo_id')) {
			$last_time = (I("get.last_time")) ? I("get.last_time") : 99999999999999 ;
			$weibo_id = I('get.weibo_id');
			$map['type'] = 'repost';
			$map['status'] = 1;
			$map['data'] = array("like",'%;i:'.$weibo_id.'%');
			$map['create_time'] = array("lt",$last_time);
			$data_all = M('weibo')->where($map)->order('id desc')->limit($this->page_num)->field('uid,create_time')->select();
			$num = count($data_all);
			if ($num) {
				for ($i=0; $i < $num; $i++) { 
					$user[$i]['uid'] = $data_all[$i]['uid'];
					$user[$i]['nickname'] = M("member")->where("uid = ".$data_all[$i]['uid'])->getField("nickname");
					$user[$i]['create_time'] = $data_all[$i]['create_time'];
					$head_pic = M("avatar")->where("uid=".$user[$i]["uid"])->getField("path");
		    		if ($head_pic != "") {//上传过头像
		    			$user[$i]["head_pic"] = C('MULU')."/Uploads/Avatar".$head_pic;
		    		} else {//没有上传过头像，显示系统默认头像
		    			$user[$i]["head_pic"] = C('MULU')."/Public/images/default_avatar.jpg";
		    		}
		    		$user[$i]["updateDate"] = $this->update_time($data_all[$i]['uid']);
				}
				$parent['code'] = 0;
	    		$parent['msg'] = "成功";
	    		$parent['last_time'] = $user[$num-1]['create_time'];
	    		$parent['data'] = $user;
			} else {
				$parent['code'] = -1;
	    		$parent['msg'] = "暂无数据";
			}
		} else {
			$parent['code'] = 1;
			$parent['msg'] = "网络繁忙";
		}
		echo json_encode($parent);
	}

	public function weibo_one(){//获取单个微博  (weibo_id)
		$s = C('MULU');
		if (I("get.weibo_id")) {
			$weibo_id = I("get.weibo_id");
			$all = M("")->query("SELECT
									ocenter_weibo.content,
									ocenter_member.nickname,
									ocenter_member.uid,
									ocenter_weibo.type,
									ocenter_weibo.data,
									ocenter_weibo.repost_count,
									ocenter_weibo.comment_count,
									CASE
								WHEN ISNULL(ocenter_avatar.path) THEN
									'$s/Public/images/default_avatar.jpg'
								ELSE
									CONCAT(
										'$s/Uploads/Avatar',
										ocenter_avatar.path
									)
								END AS head_pic,
								 ocenter_weibo.create_time
								FROM
									ocenter_weibo
								INNER JOIN ocenter_member ON ocenter_weibo.uid = ocenter_member.uid
								LEFT JOIN ocenter_avatar ON ocenter_member.uid = ocenter_avatar.uid
								WHERE
									ocenter_weibo.id = $weibo_id");
			if (count($all)) {
				$zan["row"] = $weibo_id;
				$zan["appname"] = "Weibo";
				$all[0]["likes"] = M("support")->where($zan)->count();//support表中查找点赞数量
				$all[0]['updateDate'] = $this->update_time($all[0]['uid']);

				if (strstr($all[0]["content"],"[topic:")) {//查看微博是否带有话题
	    			$str = explode(']',$all[0]["content"]);
	    			$topic = explode('[topic:',$str[0]);
	    			$topic_id = $topic[1];
	    			$first = strpos($all[0]["content"],"]");
	    			$all[0]["content"] = substr($all[0]["content"],$first+1);//去掉微博内容前面[topic:]
	    			$topic_name = M("weibo_topic")->where("id=".$topic_id)->getField("name");
	    			$all[0]["content"] = "#".$topic_name."#".$all[0]["content"];
		    	}

				if ($all[0]["type"] == "repost") {//转发的微博
					$a = unserialize($all[0]["data"]);
					$id = $a["sourceId"];//找到原博的id
	    			$maap["id"] = $id;
	    			$weiboo = M("weibo")->where($maap)->field("uid,content,type,data,create_time")->find();
	    			$all[0]["old_content"] = $weiboo["content"];
	    			$all[0]["orignal_time"] = $weiboo["create_time"];

	    			if (strstr($all[0]["old_content"],"[topic:")) {//查看微博是否带有话题
		    			$str = explode(']',$all[0]["old_content"]);
		    			$first = strpos($all[0]["old_content"],"]");
	    				$all[0]["old_content"] = substr($all[0]["old_content"],$first+1);//去掉微博内容前面[topic:]
		    			$topic = explode('[topic:',$str[0]);
		    			$topic_id = $topic[1];
		    			$topic_name = M("weibo_topic")->where("id=".$topic_id)->getField("name");
		    			$all[0]["old_content"] = "#".$topic_name."#".$all[0]["old_content"];
		    		}

	    			if ($weiboo["type"] == "image") {//判断原微博是否含有图片
	    				$pic_id = unserialize($weiboo["data"]);//反序列化找出图片id
			    		$map['id'] = array("in",$pic_id["attach_ids"]);
			    		$pic_path = M("picture")->where($map)->field('path')->select();
			    		$pic_num = count($pic_path);
			    		for($j=0;$j<$pic_num;$j++){//用*把图片路径拼接起来
			    			if($j == 0){
			    				$all[0]["pic_path"] = C('MULU').$pic_path[$j]["path"];
			    			}else{
			    				$all[0]["pic_path"] = C('MULU').$all[0]["pic_path"]."*".C('MULU').$pic_path[$j]["path"];
			    			}
			    		}
	    				$all[0]["type"] = "imagereport";
	    			}
	    			$all[0]["old_nickname"] = M("member")->where('uid='.$weiboo["uid"])->getField("nickname");
	    			$old_head_pic = M("avatar")->where("uid=".$weiboo["uid"])->getField("path");
		    		if ($old_head_pic != "") {//上传过头像
		    			$all[0]["old_head_pic"] = C('MULU')."/Uploads/Avatar".$old_head_pic;
		    		} else {//没有上传过头像，显示系统默认头像
		    			$all[0]["old_head_pic"] = C('MULU')."/Public/images/default_avatar.jpg";
		    		}
	    		}	

	    		if($all[0]["type"] == "image"){//发的图片微博（原创）
	    			$pic_id = unserialize($all[0]["data"]);//反序列化找出图片id
			    	$map['id'] = array("in",$pic_id["attach_ids"]);
		    		$pic_path = M("picture")->where($map)->field('path')->select();
		    		$pic_num = count($pic_path);
		    		for($j=0;$j<$pic_num;$j++){//用*把图片路径拼接起来
		    			if($j == 0){
		    				$all[0]["pic_path"] = C('MULU').$pic_path[$j]["path"];
		    			}else{
		    				$all[0]["pic_path"] = C('MULU').$all[0]["pic_path"]."*".C('MULU').$pic_path[$j]["path"];
		    			}
		    		}
	    		}
	    		$parent['code'] = 0;
	    		$parent['msg'] = "成功";
	    		$parent['data'] = $all[0];
			} else {
				$parent['code'] = -1;
	    		$parent['msg'] = "微博id有误";
			}	

		} else {
			$parent['code'] = 1;
			$parent['msg'] = "网络繁忙";
		}
		echo json_encode($parent);
	}

	public function likes(){//微博点赞  (weibo_id uid)
		if (I("get.uid") && I("get.weibo_id")) {
			$uid = I("get.uid");
			$weibo_id = I("get.weibo_id");
			$map['uid'] = $uid;
			$map['row'] = $weibo_id;
			$map['appname'] = "Weibo";
			$num = M('support')->where($map)->count();
			if ($num) {
				$parent['code'] = -1;
				$parent['msg'] = "不能再赞了";
			} else {
				$data['appname'] = "Weibo";
				$data['row'] = $weibo_id;
				$data['uid'] = $uid;
				$data['create_time'] = time();
				$data['table'] = "weibo";
				if (M('support')->data($data)->add()) {
					$parent['code'] = 0;
					$parent['msg'] = "成功";
				} else {
					$parent['code'] = -2;
					$parent['msg'] = "失败";
				}
			}

		} else {
			$parent['code'] = 1;
			$parent['msg'] = "网络繁忙";
		}
		echo json_encode($parent);
	}

	public function remove_likes(){//微博取消点赞  (weibo_id uid)
		$uid = I('get.uid');
		$weibo_id = I('get.weibo_id');
		if ($uid && $weibo_id) {
			$map['appname'] = 'Weibo';
			$map['row'] = $weibo_id;
			$map['uid'] = $uid;
			if (M('support')->where($map)->delete()) {
				$parent['code'] = 0;
				$parent['msg'] = '成功';
			} else {
				$parent['code'] = -1;
				$parent['msg'] = '失败';
			}
		} else {
			$parent['code'] = 1;
			$parent['msg'] = '网络繁忙';
		}
		echo json_encode($parent);
	}

	public function delete_weibo(){//删除微博  (weibo_id  uid)
		if (I("get.weibo_id") && I("get.uid")) {
			$weibo_id = I("get.weibo_id");
			$uid = I("get.uid");
			$map['id'] = $weibo_id;
			$old_uid = M('weibo')->where($map)->getField("uid");
			if ($old_uid == $uid) {
				$data['status'] = -1;
				if (M('weibo')->where($map)->data($data)->save()) {
					$parent['code'] = 0;
					$parent['msg'] = "成功";
				} else {
					$parent['code'] = -1;
					$parent['msg'] = "失败";
				}

			} else {
				$parent['code'] = -2;
				$parent['msg'] = "无权限";
			}

		} else {
			$parent['code'] = 1;
			$parent['msg'] = "网络繁忙";
		}
		echo json_encode($parent);
	}

	public function delete_weibo_comment(){//删除微博评论 (comment_id  uid)
		if (I("get.comment_id") && I("get.uid")) {
			$uid = I("get.uid");
			$comment_id = I("get.comment_id");
			$weibo_id = M('weibo_comment')->where("id=".$comment_id)->getField("weibo_id");
			$reviewers_id = M('weibo_comment')->where("id=".$comment_id)->getField("uid");//评论者uid
			$blogger_id = M('weibo')->where("id=".$weibo_id)->getField("uid");//博主uid
			if ($uid == $reviewers_id || $uid == $blogger_id) {
				$data['status'] = -1;
				if (M('weibo_comment')->where("id=".$comment_id)->save($data)) {
					$parent['code'] = 0;
					$parent['msg'] = "成功";
				} else {
					$parent['code'] = -2;
					$parent['msg'] = "删除失败";
				}
				
			} else {
				$parent['code'] = -1;
				$parent['msg'] = "无权限";
			}

		} else {
			$parent['code'] = 1;
			$parent['msg'] = "网络繁忙";
		}
		echo json_encode($parent);	
	}

	public function send_weibo(){//发表微博 (uid content from)
		if (I("post.uid") && I("post.content") && I("post.from")) {
			$uid = I("post.uid");
			$content = I("post.content");
			$from = I("post.from");
	        $data['type'] = "feed";
			$data['data'] = "a:0:{}";
	        
	        if (I("post.img_url")) {//判断是否传入图片
	        	$date = date("Y-m-d",time());
	            $img_url = I("post.img_url");
	        	$img_num = count(I("post.img_url"));
	        	$all_pic_id = "";
	        	for( $i=0; $i<$img_num; $i++ ){
	        		//$img_info = getimagesize($img_url[$i]);
	                $img_width = 123;
	                $img_height = 123;
	                $data_one["type"] = "local";
	                $data_one["path"] = $img_url[$i];
	                $data_one["status"] = 1; 
	                $data_one["create_time"] = time(); 
	                $data_one["width"] = $img_width;
	                $data_one["height"] = $img_height;
	                M('picture')->data($data_one)->add();
	                $map['path'] = $img_url[$i];
	                $pic_id = M('picture')->where($map)->getField("id");
					$all_pic_id = $all_pic_id.",".$pic_id;
	        	}
	        	$all_pic_id = ltrim($all_pic_id, ",");
	        	$all_pic_id = rtrim($all_pic_id, ",");
	        	$str_pic = array ('attach_ids' => $all_pic_id,);
	        	$data['data'] = serialize($str_pic);
				$data['type'] = "image";
	        }

	        if (substr($content, 0, 1 ) == "#") {//判断此微博是否含有话题
	        	$b = strpos($content,"#");
				$a = strpos($content,"#",1);
				$topic_name = substr($content,$b+1,$a-1);
				$school_id = $this->find_school_id($uid);
				$map_topic['name'] = $topic_name;
				$map_topic['school_id'] = $school_id;
				if (M('weibo_topic')->where($map_topic)->count()) {//查看微博话题表中是否含有该话题
					M('weibo_topic')->where($map_topic)->setInc('weibo_num');//微博数量加1
				} else {
					$data_topic['name'] = $topic_name;
					$data_topic['logo'] = "/topicavatar.png";
					$data_topic['uadmin'] = 0;
					$data_topic['read_count'] = 0;
					$data_topic['is_top'] = 0;
					$data_topic['weibo_num'] = 1;
					$data_topic['status'] = 1;
					$data_topic['school_id'] = $school_id;
					M('weibo_topic')->data($data_topic)->add();
					$topic_id = M('weibo_topic')->where($map_topic)->getField('id');
				}
				//echo $topic_name;
				$new = substr($content,$a+1);//切除话题，取后半部分
				$data['content'] = "[topic:".$topic_id."]".$new;//拼上话题id
	        } else {
	        	$data['content'] = $content;
	        }

			$data['uid'] = $uid;
			$data['from'] = $from;
			$data['create_time'] = time();
			$data['comment_count'] = 0;
			$data['status'] = 1;
			$data['is_top'] = 0;
			$data['repost_count'] = 0;
			if (M('weibo')->data($data)->add()) {//weibo表中插入数据
				$map_weibo['uid'] = $uid;
				$weibo_id = M('weibo')->where($map_weibo)->order("id desc")->getField('id');//该用户最新插入weibo表中的id
				$data_link['weibo_id'] = $weibo_id;
				$data_link['topic_id'] = $topic_id;
				$data_link['status'] = 1;
				$data_link['create_time'] = time();
				$data_link['is_top'] = 0;
				M('weibo_topic_link')->data($data_link)->add();
				$parent['code'] = 0;
				$parent['msg'] = "成功";
			} else {
				$parent['code'] = -1;
				$parent['msg'] = "失败";
			}
			
		} else {
			$parent['code'] = 1;
			$parent['msg'] = "网络繁忙";
		}
		echo json_encode($parent);
	}

	public function repost_weibo(){//转发微博 (weibo_id uid content from)
		if (I("post.weibo_id") && I("post.uid")) {
			$weibo_id = I("post.weibo_id");
			$uid = I("post.uid");
			$content = I("post.content");
			$from = I("post.from");
			$arr = array("source"=> NULL,"sourceId"=>$weibo_id);
			$str = serialize($arr);//序列化

			$data["uid"] = $uid;
			$data["content"] = $content;
			$data["create_time"] = time();
			$data["comment_count"] = 0;
			$data["status"] = 1;
			$data["is_top"] = 0;
			$data["type"] = "repost";
			$data["data"] = $str;
			$data["repost_count"] = 0;
			$data["from"] = $from;
			if (M('weibo')->data($data)->add()) {
				$parent['code'] = 0;
				$parent['msg'] = "成功";
			} else {
				$parent['code'] = -1;
				$parent['msg'] = "失败";
			}
			
		} else {
			$parent['code'] = 1;
			$parent['msg'] = "网络繁忙";
		}
		echo json_encode($parent);	
	}

	public function topic_weibo(){//某一话题的所有微博  (topic_name  uid last_id)
		if (I("get.topic_name")  && I("get.uid")) {
			$uid = I("get.uid");
	    	$mapz["uid"] = $uid;
	    	$type = M("member")->where($mapz)->getField("type");
	    	$map_topic["name"] = I("get.topic_name");
	    	$topic_id = M("weibo_topic")->where($map_topic)->getField("id");
	    	$topic_str = "[topic:".$topic_id."]%";//模糊查找话题  eg:[topic:9]%
			$last_id = (I("get.last_id")) ? I("get.last_id") : 999999999 ;

	    	$result = A('Friends')->goodFriends($uid);
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
							ocenter_weibo.status = 1 and ocenter_weibo.uid in ($result) and ocenter_weibo.content like '$topic_str' and ocenter_weibo.id < $last_id
						ORDER BY
							ocenter_weibo.id DESC LIMIT $this->page_num");
	    	$num = count($all);
	    	if ($num) {
	    		for ($i=0; $i<$num; $i++) {
	    			$map["row"] = $all[$i]["id"];
		    		$map["appname"] = "Weibo";
		    		$all[$i]["likes"] = M("support")->where($map)->count();//support表中查找点赞数量
		    		$all[$i]['likes_status'] = $this->likes_status($all[$i]['id'],$uid);
		    		// $all[$i]["url"] = __ROOT__."/index.php/weibo/index/weibodetail?id=".$all[$i]["id"];//跳转URL
		    		$head_pic = M("avatar")->where("uid=".$all[$i]["uid"])->getField("path");
		    		if ($head_pic != "") {//上传过头像
		    			$all[$i]["head_pic"] = C('MULU')."/Uploads/Avatar".$head_pic;
		    		} else {//没有上传过头像，显示系统默认头像
		    			$all[$i]["head_pic"] = C('MULU')."/Public/images/default_avatar.jpg";
		    		}

		    		if (strstr($all[$i]["content"],"[topic:")) {//查看微博是否带有话题
						$str = explode(']',$all[$i]["content"]);
		    			$first = strpos($all[$i]["content"],"]");
						$all[$i]["content"] = substr($all[$i]["content"],$first+1);//去掉微博内容前面[topic:]
		    			$topic = explode('[topic:',$str[0]);
		    			$topic_id = $topic[1];
		    			$topic_name = M("weibo_topic")->where("id=".$topic_id)->getField("name");
		    			$all[$i]["content"] = "#".$topic_name."#".$all[$i]["content"];
		    		}

		    		if ($all[$i]["type"] == "repost") {//转发的微博
		    			$a = unserialize($all[$i]["data"]);
						$id = $a["sourceId"];//找到原博的id
		    			$mappp["id"] = $id;
		    			$weiboo = M("weibo")->where($mappp)->field("uid,content,type,data,create_time")->find();
		    			$all[$i]["old_content"] = $weiboo["content"];
		    			$all[$i]["orignal_time"] = $weiboo["create_time"];

		    			if (strstr($all[$i]["old_content"],"[topic:")) {//查看微博是否带有话题
			    			$str = explode(']',$all[$i]["old_content"]);
			    			$first = strpos($all[$i]["old_content"],"]");
							$all[$i]["old_content"] = substr($all[$i]["old_content"],$first+1);//去掉微博内容前面[topic:]
			    			$topic = explode('[topic:',$str[0]);
			    			$topic_id = $topic[1];
			    			$topic_name = M("weibo_topic")->where("id=".$topic_id)->getField("name");
			    			$all[$i]["old_content"] = "#".$topic_name."#".$all[$i]["old_content"];
			    		}

		    			if ($weiboo["type"] == "image") {//判断原微博是否含有图片
		    				$pic_id = unserialize($weiboo["data"]);//反序列化找出图片id
	    					$map['id'] = array("in",$pic_id["attach_ids"]);
				    		$pic_path = M("picture")->where($map)->field('path')->select();
				    		$pic_num = count($pic_path);
				    		for($j=0;$j<$pic_num;$j++){//用*把图片路径拼接起来
				    			if($j == 0){
				    				$all[$i]["pic_path"] = C('MULU').$pic_path[$j]["path"];
				    			}else{
				    				$all[$i]["pic_path"] = C('MULU').$all[$i]["pic_path"]."*".C('MULU').$pic_path[$j]["path"];
				    			}
				    		}
		    				$all[$i]["type"] = "imagereport";
		    			}
		    			$all[$i]["old_nickname"] = M("member")->where('uid='.$weiboo["uid"])->getField("nickname");
		    			$old_head_pic = M("avatar")->where("uid=".$weiboo["uid"])->getField("path");
			    		if ($old_head_pic != "") {//上传过头像
			    			$all[$i]["old_head_pic"] = C('MULU')."/Uploads/Avatar".$old_head_pic;
			    		} else {//没有上传过头像，显示系统默认头像
			    			$all[$i]["old_head_pic"] = C('MULU')."/Public/images/default_avatar.jpg";
			    		}
		    		}

		    		if($all[$i]["type"] == "image"){//发的图片微博（原创）
		    			$pic_id = unserialize($all[$i]["data"]);//反序列化找出图片id
	    				$map['id'] = array("in",$pic_id["attach_ids"]);
			    		$pic_path = M("picture")->where($map)->field('path')->select();
			    		$pic_num = count($pic_path);
			    		for($j=0;$j<$pic_num;$j++){//用*把图片路径拼接起来
			    			if($j == 0){
			    				$all[$i]["pic_path"] = C('MULU').$pic_path[$j]["path"];
			    			}else{
			    				$all[$i]["pic_path"] = C('MULU').$all[$i]["pic_path"]."*".C('MULU').$pic_path[$j]["path"];
			    			}
			    		}
		    		}
	    		}
		    	$parentz['code'] = 0;
	    		$parentz['msg'] = "成功";
	    		$parentz['last_id'] = $all[$num-1]['id'];
	    		$parentz['data'] = $all;

	    	} else {
	    		$parentz['code'] = -1;
	    		$parentz['msg'] = "暂无数据";
	    	}
		} else {
			$parentz['code'] = 1;
			$parentz['msg'] = "网络繁忙";
		}
		echo json_encode($parentz);
	}

	public function weibo_report(){//微博举报  (content uid weibo_id)
		if (I("post.content") && I("post.uid") && I("post.weibo_id") && I("post.reason")) {
			$data['content'] = I("post.content");
			$data['reason'] = I("post.reason");
			$data['uid'] = I("post.uid");
			$data['url'] ='Weibo/Index/weiboDetail?id='.I("post.weibo_id");
			$data['data'] = '"{\"weibo-id\":\"'.I("post.weibo_id").'\"}"';
			$data['type'] = '动态/动态';
			$data['create_time'] = time();
			if (M("report")->data($data)->add()) {
				$parent['code'] = -1;
				$parent['msg'] = "提交失败";
			} else {
				$parent['code'] = 0;
				$parent['msg'] = "成功";
			}
		} else {
			$parent['code'] = 1;
			$parent['msg'] = "网络繁忙";
		}
		echo json_encode($parent);
	}

	public function my_likes(){//发出的点赞  (uid last_id)
		$uid = I('get.uid');
		if ($uid) {
			$map['appname'] = 'Weibo';
			$map['uid'] = $uid;
			if (I('get.last_id')) {
				$map['id'] = array('lt',I('get.last_id'));
			}
			$weibo_id = M('support')->where($map)->order('id desc')->field('id,row')->limit($this->page_num)->select();
			$num = count($weibo_id);
			if ($num) {
				for ($i=0; $i < $num; $i++) {
					$all[$i] = $this->wb_one($weibo_id[$i]['row']);
					$all[$i]['id'] = $weibo_id[$i]['id']; 
				}
				$parent['code'] = 0;
				$parent['msg'] = '成功';
				$parent['last_id'] = $all[$num-1]['id'];
				$parent['data'] = $all;
			} else {
				$parent['code'] = -1;
				$parent['msg'] = '暂无数据';
			}
		} else {
			$parent['code'] = 1;
			$parent['msg'] = '网络繁忙';
		}
		echo json_encode($parent);
	} 

	public function my_comment(){//发出的评论   (uid last_id)
		$uid = I('get.uid');
		if ($uid) {
			$map['uid'] = $uid;
			$map['status'] = 1;
			if (I('get.last_id')) {
				$map['id'] = array('lt',I('get.last_id'));
			}
			$weibo_id = M('weibo_comment')->where($map)->order('id desc')->field('id,weibo_id,content,create_time')->limit($this->page_num)->select();
			$num = count($weibo_id);
			if ($num) {
				for ($i=0; $i < $num; $i++) { 
					$all[$i] = $this->wb_one($weibo_id[$i]['weibo_id']);
					$all[$i]['id'] = $weibo_id[$i]['id'];
					$all[$i]['comment_content'] = $weibo_id[$i]['content'];
					$all[$i]['comment_time'] = $weibo_id[$i]['create_time'];
				}
				$parent['code'] = 0;
				$parent['msg'] = '成功';
				$parent['last_id'] = $all[$num-1]['id'];
				$parent['data'] = $all;
			} else {
				$parent['code'] = -1;
				$parent['msg'] = '暂无数据';
			}
			
		} else {
			$parent['code'] = 1;
			$parent['msg'] = '网络繁忙';
		}
		echo json_encode($parent);
	}

	private function wb_one($weibo_id){//发出的评论和点赞的调用
		$s = C('MULU');
		$all = M("")->query("SELECT
								ocenter_weibo.content,
								ocenter_member.nickname,
								ocenter_member.uid,
								ocenter_weibo.type,
								ocenter_weibo.data,
								ocenter_weibo.repost_count,
								ocenter_weibo.comment_count,
								CASE
							WHEN ISNULL(ocenter_avatar.path) THEN
								'$s/Public/images/default_avatar.jpg'
							ELSE
								CONCAT(
									'$s/Uploads/Avatar',
									ocenter_avatar.path
								)
							END AS head_pic,
							 ocenter_weibo.create_time
							FROM
								ocenter_weibo
							INNER JOIN ocenter_member ON ocenter_weibo.uid = ocenter_member.uid
							LEFT JOIN ocenter_avatar ON ocenter_member.uid = ocenter_avatar.uid
							WHERE
								ocenter_weibo.id = $weibo_id");
	
		$zan["row"] = $weibo_id;
		$zan["appname"] = "Weibo";
		$all[0]["likes"] = M("support")->where($zan)->count();//support表中查找点赞数量
		$all[0]['updateDate'] = $this->update_time($all[0]['uid']);

		if (strstr($all[0]["content"],"[topic:")) {//查看微博是否带有话题
			$str = explode(']',$all[0]["content"]);
			$topic = explode('[topic:',$str[0]);
			$topic_id = $topic[1];
			$first = strpos($all[0]["content"],"]");
			$all[0]["content"] = substr($all[0]["content"],$first+1);//去掉微博内容前面[topic:]
			$topic_name = M("weibo_topic")->where("id=".$topic_id)->getField("name");
			$all[0]["content"] = "#".$topic_name."#".$all[0]["content"];
    	}

		if ($all[0]["type"] == "repost") {//转发的微博
			$a = unserialize($all[0]["data"]);
			$id = $a["sourceId"];//找到原博的id
			$maap["id"] = $id;
			$weiboo = M("weibo")->where($maap)->field("uid,content,type,data,create_time")->find();
			$all[0]["old_content"] = $weiboo["content"];
			$all[0]["orignal_time"] = $weiboo["create_time"];

			if (strstr($all[0]["old_content"],"[topic:")) {//查看微博是否带有话题
    			$str = explode(']',$all[0]["old_content"]);
    			$first = strpos($all[0]["old_content"],"]");
				$all[0]["old_content"] = substr($all[0]["old_content"],$first+1);//去掉微博内容前面[topic:]
    			$topic = explode('[topic:',$str[0]);
    			$topic_id = $topic[1];
    			$topic_name = M("weibo_topic")->where("id=".$topic_id)->getField("name");
    			$all[0]["old_content"] = "#".$topic_name."#".$all[0]["old_content"];
    		}

			if ($weiboo["type"] == "image") {//判断原微博是否含有图片
				$pic_id = unserialize($weiboo["data"]);//反序列化找出图片id
	    		$map['id'] = array("in",$pic_id["attach_ids"]);
	    		$pic_path = M("picture")->where($map)->field('path')->select();
	    		$pic_num = count($pic_path);
	    		for($j=0;$j<$pic_num;$j++){//用*把图片路径拼接起来
	    			if($j == 0){
	    				$all[0]["pic_path"] = $s.$pic_path[$j]["path"];
	    			}else{
	    				$all[0]["pic_path"] = $s.$all[0]["pic_path"]."*".$s.$pic_path[$j]["path"];
	    			}
	    		}
				$all[0]["type"] = "imagereport";
			}
			$all[0]["old_nickname"] = M("member")->where('uid='.$weiboo["uid"])->getField("nickname");
			$old_head_pic = M("avatar")->where("uid=".$weiboo["uid"])->getField("path");
    		if ($old_head_pic != "") {//上传过头像
    			$all[0]["old_head_pic"] = C('MULU')."/Uploads/Avatar".$old_head_pic;
    		} else {//没有上传过头像，显示系统默认头像
    			$all[0]["old_head_pic"] = C('MULU')."/Public/images/default_avatar.jpg";
    		}
		}	

		if($all[0]["type"] == "image"){//发的图片微博（原创）
			$pic_id = unserialize($all[0]["data"]);//反序列化找出图片id
	    	$map['id'] = array("in",$pic_id["attach_ids"]);
    		$pic_path = M("picture")->where($map)->field('path')->select();
    		$pic_num = count($pic_path);
    		for($j=0;$j<$pic_num;$j++){//用*把图片路径拼接起来
    			if($j == 0){
    				$all[0]["pic_path"] = $s.$pic_path[$j]["path"];
    			}else{
    				$all[0]["pic_path"] = $s.$all[0]["pic_path"]."*".$s.$pic_path[$j]["path"];
    			}
    		}
		}
		return $all[0];
	}

	private function wb_find($uid,$last_id){//查找某一用户的微博
		if (!$last_id) {//没有传last_id，默认显示最新的几条
			$lastmap['status'] = 1;
			$lastmap['uid'] = $uid;
			$last_id = M("weibo")->where($lastmap)->order('id desc')->getField('id') + 1;
		}
		//echo $last_id;
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
					ocenter_weibo.status = 1 and ocenter_weibo.uid = $uid and ocenter_weibo.id < $last_id
				ORDER BY
					ocenter_weibo.id DESC LIMIT $this->page_num");
    	// AND char_length(ocenter_weibo.content) > 5
    	$num = count($all);
    	for ($i=0; $i<$num; $i++) {
    			$map["row"] = $all[$i]["id"];
	    		$map["appname"] = "Weibo";
	    		$all[$i]["likes"] = M("support")->where($map)->count();//support表中查找点赞数量
	    		// $all[$i]["url"] = __ROOT__."/index.php/weibo/index/weibodetail?id=".$all[$i]["id"];//跳转URL
	    		$head_pic = M("avatar")->where("uid=".$all[$i]["uid"])->getField("path");
	    		if ($head_pic != "") {//上传过头像
	    			$all[$i]["head_pic"] = C('MULU')."/Uploads/Avatar".$head_pic;
	    		} else {//没有上传过头像，显示系统默认头像
	    			$all[$i]["head_pic"] = C('MULU')."/Public/images/default_avatar.jpg";
	    		}

	    		if (strstr($all[$i]["content"],"[topic:")) {//查看微博是否带有话题
					$str = explode(']',$all[$i]["content"]);
	    			$first = strpos($all[$i]["content"],"]");
					$all[$i]["content"] = substr($all[$i]["content"],$first+1);//去掉微博内容前面[topic:]
	    			$topic = explode('[topic:',$str[0]);
	    			$topic_id = $topic[1];
	    			$topic_name = M("weibo_topic")->where("id=".$topic_id)->getField("name");
	    			$all[$i]["content"] = "#".$topic_name."#".$all[$i]["content"];
	    		}

	    		if ($all[$i]["type"] == "repost") {//转发的微博
	    			$a = unserialize($all[$i]["data"]);
					$id = $a["sourceId"];//找到原博的id
	    			$mappp["id"] = $id;
	    			$weiboo = M("weibo")->where($mappp)->field("uid,content,type,data,create_time")->find();
	    			$all[$i]["old_content"] = $weiboo["content"];
	    			$all[$i]["orignal_time"] = $weiboo["create_time"];

	    			if (strstr($all[$i]["old_content"],"[topic:")) {//查看微博是否带有话题
		    			$str = explode(']',$all[$i]["old_content"]);
		    			$first = strpos($all[$i]["old_content"],"]");
						$all[$i]["old_content"] = substr($all[$i]["old_content"],$first+1);//去掉微博内容前面[topic:]
		    			$topic = explode('[topic:',$str[0]);
		    			$topic_id = $topic[1];
		    			$topic_name = M("weibo_topic")->where("id=".$topic_id)->getField("name");
		    			$all[$i]["old_content"] = "#".$topic_name."#".$all[$i]["old_content"];
		    		}

	    			if ($weiboo["type"] == "image") {//判断原微博是否含有图片
	    				$pic_id = unserialize($weiboo["data"]);//反序列化找出图片id
    					$map['id'] = array("in",$pic_id["attach_ids"]);
			    		$pic_path = M("picture")->where($map)->field('path')->select();
			    		$pic_num = count($pic_path);
			    		for($j=0;$j<$pic_num;$j++){//用*把图片路径拼接起来
			    			if($j == 0){
			    				$all[$i]["pic_path"] = C('MULU').$pic_path[$j]["path"];
			    			}else{
			    				$all[$i]["pic_path"] = C('MULU').$all[$i]["pic_path"]."*".C('MULU').$pic_path[$j]["path"];
			    			}
			    		}
	    				$all[$i]["type"] = "imagereport";
	    			}
	    			$all[$i]["old_nickname"] = M("member")->where('uid='.$weiboo["uid"])->getField("nickname");
	    			$old_head_pic = M("avatar")->where("uid=".$weiboo["uid"])->getField("path");
		    		if ($old_head_pic != "") {//上传过头像
		    			$all[$i]["old_head_pic"] = C('MULU')."/Uploads/Avatar".$old_head_pic;
		    		} else {//没有上传过头像，显示系统默认头像
		    			$all[$i]["old_head_pic"] = C('MULU')."/Public/images/default_avatar.jpg";
		    		}
	    		}
	    		if($all[$i]["type"] == "image"){//发的图片微博（原创）
	    			$pic_id = unserialize($all[$i]["data"]);//反序列化找出图片id
    				$map['id'] = array("in",$pic_id["attach_ids"]);
		    		$pic_path = M("picture")->where($map)->field('path')->select();
		    		$pic_num = count($pic_path);
		    		for($j=0;$j<$pic_num;$j++){//用*把图片路径拼接起来
		    			if($j == 0){
		    				$all[$i]["pic_path"] = C('MULU').$pic_path[$j]["path"];
		    			}else{
		    				$all[$i]["pic_path"] = C('MULU').$all[$i]["pic_path"]."*".C('MULU').$pic_path[$j]["path"];
		    			}
		    		}
	    		}
    	}
    	return $all;
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

	private function find_school_id($uid){//根据uid查找所属学校
		$school_id = M('member')->where('uid='.$uid)->getField('school_id');
		if (!$school_id) {//家长身份
			$parent_id = M('member')->where('uid='.$uid)->getField('parent_id');
			$student_id = M('user_patriarch')->where('id='.$parent_id)->getField('student_id');
			$school_id = M('user_student')->where('id='.$student_id)->getField('school_id');
		}
		return $school_id;
	}

	private function likes_status($weibo_id,$uid){//查找自己是否为此微博点过赞
		$map['row'] = $weibo_id;
		$map['appname'] = 'Weibo';
		$map['uid'] = $uid;
		if (M("support")->where($map)->count()) {
			$status = '1';
		} else {
			$status = '0';
		}
		return $status;
	}

	public function _request($curl,$https = true,$method='POST',$data = null){
        //echo $curl;exit;
        $ch = curl_init();  //初始化
        //$this_header = array("content-type: application/x-www-form-urlencoded;charset=UTF-8");
        curl_setopt($ch,CURLOPT_URL,$curl);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$this_header);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); //返回字符串，不直接输出
        //判断是否使用https协议
        if ($https) {
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false); //不做服务器的验证
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2); //服务器证书验证
        }
        //是否是POST请求
        if ($method == 'POST') {
            curl_setopt($ch,CURLOPT_POST,true); //设置为POST请求
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data); //设置POST的请求数据
        }
        $content = curl_exec($ch); //访问指定URL
        curl_close($ch); //关闭cURL释放资源
        return $content;
    }
}