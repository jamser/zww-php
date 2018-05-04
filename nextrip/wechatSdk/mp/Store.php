<?php

namespace WechatSdk\mp;

use WechatSdk\utils\Arr;
use WechatSdk\utils\Bag;

/**
 * 门店
 */
class Store {

	/**
	 * Http对象
	 *
	 * @var Http
	 */
	protected $http;

	const API_CREATE = 'http://api.weixin.qq.com/cgi-bin/poi/addpoi';
	const API_GET = 'http://api.weixin.qq.com/cgi-bin/poi/getpoi';
	const API_LIST = 'http://api.weixin.qq.com/cgi-bin/poi/getpoilist';
	const API_UPDATE = 'http://api.weixin.qq.com/cgi-bin/poi/updatepoi';
	const API_DELETE = 'http://api.weixin.qq.com/cgi-bin/poi/delpoi';

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
	 * 获取指定门店信息
	 *
	 * @param int $storeId
	 *
	 * @return WechatSdk\utils\Bag
	 */
	public function get($storeId) {
		$params = array(
				'poi_id' => $storeId,
		);

		$response = $this->http->jsonPost(self::API_GET, $params);

		return new Bag(Arr::get($response, 'business.base_info'));
	}

	/**
	 * 获取用户列表
	 *
	 * @param int $offset
	 * @param int $limit
	 *
	 * @return WechatSdk\utils\Bag
	 */
	public function lists($offset = 0, $limit = 10) {
		$params = array(
				'begin' => $offset,
				'limit' => $limit,
		);

		$stores = $this->http->jsonPost(self::API_LIST, $params);

		return Arr::fetch($stores['business_list'], 'base_info');
	}

	/**
	 * 创建门店
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function create(array $data) {
		$params = array(
				'business' => array(
						'base_info' => $data,
				),
		);

		return $this->http->jsonPost(self::API_CREATE, $params);
	}

	/**
	 * 更新门店
	 *
	 * @param int   $storeId
	 * @param array $data
	 *
	 * @return bool
	 */
	public function update($storeId, array $data) {
		$data = array_merge($data, array('poi_id' => $storeId));

		$params = array(
				'business' => array(
						'base_info' => $data,
				),
		);

		return $this->http->jsonPost(self::API_UPDATE, $params);
	}

	/**
	 * 删除门店
	 *
	 * @param int $storeId
	 *
	 * @return bool
	 */
	public function delete($storeId) {
		$params = array(
				'poi_id' => $storeId,
		);

		return $this->http->jsonPost(self::API_DELETE, $params);
	}

}
