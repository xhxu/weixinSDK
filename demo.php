<?php
/**
*  微信 公众平台消息接口 SDK DEMO
*  @author xhxu xh_xu@qq.com/QQ:7844577
*  @version 1.0.20130103
*/
error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set("Asia/Shanghai");

require_once 'weixin.class.php';

define('TOKEN', 'YourToken');	//微信公众平台自定义接口处设置的 Token
define('DEBUG', true);					//是否调试模式 true/false (开启调试将会把收发的信息写入文件)

$weixin = new weixin(TOKEN,DEBUG);

$weixin->valid();
$weixin->getMsg();
$type = $weixin->msgtype;

if ($type==='text') {
	//用户上行文本信息
	if ($weixin->msg['Content']=='Hello2BizUser') {
		//关注成功后的信息
		$note = '你已经成功关注该公众账号';
	}else{
		$note = '你好,你发的信息是:'.$weixin->msg['Content'];
	}
	$reply = $weixin->makeText($note);

}elseif ($type==='location') {
	//用户上行位置信息
	$note = '您的位置在: '.$weixin->msg['Label'].'坐标是: X:'.$weixin->msg['Location_X'].' Y:'.$weixin->msg['Location_Y'];
	$reply = $weixin->makeText($note);

}elseif ($type==='image') {
	//用户上行图片消息
	//如有必要可以为此条信息标星
	//$weixin->setFlag = true;
	$news['content'] = '这个是图文消息';
	//图文内容条数最大10(多余10条将自动丢弃).当为1条时则为单条图文
	$news['items'] =  array(
			array(
				'title' => '微信 公众平台消息接口 SDK',
				'description' => '微信 公众平台消息接口SDK 说明',
				'picurl' => 'http://www.xhxu.cn/weixin/sdk.jpg',	//图片地址为接口域名下图片
				'url' => 'http://www.xhxu.cn'
			),
			array(
				'title' => '微信 公众平台消息接口 SDK',
				'description' => '微信 公众平台消息接口 SDK 说明2',
				'picurl' => 'http://www.xhxu.cn/weixin/sdk.jpg',
				'url' => 'http://www.xhxu.cn'
			)
	);
	$reply = $weixin->makeNews($news);

}
//输出
$weixin->reply($reply);
