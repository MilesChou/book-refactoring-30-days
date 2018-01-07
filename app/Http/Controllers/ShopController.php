<?php

namespace App\Http\Controllers;

use App\Shop\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request, Shop $shop, \Smarty $tpl)
    {
        ob_start();

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

                if (isset($_GET['id'])) {
                    // 查詢沒有東西的話 會設定為Null
                    if (!$one = $shop->one($_GET['id'])) {
                        die('查無資料');
                    }
                    $data['one'] = $one;
                }

                $data = [
                    'all' => $shop->query([
                        'query' => $_GET['query'],
                        'op' => $_GET['opera'],
                        'val' => $_GET['val']
                    ]),
                    'all_category' => $shop->allCategory(),
                ];

                return view('shop.index', $data);
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
    }
}
