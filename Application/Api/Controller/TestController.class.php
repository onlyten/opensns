<?php
namespace Api\Controller;
use Think\Controllerr;
class TestController extends Controllerr {
	public function __construct(){
		$token = I("get.accessToken");
		if ($this->check($token)){

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
		if ($pan == "") {
			return 0;
		} else {
			return 1;
		}		
	}
}