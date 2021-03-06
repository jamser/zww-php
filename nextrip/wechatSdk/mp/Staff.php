<?php

namespace WechatSdk\mp;

use WechatSdk\mp\Messages\BaseMessage;

/**
 * 客服
 */
class Staff {

	/**
	 * 消息
	 *
	 * @var \WechatSdk\mp\Messages\BaseMessage;
	 */
	protected $message;

	/**
	 * 指定消息发送客服账号
	 *
	 * @var string
	 */
	protected $by;

	/**
	 * 请求的headers
	 *
	 * @var array
	 */
	protected $headers = array('content-type:application/json');

	const API_GET = 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist';
	const API_ONLINE = 'https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist';
	const API_DELETE = 'https://api.weixin.qq.com/customservice/kfaccount/del';
	const API_UPDATE = 'https://api.weixin.qq.com/customservice/kfaccount/update';
	const API_CREATE = 'https://api.weixin.qq.com/customservice/kfaccount/add';
	const API_MESSAGE_SEND = 'https://api.weixin.qq.com/cgi-bin/message/custom/send';
	const API_AVATAR_UPLOAD = 'http://api.weixin.qq.com/customservice/kfaccount/uploadheadimg';

	/**
	 * Http对象
	 *
	 * @var Http
	 */
	protected $http;

	/**
	 * constructor
	 *
	 * @param string $appId
	 * @param string $appSecret
	 */
	public function __construct($appId, $appSecret) {
		$this->http = new Http(new AccessToken($appId, $appSecret));
	}

	/**
	 * 获取所有的客服
	 *
	 * @return array
	 */
	public function lists() {
		$response = $this->http->get(self::API_GET);

		return $response['kf_list'];
	}

	/**
	 * 获取所有在线的
	 *
	 * @return array
	 */
	public function onlines() {
		$response = $this->http->get(self::API_GET);

		return $response['kf_online_list'];
	}

	/**
	 * 添加客服账号
	 *
	 * @param string $email
	 * @param string $nickname
	 * @param string $password
	 *
	 * @return bool
	 */
	public function create($email, $nickname, $password) {
		$params = array(
				'kf_account' => $email,
				'nickname' => $nickname,
				'password' => $password,
		);

		return $this->http->jsonPost(self::API_CREATE, $params);
	}

	/**
	 * 修改客服账号
	 *
	 * @param string $email
	 * @param string $nickname
	 * @param string $password
	 *
	 * @return bool
	 */
	public function update($email, $nickname, $password) {
		$params = array(
				'kf_account' => $email,
				'nickname' => $nickname,
				'password' => $password,
		);

		return $this->http->jsonPost(self::API_UPDATE, $params);
	}

	/**
	 * 删除客服账号
	 *
	 * @param string $email
	 * @param string $nickname
	 * @param string $password
	 *
	 * @return bool
	 */
	public function delete($email, $nickname, $password) {
		$params = array(
				'kf_account' => $email,
				'nickname' => $nickname,
				'password' => $password,
		);

		return $this->http->jsonPost(self::API_UPDATE, $params);
	}

	/**
	 * 上传头像
	 *
	 * @param string $email
	 * @param string $path
	 *
	 * @return bool
	 */
	public function avatar($email, $path) {
		$options = array(
				'files' => array(
						'media' => $path,
				),
		);

		$url = self::API_AVATAR_UPLOAD . "?kf_account={$email}";

		return $this->http->jsonPost($url, array(), $options);
	}

	/**
	 * 准备消息
	 *
	 * @param \WechatSdk\mp\Messages\BaseMessage $message
	 *
	 * @return Staff
	 */
	public function send($message) {
		is_string($message) && $message = Message::make('text')->with('content', $message);

		if (!$message instanceof BaseMessage) {
			throw new Exception("消息必须继承自 'WechatSdk\mp\BaseMessage'");
		}

		$this->message = $message;

		return $this;
	}

	/**
	 * 指定客服
	 *
	 * @param string $account
	 *
	 * @return Staff
	 */
	public function by($account) {
		if (empty($this->message)) {
			throw new Exception('未设置要发送的消息');
		}

		$this->message->staff = $account;

		return $this;
	}

	/**
	 * 发送消息
	 *
	 * @param string $openId
	 *
	 * @return bool
	 */
	public function to($openId) {
		if (empty($this->message)) {
			throw new Exception('未设置要发送的消息');
		}

		$this->message->to = $openId;

		return $this->http->jsonPost(self::API_MESSAGE_SEND, $this->message->buildForStaff());
	}

}
