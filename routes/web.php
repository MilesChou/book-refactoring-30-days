<?php

use App\Shop\Shop;

Route::get('/admin.php', function () {
    ob_start();

    // 引用設定檔
    require __DIR__ . '/../config.php';

    // 建立shop物件
    $shop = new Shop(DEBUG_MODE);

    // $_GET['act'] 如沒有設定的話，預設值為'main'
    if (!isset($_GET['act'])) {
        $_GET['act'] = 'main';
    }

    // 依 $_GET['act'] 決定要做何種處理
    switch ($_GET['act']) {
        // 登入頁面
        case 'login':
            $tpl->assign('admin_page', 'admin_login.html');
            break;
        // 檢查頁面
        case 'check':
            if (!isset($_POST['username']) || !isset($_POST['password'])) {
                die($shop->showAlert('帳號密碼輸入有誤', 'BACK'));
            }
            if ($_POST['username'] != $user || md5($_POST['password']) != $pass) {
                die($shop->showAlert('帳號密碼輸入錯誤', 'BACK'));
            } else {
                $_SESSION['login'] = true;
                die($shop->showAlert('登入成功', 'admin.php'));
            }
            break;
        // 產品管理
        case 'shop':
            if (!isset($_SESSION['login']) && !DEBUG_MODE) {
                die($shop->showAlert('請先登入！', 'admin.php?act=login'));
            }
            if (!isset($_GET['op'])) {
                $_GET['op'] = 'view';
            }
            switch ($_GET['op']) {
                default:
                case 'view':
                    // 取得分類資料
                    $tpl->assign('all_category', $shop->allCategory());
                    // 取得所有資料
                    $data = $shop->all();
                    $tpl->assign('all', $data);
                    // $_GET['id'] 沒有設定的話 預設值為all的第一個;
                    if (isset($_GET['id'])) {
                        // 查詢沒有東西的話 會設定為Null
                        if (!$one = $shop->one($_GET['id'])) {
                            die('查無資料');
                        }
                        $tpl->assign('one', $one);
                    }
                    $tpl->assign('admin_page', 'admin_shop.html');
                    break;
                // 條件查詢商品資料
                case 'query':
                    // 檢查傳入值是否有設定
                    if (!isset($_GET['query']) ||
                        !isset($_GET['opera']) ||
                        !isset($_GET['val'])) {
                        die('資料有誤！');
                    }
                    // 設定查詢資料
                    $data = [
                        'query' => $_GET['query'],
                        'op' => $_GET['opera'],
                        'val' => $_GET['val']
                    ];

                    // 取得分類資料
                    $tpl->assign('all_category', $shop->allCategory());
                    // 取得查詢結果
                    $data = $shop->query($data);
                    $tpl->assign('all', $data);
                    // $_GET['id'] 沒有設定的話 預設值為all的第一個;
                    if (isset($_GET['id'])) {
                        // 查詢沒有東西的話 會設定為Null
                        if (!$one = $shop->one($_GET['id'])) {
                            $one = null;
                        }
                        $tpl->assign('one', $one);
                    }
                    // 查詢結果的子樣板：shop_view.html
                    $tpl->assign('admin_page', 'admin_shop.html');
                    break;
                // 新增商品
                case 'add':
                    if (!$_POST['title']) {
                        die($shop->showAlert('標題為必填項目！', 'BACK'));
                    }
                    $data = [];
                    $data['title'] = $_POST['title'];
                    $data['category'] = (isset($_POST['category'])) ? (int)$_POST['category'] : 0;
                    $data['cost'] = (isset($_POST['cost'])) ? (int)$_POST['cost'] : 0;
                    $data['price'] = (isset($_POST['price'])) ? (int)$_POST['price'] : 0;
                    $data['store'] = (isset($_POST['store'])) ? (int)$_POST['store'] : 0;
                    $data['pic'] = (isset($_FILES['pic'])) ? $_FILES['pic']['name'] : null;
                    $data['content'] = (isset($_POST['content'])) ? $_POST['content'] : null;
                    if ($shop->shopAction($_GET['op'], $data)) {
                        die($shop->showAlert('商品已新增', 'admin.php?act=shop&op=view'));
                    } else {
                        die($shop->showAlert('商品新增失敗', 'BACK'));
                    }
                    break;
                // 更新商品
                case 'upd':
                    if (!isset($_GET['id'])) {
                        die($shop->showAlert('資料錯誤！', 'BACK'));
                    }
                    if (!$data = $shop->one($_GET['id'])) {
                        die($shop->showAlert('查無資料！', 'BACK'));
                    }
                    if (!$_POST['title']) {
                        die($shop->showAlert('標題為必填項目！', 'BACK'));
                    }

                    $data = [];
                    $data['title'] = $_POST['title'];
                    $data['category'] = (isset($_POST['category'])) ? (int)$_POST['category'] : 0;
                    $data['cost'] = (isset($_POST['cost'])) ? (int)$_POST['cost'] : 0;
                    $data['price'] = (isset($_POST['price'])) ? (int)$_POST['price'] : 0;
                    $data['store'] = (isset($_POST['store'])) ? (int)$_POST['store'] : 0;
                    $data['pic'] = ($_FILES['pic']['name'] != null) ? $_FILES['pic']['name'] : null;
                    $data['content'] = (isset($_POST['content'])) ? $_POST['content'] : null;
                    if ($shop->shopAction($_GET['op'], $data, $_GET['id'])) {
                        die($shop->showAlert('商品已更新', 'admin.php?act=shop&op=view&id=' . $_GET['id']));
                    } else {
                        die($shop->showAlert('商品更新失敗', 'BACK'));
                    }
                    break;
                // 刪除商品
                case 'del':
                    if (!isset($_GET['id'])) {
                        die($shop->showAlert('資料錯誤！', 'BACK'));
                    }
                    if ($shop->shopAction($_GET['op'], $data, $_GET['id'])) {
                        die($shop->showAlert('商品已刪除', 'admin.php?act=shop&op=view'));
                    } else {
                        die($shop->showAlert('商品刪除失敗', 'BACK'));
                    }
                    break;
                // 新增分類
                case 'cadd':
                    if (!isset($_POST['id'])) {
                        die($shop->showAlert('標題為必填項目！', 'BACK'));
                    }
                    $data['title'] = $_POST['title'];
                    if ($shop->shopAction($_GET['op'], $data, $_GET['id'])) {
                        die($shop->showAlert('分類已新增', 'admin.php?act=shop&op=view'));
                    } else {
                        die($shop->showAlert('分類新增失敗', 'BACK'));
                    }
                    break;
                // 更新分類
                case 'cupd':
                    if (!isset($_GET['id'])) {
                        die($shop->showAlert('資料錯誤！', 'BACK'));
                    }
                    if (!isset($_POST['id'])) {
                        die($shop->showAlert('標題為必填項目！', 'BACK'));
                    }
                    if (!$data = $shop->oneCategory($_GET['id'])) {
                        die($shop->showAlert('查無資料！', 'BACK'));
                    }
                    $data['title'] = $_POST['title'];
                    if ($shop->shopAction($_GET['op'], $data, $_GET['id'])) {
                        die($shop->showAlert('分類已更新', 'admin.php?act=shop&op=view'));
                    } else {
                        die($shop->showAlert('分類更新失敗', 'BACK'));
                    }
                    break;
                // 刪除分類
                case 'cdel':
                    if (!isset($_GET['id'])) {
                        die($shop->showAlert('資料錯誤！', 'BACK'));
                    }
                    if ($shop->shopAction($_GET['op'], $data, $_GET['id'])) {
                        die($shop->showAlert('分類已刪除', 'admin.php?act=shop&op=view'));
                    } else {
                        die($shop->showAlert('商品刪除失敗', 'BACK'));
                    }
                    break;
            }
            break;
        // 訂單管理
        case 'order':
            if (!isset($_SESSION['login']) && !DEBUG_MODE) {
                die($shop->showAlert('請先登入！', 'admin.php?act=login'));
            }
            // 檢查傳入值是否有設定
            if (!isset($_GET['op'])) {
                $_GET['op'] = 'view';
            }
            // 設定傳入參數
            switch ($_GET['op']) {
                // 檢視
                default:
                case 'view':
                    // 取得結果
                    $tpl->assign('order_data', $shop->orderAction($_GET['op']));
                    // 子樣板：admin_order.html
                    $tpl->assign('admin_page', 'admin_order.html');
                    break;
                // 詳細內容
                case 'info':
                    $data = null;
                    // 取得結果
                    if (!isset($_GET['id'])) {
                        die('無輸入資料！');
                    }
                    $tpl->assign('order', $shop->orderAction($_GET['op'], ['id' => $_GET['id']]));
                    // 子樣板：admin_orderInfo.html
                    $tpl->assign('admin_page', 'admin_orderInfo.html');
                    break;
                // 刪除
                case 'del':
                    if (!isset($_GET['id'])) {
                        die('資料輸入錯誤！');
                    }
                    if (!$data = $shop->one($_GET['id'])) {
                        die('查無資料！');
                    }
                    break;
                //
                case 'submit':
                    break;
                // 結帳
                case 'checkout':
                    if (!isset($_GET['id'])) {
                        die('資料輸入錯誤！');
                    }
                    if ($shop->orderAction($_GET['op'], ['id' => $_GET['id']])) {
                        die($shop->showAlert('已結帳！', 'admin.php?act=order'));
                    } else {
                        die($shop->showAlert('處理錯誤！', 'BACK'));
                    }
                    break;
            }
            break;
        // 管理主頁面
        case 'main':
        default:
            if (!isset($_SESSION['login']) && !DEBUG_MODE) {
                die($shop->showAlert('請先登入！', 'admin.php?act=login'));
            }
            // 取得所有資料
            $tpl->assign('top', $shop->top(PER_TOP_LIST));
            $tpl->assign('calc', $shop->calc());
            break;
    }

    // 主頁面的子樣板：admin.html
    $tpl->assign('tplContent', 'admin.html');

    // 主樣版：index.blade.php
    $tpl->display('index.html');

    return ob_get_clean();
});

