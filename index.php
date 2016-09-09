<?php

// 引用設定檔
include('config.php');
// 引用shop類別檔
include(CLASS_PATH . 'shop.class.php');
// 建立shop物件
$shop = new shop (DEBUG_MODE);
// $_GET['act'] 如沒有設定的話，預設值為'main'
if (!isset($_GET['act'])) { $_GET['act'] = 'main'; }
// 依 $_GET['act'] 決定要做何種處理
switch ($_GET['act']) {
	case 'contact':
		$tpl->assign('tplContent', 'contact.html');
		break;
	// 條件查詢產品資料
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
		// $_GET['id'] 有設定的話，即會在樣版的最上面顯示;
		if(isset($_GET['id'])) {
			// 查詢沒有東西的話 會設定為Null
			if(!$one = $shop->one($_GET['id'])) { $one = Null; }
			// 將查詢結果傳到樣版的$one變數
			$tpl->assign('one', $one);
		}
		// 查詢結果的子樣板：shop_view.html
		$tpl->assign('tplContent', 'shop.html');
		break;
	// 購物車處理
	case 'cart':
		// 檢查傳入值是否有設定
		if(!isset($_GET['op'])) { $_GET['op'] = 'view'; }
		// 設定傳入參數
		switch ($_GET['op']) {
			default:
			case 'view':
			case 'clear':
				$data = Null;
				break;
			case 'add':
				// 加入購物車
				if(!isset($_GET['id'])) { die('資料輸入錯誤！'); }
				if(!$data = $shop->one($_GET['id'])) { die('查無資料！'); }
				break;
			case 'upd':
				// 更新購物車
				if (!isset($_POST['amount']) || !isset($_GET['id'])) { die('無輸入資料！'); }
				if (!is_long((int)$_POST['amount'])) { die('輸入資料錯誤！'); }
				if(!$data = $shop->one($_GET['id'])) { die('查無資料！'); }
				$data['amount'] = (int)$_POST['amount'];
				break;
			case 'del':
				// 刪除購物車
				if(!isset($_GET['id'])) { die('資料輸入錯誤！'); }
				if(!$data = $shop->one($_GET['id'])) { die('查無資料！'); }
				break;
			case 'submit':
				// 提交
				if(!isset($_POST['name']) ||
					!isset($_POST['email']) ||
					!isset($_POST['phone']) ||
					!isset($_POST['address'])) { die('個人資料輸入不完整！請重新輸入'); }
				if(empty($_SESSION['cart'])) { die('購物車無資料！'); }
				$sn = md5(uniqid(rand()));
				$data = array('datetime' => date('Y-m-d H:i:s'),
					'name' => $_POST['name'],
					'email' => $_POST['email'],
					'phone' => $_POST['phone'],
					'address' => $_POST['address'],
					'data' => '',
					'sn' => $sn);
				$tpl->assign('sn', $sn);
				break;
			case 'query':
				// 查詢已提交的資料
				if(!isset($_GET['sn'])) { die('資料輸入錯誤！'); }
				$data['sn'] = $_GET['sn'];
				break;
		}
		// 取得結果
		$tpl->assign('data', $shop->cart_action($_GET['op'], $data));
		// 子樣板：shop_cart.html
		$tpl->assign('tplContent', 'shop_cart.html');
		break;
	// 預設頁面/主頁面
	case 'main':
	default:
		// 取得分類資料
		$tpl->assign('all_category', $shop->all_category());
		// 取得所有資料
		$data = $shop->all();
		$tpl->assign('all', $data);
		// $_GET['id'] 沒有設定的話 預設值為all的第一個;
		if(isset($_GET['id'])) {
			// 查詢沒有東西的話 會設定為Null
			if(!$one = $shop->one($_GET['id'])) { die('查無資料'); }
			$tpl->assign('one', $one);
		}
		// 主頁面的子樣板：shop.html
		$tpl->assign('tplContent', 'shop.html');
		break;
}
// 主樣版：index.html
$tpl->display('index.html');

?>