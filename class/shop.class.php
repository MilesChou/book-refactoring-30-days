<?php

class shop {
	/**
	 * $_CLOUMN
	 * 可接受查詢的欄位
	 */
	private $_CLOUMN = array('id', 'category', 'title', 'content', 'price', 'store', 'sale', 'click');
	/**
	 * $_LIKE_QUERY
	 * 可接受模糊比對的欄位
	 */
	private $_LIKE_QUERY = array('title', 'content');
	/**
	 * $_NUM_QUERY
	 * 可接受數字比對的欄位
	 */
	private $_NUM_QUERY = array('id', 'category', 'price', 'store', 'sale', 'click');
	/**
	 * $_QUERY_OP
	 * 可接受的比對運算元
	 */
	private $_QUERY_OP = array('like' => 'LIKE',
		'lt' => '<',
		'le' => '<=',
		'gt' => '>',
		'ge' => '>=',
		'ne' => '<>',
		'eq' => '=',
		'sne' => '<>',
		'seq' => '=');
	/**
	 * $_debug
	 * 除錯模式
	 */
	private $_debug = False;
	/**
	 * $_db
	 * 放DB物件的成員
	 */
	private $_db = Null;
	/**
	 * $data
	 * 資料
	 */
	private $data = Null;
	/**
	 * shop::__construct(boolen $DEBUG_MODE)
	 * 建構方法
	 * 可傳入一布林，這會決定之後的使用是否會開啟除錯模式
	 * 建構的同時，會建立DB物件，DB物件使用方法請參考class/mysql.class.php
	 */
	public function __construct($DEBUG_MODE) {
		$this->_debug = $DEBUG_MODE;
		if ($this->_db == Null) {
			$this->_db = new db($this->_debug);
		}
		return;
	}
	/**
	 * 解構方法
	 */
	public function __destruct() {
	}
	/**
	 * 取得所有項目的方法
	 */
	public function all() {
		$SQL = 'SELECT `p`.*, `c`.`title` AS `ctitle` FROM `product` AS `p` JOIN `product_category` AS `c` ON `p`.`category` = `c`.`id` ORDER BY `p`.`id`';
		return $this->_db->all($SQL);
	}
	/**
	 * shop::one(int $id)
	 * 取得單一項目的方法
	 * 參數：$id為指定產品資料表的id欄位值
	 */
	public function one($id) {
		$data = array('query'=>'id', 'op'=>'eq', 'val'=>$id);
		$one = $this->query($data);
		$this->_db->update(array(array(Null),array('id'),array($id)), array('click'=> ++$one[0]['click']), 'product');
		return $one[0];
	}
	/**
	 * shop::query(array $data)
	 * 處理分類的方法
	 * 參數：$data = array(
	 * 	'query' => {欲查詢的欄位},
	 * 	'op' => {比對方法},
	 * 	'val' => {比對值}
	 * 	[,
	 * 	'per' => {一次提取資料筆數},
	 * 	'page' => {提取資料的頁碼} ]);
	 */
	public function query($data) {
		$column = '';
		$where = '';
		$limit = '';
		$order = '`p`.`id`';
		if (isset($data['query']) && isset($data['op']) && isset($data['val'])) {
			if ($this->checkQuery($data) === False) {
				die('查詢資料輸入有誤！');
			}
			if ($data['op'] == 'like') { $data['val'] = '%' . $data['val'] . '%';}
			$where = 'WHERE `p`.`' . $data['query'] . '` ' . $this->_QUERY_OP[$data['op']] . ' \'' . $this->_db->escape($data['val']) . '\'';
		}
		if (isset($data['per'])) { 
			$page = (isset($data['page']))? $data['page']: 1;
			$limit = 'LIMIT ' . (($page-1) * $data['per']) . ' , ' . $data['per'];
		}
		if (isset($data['order'])) {
			$order = $data['order'];
		}
		if (isset($data['column'])) {
			foreach ($data['column'] as $value) {
				$column .= ', ' . $value;
			}
		}
		$SQL = 'SELECT `p`.*, `c`.`title` AS `ctitle` ' . $column . ' FROM `product` AS `p` JOIN `product_category` AS `c` ON `p`.`category` = `c`.`id` ' . $where . ' ORDER BY ' . $order . ' ' . $limit;
		return $this->_db->all($SQL);
	}
	/**
	 * shop::top(int $per)
	 */
	public function top($per) {
		$data['popular_top'] = $this->query(array(
			'per' => $per,
			'order'=> '`p`.`click` DESC'
		));
		$data['popular_bot'] = $this->query(array(
			'per' => $per,
			'order'=> '`p`.`click` ASC'
		));
		$data['sale_top'] = $this->query(array(
			'per' => $per,
			'order'=> '`p`.`sale` DESC'
		));
		$data['sale_bot'] = $this->query(array(
			'per' => $per,
			'order'=> '`p`.`sale` ASC'
		));
		$data['store'] = $this->query(array(
			'per' => $per,
			'order'=> '`p`.`store` ASC'
		));
		$data['profit'] = $this->query(array(
			'column' => array('(`p`.`price` / `p`.`cost`) AS `profit`'),
			'per' => $per,
			'order'=> '`profit` DESC'
		));
		return $data;
	}
	public function calc() {
		$calc = array();
		$SQL = 'SELECT sum(`total`) FROM `order` WHERE `_checkout` = 1';
		$calc['sale'] = $this->_db->one($SQL);
		return $calc;
	}
	/**
	 * shop::checkQuery(array $data)
	 * 查詢資料驗證
	 * 會回傳True或False
	 * 參數$data使用方法與shop::query同
	 */
	public function checkQuery($data) {
		switch ($data['op']) {
			case 'like':
				if(!in_array($data['query'], $this->_LIKE_QUERY)) {
					return False;
				}
				break;
			case 'lt':
			case 'le':
			case 'gt':
			case 'ge':
			case 'ne':
			case 'eq':
				if(!in_array($data['query'], $this->_NUM_QUERY)) {
					return False;
				}
				if(!is_long((int)$data['val'])) {
					return False;
				}
				break;
			default:
				return False;
		}
		return True;
	}
	/**
	 * shop::cart(array $data)
	 * 處理購物車的方法
	 * 參數$data = array('op'=>{處理事件})
	 */
	public function shop_action($op, $data = Null, $id = Null) {
		if (!isset($_SESSION['cart'])) {
			$_SESSION['cart'] = array();
		}
		switch ($op) {
			case 'view':
				break;
			case 'add':
				if ($data['pic'] != Null ) {
					if (!preg_match("/(\..*)$/",$data['pic'],$match)) { die('上傳檔案錯誤');}
					$data['pic'] = $this->_db->get_insert_id('product') . $match[1];
					if (!move_uploaded_file($_FILES['pic']['tmp_name'], 'img/' . $data['pic'])) {
						die('檔案移動錯誤');
					}
				}
				return $this->_db->insert($data, 'product');
			case 'upd':
				$one = $this->one($id);
				if ($data['pic'] != Null ) {
					if (!preg_match("/(\..*)$/",$data['pic'],$match)) { die('上傳檔案錯誤');}
					if ($one['pic'] != Null) { unlink('img/' . $one['pic']);}
					$data['pic'] = $one['id'] . $match[1];
					if (!move_uploaded_file($_FILES['pic']['tmp_name'], 'img/' . $data['pic'])) {
						die('檔案移動錯誤');
					}
				} else {
					$data['pic'] = $one['pic'];
				}
				return $this->_db->update(array(array(Null),array('id'),array($id)), $data, 'product');
			case 'del':
				return $this->_db->delete(array(array(Null),array('id'),array($id)), $data, 'product');
			case 'cadd':
				die();
				return $this->_db->delete(array(array(Null),array('id'),array($id)), $data, 'product_category');
				break;
			case 'cupd':
				return $this->_db->update(array(array(Null),array('id'),array($id)), $data, 'product_category');
				break;
			case 'cdel':
				return $this->_db->delete(array(array(Null),array('id'),array($id)), $data, 'product_category');
				break;
		}
		$cart_data['cart'] = $_SESSION['cart'];
		$cart_data['total'] = $this->_cart_total($_SESSION['cart']);
		return $cart_data;
	}
	/**
	 * shop::all_category()
	 * 取得所有分類的方法
	 */
	public function all_category() {
		$SQL = 'SELECT * FROM `product_category` AS `c` ORDER BY `c`.`id`';
		return $this->_db->all($SQL);
	}
	/**
	 * shop::one_category(int $id)
	 * 取得單一項目的方法
	 * 參數：$id為指定產品資料表的id欄位值
	 */
	public function one_category($id) {
		$SQL = 'SELECT * FROM `product_category` AS `c` WHERE `id` = ' . $id;
		return $this->_db->row($SQL);
	}
	/**
	 * shop::cart_action(string $op, array $data)
	 * 處理購物車的方法
	 */
	public function cart_action($op, $data = Null) {
		$cart_data = array();
		if (!isset($_SESSION['cart'])) {
			$_SESSION['cart'] = array();
		}
		switch ($op) {
			case 'view':
				$cart_data['cart'] = $_SESSION['cart'];
				$cart_data['total'] = $this->_cart_total($_SESSION['cart']);
				break;
			case 'add':
				$data['amount'] = (isset($_SESSION['cart'][$data['id']])) ? $_SESSION['cart'][$data['id']]['amount'] + 1: 1;
				$_SESSION['cart'][$data['id']] = $data;
				echo $this->show_alert('已加入購物車', 'index.php?act=cart&op=view');
				$cart_data['cart'] = $_SESSION['cart'];
				$cart_data['total'] = $this->_cart_total($_SESSION['cart']);
				break;
			case 'upd':
				$_SESSION['cart'][$data['id']] = $data;
				echo $this->show_alert('項目已更新', 'index.php?act=cart&op=view');
				$cart_data['cart'] = $_SESSION['cart'];
				$cart_data['total'] = $this->_cart_total($_SESSION['cart']);
				break;
			case 'del':
				unset($_SESSION['cart'][$data['id']]);
				echo $this->show_alert('項目已刪除', 'index.php?act=cart&op=view');
				$cart_data['cart'] = $_SESSION['cart'];
				$cart_data['total'] = $this->_cart_total($_SESSION['cart']);
				break;
			case 'submit':
				$this->_cart_submit($data);
				echo $this->show_alert('訂單已送出', Null);
				session_destroy();
				break;
			case 'query':
				$result = $this->order_action($op, $data);
				$cart_data['cart'] = $this->_analysis_order($result['data']);
				$cart_data['total'] = $result['total'];
				$cart_data['user'] = $result;
				break;
			case 'clear':
				session_destroy();
				echo $this->show_alert('訂單已清除', 'index.php?act=cart&op=view');
				break;
		}
		return $cart_data;
	}
	/**
	 * shop::_cart_total(array $data)
	 * 購物車計算總合的方法
	 */
	private function _cart_total($data) {
		$total = 0;
		foreach ($data as $value) {
			$total += $value['price'] * $value['amount'];
		}
		return $total;
	}
	/**
	 * shop::_cart_submit(array $data)
	 * 購物車提交的方法
	 */
	private function _cart_submit($data) {
		$data['total'] = $this->_cart_total($_SESSION['cart']);
		foreach ($_SESSION['cart'] as $value) {
			$data['data'] .= ($data['data']) ?'|' . $value['id'] . ':' . $value['amount'] :$value['id'] . ':' . $value['amount'];
		}
		if ($this->_db->insert($data, 'order') === False) $this->show_alert('訂單送出失敗！請返回操作！', BACK);
		return;
	}
	/**
	 * shop::show_alert(string $msg, string $url)
	 * 顯示訊息的方法
	 */
	public function show_alert($msg = Null, $url = Null) {
		if ($msg != Null) { $msg = 'alert(\'' . $msg . '\');'; }
		switch ($url) {
			case 'BACK':
				$url = 'history.go(-1);';
				break;
			case null:
				$url = '';
				break;
			default:
				$url = 'location.href = \'' . $url . '\';';
		}
		return '<script>' . $msg . $url . '</script>';
	}
	/**
	 * shop::order_action(string $op, array $data)
	 * 處理購物車的方法
	 */
	public function order_action($op, $data = Null) {
		
		if (!isset($_SESSION['cart'])) {
			$_SESSION['cart'] = array();
		}
		switch ($op) {
			case 'view':
				$SQL = 'SELECT * FROM `order`';
				return $this->_db->all($SQL);
				break;
			case 'info':
				$SQL = 'SELECT * FROM `order` WHERE `id` = '. $data['id'];
				$result = $this->_db->row($SQL);
				$result['data'] = $this->_analysis_order($result['data']);
				return $result;
				break;
			case 'upd':
				$_SESSION['cart'][$data['id']] = $data;
				echo $this->show_alert('項目已更新', 'index.php?act=cart&op=view');
				break;
			case 'del':
				unset($_SESSION['cart'][$data['id']]);
				echo $this->show_alert('項目已刪除', 'index.php?act=cart&op=view');
				break;
			case 'submit':
				$this->_cart_submit($data);
				echo $this->show_alert('訂單已送出', 'index.php');
				session_destroy();
				break;
			case 'query':
				$SQL = 'SELECT * FROM `order` WHERE `sn` = \'' . $data['sn'] . '\'';
				return $this->_db->row($SQL);
				break;
			case 'checkout':
				$SQL = 'UPDATE `order` SET `_checkout` = \'1\' WHERE `id` = \'' . $data['id'] . '\'';
				return $this->_db->query($SQL);
				break;
			case 'clear':
				session_destroy();
				echo $this->show_alert('訂單已清除', 'index.php?act=cart&op=view');
				break;
		}
		return;
	}
	/**
	 * shop::_analysis_order(string $op, array $data)
	 * 購物車訂單分析
	 */
	private function _analysis_order($data) {
		$result = array();
		foreach( explode("|", $data) as $row) {
			$order = explode(":", $row);
			$SQL = 'SELECT `p`.*, `c`.`title` AS `ctitle` FROM `product` AS `p` JOIN `product_category` AS `c` ON `p`.`category` = `c`.`id` WHERE `p`.`id` = ' . $order[0];
			$order_data = $this->_db->row($SQL);
			$order_data['amount'] = $order[1];
			$result[] = $order_data;
		}
		return $result;
	}
}