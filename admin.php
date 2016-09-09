<?php

// 引用設定檔
include('config.php');
// 引用shop類別檔
include(CLASS_PATH . 'shop.class.php');
// 建立shop物件
$shop = new shop (DEBUG_MODE);
// $_GET['act'] 如沒有設定的話，預設值為'main'
if (!isset($_GET['act'])) { $_GET['act'] = 'main';}
// 依 $_GET['act'] 決定要做何種處理
switch ($_GET['act']) {
	// 登入頁面
	case 'login':
		$tpl->assign('admin_page', 'admin_login.html');
		break;
	// 檢查頁面
	case 'check':
		if(!isset($_POST['username']) || !isset($_POST['password'])) { die($shop->show_alert('帳號密碼輸入有誤', 'BACK'));}
		if($_POST['username'] != $user || md5($_POST['password']) != $pass) { die($shop->show_alert('帳號密碼輸入錯誤', 'BACK'));}
		else {
			$_SESSION['login'] = True;
			die($shop->show_alert('登入成功', 'admin.php'));
		}
		break;
	// 產品管理
	case 'shop':
		if (!isset($_SESSION['login']) && !DEBUG_MODE) { die($shop->show_alert('請先登入！', 'admin.php?act=login'));}
		if (!isset($_GET['op'])) { $_GET['op'] = 'view';}
		switch ($_GET['op']) {
			default:
			case 'view':
				// 取得分類資料
				$tpl->assign('all_category', $shop->all_category());
				// 取得所有資料
				$data = $shop->all();
				$tpl->assign('all', $data);
				// $_GET['id'] 沒有設定的話 預設值為all的第一個;
				if(isset($_GET['id'])) {
					// 查詢沒有東西的話 會設定為Null
					if(!$one = $shop->one($_GET['id'])) { die('查無資料');}
					$tpl->assign('one', $one);
				}
				$tpl->assign('admin_page', 'admin_shop.html');
				break;
			// 條件查詢商品資料
			case 'query':
				// 檢查傳入值是否有設定
				if(!isset($_GET['query']) ||
					!isset($_GET['opera']) ||
					!isset($_GET['val'])) { die('資料有誤！'); }
				// 設定查詢資料
				$data = array('query'=>$_GET['query'], 'op'=>$_GET['opera'], 'val'=>$_GET['val']);
				// 取得分類資料
				$tpl->assign('all_category', $shop->all_category());
				// 取得查詢結果
				$data = $shop->query($data);
				$tpl->assign('all', $data);
				// $_GET['id'] 沒有設定的話 預設值為all的第一個;
				if(isset($_GET['id'])) {
					// 查詢沒有東西的話 會設定為Null
					if(!$one = $shop->one($_GET['id'])) { $one = Null; }
					$tpl->assign('one', $one);
				}
				// 查詢結果的子樣板：shop_view.html
				$tpl->assign('admin_page', 'admin_shop.html');
				break;
			// 新增商品
			case 'add':
				if(!$_POST['title']) { die($shop->show_alert('標題為必填項目！', 'BACK')); }
				$data = array();
				$data['title'] = $_POST['title'];
				$data['category'] = (isset($_POST['category'])) ? (int)$_POST['category'] : 0;
				$data['cost'] = (isset($_POST['cost'])) ? (int)$_POST['cost'] : 0;
				$data['price'] = (isset($_POST['price'])) ? (int)$_POST['price'] : 0;
				$data['store'] = (isset($_POST['store'])) ? (int)$_POST['store'] : 0;
				$data['pic'] = (isset($_FILES['pic'])) ? $_FILES['pic']['name']: Null;
				$data['content'] = (isset($_POST['content'])) ? $_POST['content'] : Null;
				if ($shop->shop_action($_GET['op'], $data)) {
					die($shop->show_alert('商品已新增', 'admin.php?act=shop&op=view'));
				} else {
					die($shop->show_alert('商品新增失敗', 'BACK'));
				}
				break;
			// 更新商品
			case 'upd':
				if(!isset($_GET['id'])) { die($shop->show_alert('資料錯誤！','BACK')); }
				if(!$data = $shop->one($_GET['id'])) { die($shop->show_alert('查無資料！','BACK')); }
				if(!$_POST['title']) { die($shop->show_alert('標題為必填項目！', 'BACK')); }
				$data = array();
				$data['title'] = $_POST['title'];
				$data['category'] = (isset($_POST['category'])) ? (int)$_POST['category'] : 0;
				$data['cost'] = (isset($_POST['cost'])) ? (int)$_POST['cost'] : 0;
				$data['price'] = (isset($_POST['price'])) ? (int)$_POST['price'] : 0;
				$data['store'] = (isset($_POST['store'])) ? (int)$_POST['store'] : 0;
				$data['pic'] = ($_FILES['pic']['name'] != Null) ? $_FILES['pic']['name']: Null;
				$data['content'] = (isset($_POST['content'])) ? $_POST['content'] : Null;
				if ($shop->shop_action($_GET['op'], $data, $_GET['id'])) {
					die($shop->show_alert('商品已更新', 'admin.php?act=shop&op=view&id=' . $_GET['id']));
				} else {
					die($shop->show_alert('商品更新失敗', 'BACK'));
				}
				break;
			// 刪除商品
			case 'del':
				if(!isset($_GET['id'])) { die($shop->show_alert('資料錯誤！','BACK')); }
				if ($shop->shop_action($_GET['op'], $data, $_GET['id'])) {
					die($shop->show_alert('商品已刪除', 'admin.php?act=shop&op=view'));
				} else {
					die($shop->show_alert('商品刪除失敗', 'BACK'));
				}
				break;
			// 新增分類
			case 'cadd':
				if(!isset($_POST['id'])) { die($shop->show_alert('標題為必填項目！','BACK')); }
				$data['title'] = $_POST['title'];
				if ($shop->shop_action($_GET['op'], $data, $_GET['id'])) {
					die($shop->show_alert('分類已新增', 'admin.php?act=shop&op=view'));
				} else {
					die($shop->show_alert('分類新增失敗', 'BACK'));
				}
				break;
			// 更新分類
			case 'cupd':
				if(!isset($_GET['id'])) { die($shop->show_alert('資料錯誤！','BACK')); }
				if(!isset($_POST['id'])) { die($shop->show_alert('標題為必填項目！','BACK')); }
				if(!$data = $shop->one_category($_GET['id'])) { die($shop->show_alert('查無資料！','BACK')); }
				$data['title'] = $_POST['title'];
				if ($shop->shop_action($_GET['op'], $data, $_GET['id'])) {
					die($shop->show_alert('分類已更新', 'admin.php?act=shop&op=view'));
				} else {
					die($shop->show_alert('分類更新失敗', 'BACK'));
				}
				break;
			// 刪除分類
			case 'cdel':
				if(!isset($_GET['id'])) { die($shop->show_alert('資料錯誤！','BACK')); }
				if ($shop->shop_action($_GET['op'], $data, $_GET['id'])) {
					die($shop->show_alert('分類已刪除', 'admin.php?act=shop&op=view'));
				} else {
					die($shop->show_alert('商品刪除失敗', 'BACK'));
				}
				break;
		}
		break;
	// 訂單管理
	case 'order':
		if(!isset($_SESSION['login']) && !DEBUG_MODE) { die($shop->show_alert('請先登入！', 'admin.php?act=login'));}
		// 檢查傳入值是否有設定
		if(!isset($_GET['op'])) { $_GET['op'] = 'view'; }
		// 設定傳入參數
		switch ($_GET['op']) {
			// 檢視
			default:
			case 'view':
				// 取得結果
				$tpl->assign('order_data', $shop->order_action($_GET['op']));
				// 子樣板：admin_order.html
				$tpl->assign('admin_page', 'admin_order.html');
				break;
			// 詳細內容
			case 'info':
				$data = Null;
				// 取得結果
				if (!isset($_GET['id'])) { die('無輸入資料！'); }
				$tpl->assign('order', $shop->order_action($_GET['op'], array('id' => $_GET['id'])));
				// 子樣板：admin_orderInfo.html
				$tpl->assign('admin_page', 'admin_orderInfo.html');
				break;
			// 刪除
			case 'del':
				if(!isset($_GET['id'])) { die('資料輸入錯誤！'); }
				if(!$data = $shop->one($_GET['id'])) { die('查無資料！'); }
				break;
			//
			case 'submit':
				break;
			// 結帳
			case 'checkout':
				if(!isset($_GET['id'])) { die('資料輸入錯誤！'); }
				if ($shop->order_action($_GET['op'], array('id' => $_GET['id']))){
					die($shop->show_alert('已結帳！', 'admin.php?act=order'));
				} else {
					die($shop->show_alert('處理錯誤！', 'BACK'));
				}
				break;
		}
		break;
	// 管理主頁面
	case 'main':
	default:
		if(!isset($_SESSION['login']) && !DEBUG_MODE) { die($shop->show_alert('請先登入！', 'admin.php?act=login'));}
		// 取得所有資料
		$tpl->assign('top', $shop->top(PER_TOP_LIST));
		$tpl->assign('calc', $shop->calc());
		break;
}
// 主頁面的子樣板：admin.html
$tpl->assign('tplContent', 'admin.html');
// 主樣版：index.html
$tpl->display('index.html');

?>