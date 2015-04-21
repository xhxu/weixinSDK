<?php
/**
 *	微信 公众平台消息接口 SDK
 *  @author xhxu xh_xu@qq.com/QQ:7844577
 *  @version 1.0.20130122
 */
class Weixin
{
	public $token = '';
	public $debug =  false;
	public $setFlag = false;
	public $msgtype = 'text';	//('text','image','location')
	Public $msg = array();
	Public $version = '1.0.20130122';
	private $logPath = '';

	public function __construct($token,$debug,$log='./')
	{
		$this->token = $token;
		$this->debug = $debug;
		$this->logPath = $log;
	}
	public function getMsg()
	{
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		if ($this->debug) {
			file_put_contents($this->logPath .'log.txt', $postStr."\n",FILE_APPEND);
		}
		if (!empty($postStr)) {
			$this->msg = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$this->msgtype = strtolower($this->msg['MsgType']);
		}
	}
	public function makeText($text='')
	{
		$CreateTime = time();
		$FuncFlag = $this->setFlag ? 1 : 0;
		$textTpl = "<xml>
			<ToUserName><![CDATA[{$this->msg['FromUserName']}]]></ToUserName>
			<FromUserName><![CDATA[{$this->msg['ToUserName']}]]></FromUserName>
			<CreateTime>{$CreateTime}</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[%s]]></Content>
			<FuncFlag>%s</FuncFlag>
			</xml>";
		return sprintf($textTpl,$text,$FuncFlag);
	}
	public function makeNews($newsData=array())
	{
		$CreateTime = time();
		$FuncFlag = $this->setFlag ? 1 : 0;
		$newTplHeader = "<xml>
			<ToUserName><![CDATA[{$this->msg['FromUserName']}]]></ToUserName>
			<FromUserName><![CDATA[{$this->msg['ToUserName']}]]></FromUserName>
			<CreateTime>{$CreateTime}</CreateTime>
			<MsgType><![CDATA[news]]></MsgType>
			<Content><![CDATA[%s]]></Content>
			<ArticleCount>%s</ArticleCount><Articles>";
		$newTplItem = "<item>
			<Title><![CDATA[%s]]></Title>
			<Description><![CDATA[%s]]></Description>
			<PicUrl><![CDATA[%s]]></PicUrl>
			<Url><![CDATA[%s]]></Url>
			</item>";
		$newTplFoot = "</Articles>
			<FuncFlag>%s</FuncFlag>
			</xml>";
		$Content = '';
		$itemsCount = count($newsData['items']);
		$itemsCount = $itemsCount < 10 ? $itemsCount : 10;
		if ($itemsCount) {
			foreach ($newsData['items'] as $key => $item) {
				if ($key<=9) {
					$Content .= sprintf($newTplItem,$item['title'],$item['description'],$item['picurl'],$item['url']);
				}
			}
		}
		$header = sprintf($newTplHeader,$newsData['content'],$itemsCount);
		$footer = sprintf($newTplFoot,$FuncFlag);
		return $header . $Content . $footer;
	}
	public function reply($data)
	{
		if ($this->debug) {
			file_put_contents($this->logPath .'reply.txt', $data."\n",FILE_APPEND);
		}
		echo $data;
	}
	public function valid()
	{
		if ($this->checkSignature()) {
			if( $_SERVER['REQUEST_METHOD']=='GET' )
			{
				echo $_GET['echostr'];
				exit;
			}
		}else{
			file_put_contents($this->logPath .'log.txt', 'valid fild'."\n",FILE_APPEND);
			exit;
		}
	}
	private function checkSignature()
	{
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];

		$tmpArr = array($this->token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );

		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}