Route::get('/', function () {
    ob_start();

    // 引用設定檔
    require __DIR__ . '/../config.php';

    // 建立shop物件
    $shop = new Shop(DEBUG_MODE);

    // $_GET['act'] 如沒有設定的話，預設值為'main'
    if (!isset($_GET['act'])) {
        $_GET['act'] = 'main';
    }

    // 依 $_GET['act'] 決定要做何種處理
    switch ($_GET['act']) {
        case 'contact':
            return view('shop.contact');
        // 條件查詢產品資料
        case 'query':
            // 檢查傳入值是否有設定
            if (!isset($_GET['query']) ||
                !isset($_GET['opera']) ||
                !isset($_GET['val'])) {
                die('資料有誤！');
            }

            // 設定查詢資料
            $data = [
                'query' => $_GET['query'],
                'op' => $_GET['opera'],
                'val' => $_GET['val']
            ];

            // 取得分類資料
            $tpl->assign('all_category', $shop->allCategory());

            // 取得查詢結果
            $data = $shop->query($data);

            $tpl->assign('all', $data);
            // $_GET['id'] 有設定的話，即會在樣版的最上面顯示;
            if (isset($_GET['id'])) {
                // 查詢沒有東西的話 會設定為Null
                if (!$one = $shop->one($_GET['id'])) {
                    $one = null;
                }
                // 將查詢結果傳到樣版的$one變數
                $tpl->assign('one', $one);
            }
            // 查詢結果的子樣板：shop_view.html
            $tpl->assign('tplContent', 'shop.html');

            break;
        // 購物車處理
        case 'cart':
            // 檢查傳入值是否有設定
            if (!isset($_GET['op'])) {
                $_GET['op'] = 'view';
            }
            // 設定傳入參數
            switch ($_GET['op']) {
                default:
                case 'view':
                case 'clear':
                    $data = null;
                    break;
                case 'add':
                    // 加入購物車
                    if (!isset($_GET['id'])) {
                        die('資料輸入錯誤！');
                    }
                    if (!$data = $shop->one($_GET['id'])) {
                        die('查無資料！');
                    }
                    break;
                case 'upd':
                    // 更新購物車
                    if (!isset($_POST['amount']) || !isset($_GET['id'])) {
                        die('無輸入資料！');
                    }
                    if (!is_long((int)$_POST['amount'])) {
                        die('輸入資料錯誤！');
                    }
                    if (!$data = $shop->one($_GET['id'])) {
                        die('查無資料！');
                    }
                    $data['amount'] = (int)$_POST['amount'];
                    break;
                case 'del':
                    // 刪除購物車
                    if (!isset($_GET['id'])) {
                        die('資料輸入錯誤！');
                    }
                    if (!$data = $shop->one($_GET['id'])) {
                        die('查無資料！');
                    }
                    break;
                case 'submit':
                    // 提交
                    if (!isset($_POST['name']) ||
                        !isset($_POST['email']) ||
                        !isset($_POST['phone']) ||
                        !isset($_POST['address'])) {
                        die('個人資料輸入不完整！請重新輸入');
                    }
                    if (empty($_SESSION['cart'])) {
                        die('購物車無資料！');
                    }
                    $sn = md5(uniqid(rand()));
                    $data = ['datetime' => date('Y-m-d H:i:s'),
                        'name' => $_POST['name'],
                        'email' => $_POST['email'],
                        'phone' => $_POST['phone'],
                        'address' => $_POST['address'],
                        'data' => '',
                        'sn' => $sn];
                    $tpl->assign('sn', $sn);
                    break;
                case 'query':
                    // 查詢已提交的資料
                    if (!isset($_GET['sn'])) {
                        die('資料輸入錯誤！');
                    }
                    $data['sn'] = $_GET['sn'];
                    break;
            }
            // 取得結果
            $tpl->assign('data', $shop->cartAction($_GET['op'], $data));
            // 子樣板：shop_cart.html
            $tpl->assign('tplContent', 'shop_cart.html');
            break;
        // 預設頁面/主頁面
        case 'main':
        default:
            ob_get_clean();

            $data = [
                'all' => $shop->all(),
                'all_category' => $shop->allCategory(),
            ];

            if (isset($_GET['id'])) {
                // 查詢沒有東西的話 會設定為Null
                if (!$one = $shop->one($_GET['id'])) {
                    die('查無資料');
                }
                $data['one'] = $one;
            }

            return view('shop.index', $data);
    }
    // 主樣版：index.html
    $tpl->display('index.html');

    return ob_get_clean();
});
