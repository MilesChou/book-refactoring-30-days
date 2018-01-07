<?php

namespace App\Shop;

use App\Product;
use App\ProductCategory;
use Illuminate\Database\Eloquent\Collection;

class Shop
{
    /**
     * 可接受查詢的欄位
     */
    private $_CLOUMN = ['id', 'category', 'title', 'content', 'price', 'store', 'sale', 'click'];

    /**
     * 可接受模糊比對的欄位
     */
    private $_LIKE_QUERY = ['title', 'content'];

    /**
     * 可接受數字比對的欄位
     */
    private $_NUM_QUERY = ['id', 'category', 'price', 'store', 'sale', 'click'];

    /**
     * 可接受的比對運算元
     */
    private $_QUERY_OP = ['like' => 'LIKE',
        'lt' => '<',
        'le' => '<=',
        'gt' => '>',
        'ge' => '>=',
        'ne' => '<>',
        'eq' => '=',
        'sne' => '<>',
        'seq' => '='];

    /**
     * 放DB物件的成員
     */
    private $_db;

    /**
     * @param bool $debug
     */
    public function __construct($debug)
    {
        $this->_db = new Mysql($debug);
    }

    /**
     * 取得所有項目的方法
     *
     * @return array
     */
    public function all()
    {
        $SQL = 'SELECT `p`.*, `c`.`title` AS `ctitle` FROM `product` AS `p` JOIN `product_category` AS `c` ON `p`.`category` = `c`.`id` ORDER BY `p`.`id`';
        return $this->_db->all($SQL);
    }

    /**
     * 取得單一項目的方法
     * 參數：$id為指定產品資料表的id欄位值
     *
     * @param string $id
     * @return Product
     */
    public function one($id): Product
    {
        /** @var Product $product */
        $product = Product::find($id);
        $product->click++;
        $product->save();

        return $product;
    }

    /**
     * shop::query(array $data)
     * 處理分類的方法
     * 參數：$data = array(
     *  'query' => {欲查詢的欄位},
     *  'op' => {比對方法},
     *  'val' => {比對值}
     *  [,
     *  'per' => {一次提取資料筆數},
     *  'page' => {提取資料的頁碼} ]);
     */
    public function query($data)
    {
        $column = '';
        $where = '';
        $limit = '';
        $order = '`p`.`id`';
        if (isset($data['query']) && isset($data['op']) && isset($data['val'])) {
            if ($this->checkQuery($data) === false) {
                die('查詢資料輸入有誤！');
            }
            if ($data['op'] == 'like') {
                $data['val'] = '%' . $data['val'] . '%';
            }
            $where = 'WHERE `p`.`' . $data['query'] . '` ' . $this->_QUERY_OP[$data['op']] . ' \'' . $this->_db->escape($data['val']) . '\'';
        }
        if (isset($data['per'])) {
            $page = (isset($data['page'])) ? $data['page'] : 1;
            $limit = 'LIMIT ' . (($page - 1) * $data['per']) . ' , ' . $data['per'];
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
    public function top($per)
    {
        $data['popular_top'] = $this->query([
            'per' => $per,
            'order' => '`p`.`click` DESC'
        ]);
        $data['popular_bot'] = $this->query([
            'per' => $per,
            'order' => '`p`.`click` ASC'
        ]);
        $data['sale_top'] = $this->query([
            'per' => $per,
            'order' => '`p`.`sale` DESC'
        ]);
        $data['sale_bot'] = $this->query([
            'per' => $per,
            'order' => '`p`.`sale` ASC'
        ]);
        $data['store'] = $this->query([
            'per' => $per,
            'order' => '`p`.`store` ASC'
        ]);
        $data['profit'] = $this->query([
            'column' => ['(`p`.`price` / `p`.`cost`) AS `profit`'],
            'per' => $per,
            'order' => '`profit` DESC'
        ]);
        return $data;
    }

    /**
     * @return array
     */
    public function calc()
    {
        $calc = [];
        $SQL = 'SELECT sum(`total`) FROM `order` WHERE `_checkout` = 1';
        $calc['sale'] = $this->_db->one($SQL);
        return $calc;
    }

    /**
     * 查詢資料驗證
     *
     * @param array $data
     * @return bool
     */
    public function checkQuery($data)
    {
        switch ($data['op']) {
            case 'like':
                if (!in_array($data['query'], $this->_LIKE_QUERY)) {
                    return false;
                }
                break;
            case 'lt':
            case 'le':
            case 'gt':
            case 'ge':
            case 'ne':
            case 'eq':
                if (!in_array($data['query'], $this->_NUM_QUERY)) {
                    return false;
                }
                if (!is_long((int)$data['val'])) {
                    return false;
                }
                break;
            default:
                return false;
        }
        return true;
    }

    /**
     * 處理購物車的方法
     *
     * @param string $op
     * @param array null $data
     * @param int $id
     * @return bool
     */
    public function shopAction($op, $data = null, $id = null)
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        switch ($op) {
            case 'view':
                break;
            case 'add':
                if ($data['pic'] != null) {
                    if (!preg_match("/(\..*)$/", $data['pic'], $match)) {
                        die('上傳檔案錯誤');
                    }
                    $data['pic'] = $this->_db->getInsertId('product') . $match[1];
                    if (!move_uploaded_file($_FILES['pic']['tmp_name'], 'img/' . $data['pic'])) {
                        die('檔案移動錯誤');
                    }
                }
                return $this->_db->insert($data, 'product');
            case 'upd':
                $one = $this->one($id);
                if ($data['pic'] != null) {
                    if (!preg_match("/(\..*)$/", $data['pic'], $match)) {
                        die('上傳檔案錯誤');
                    }
                    if ($one['pic'] != null) {
                        unlink('img/' . $one['pic']);
                    }
                    $data['pic'] = $one['id'] . $match[1];
                    if (!move_uploaded_file($_FILES['pic']['tmp_name'], 'img/' . $data['pic'])) {
                        die('檔案移動錯誤');
                    }
                } else {
                    $data['pic'] = $one['pic'];
                }
                return $this->_db->update([[null], ['id'], [$id]], $data, 'product');
            case 'del':
                return $this->_db->delete([[null], ['id'], [$id]], $data, 'product');
            case 'cadd':
                die();
                return $this->_db->delete([[null], ['id'], [$id]], $data, 'product_category');
                break;
            case 'cupd':
                return $this->_db->update([[null], ['id'], [$id]], $data, 'product_category');
                break;
            case 'cdel':
                return $this->_db->delete([[null], ['id'], [$id]], $data, 'product_category');
                break;
        }
        $cart_data['cart'] = $_SESSION['cart'];
        $cart_data['total'] = $this->cartTotal($_SESSION['cart']);
        return $cart_data;
    }

    /**
     * 取得所有分類的方法
     *
     * @return Collection
     */
    public function allCategory(): Collection
    {
        return ProductCategory::all();
    }

    /**
     * 取得單一項目的方法
     *
     * @param int $id 指定產品資料表的 id 欄位值
     * @return ProductCategory
     */
    public function oneCategory($id): ProductCategory
    {
        return ProductCategory::find($id);
    }

    /**
     * 處理購物車的方法
     *
     * @param string $op
     * @param null|array $data
     * @return array
     */
    public function cartAction($op, $data = null)
    {
        $cart_data = [];
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        switch ($op) {
            case 'view':
                $cart_data['cart'] = $_SESSION['cart'];
                $cart_data['total'] = $this->cartTotal($_SESSION['cart']);
                break;
            case 'add':
                $data['amount'] = (isset($_SESSION['cart'][$data['id']])) ? $_SESSION['cart'][$data['id']]['amount'] + 1 : 1;
                $_SESSION['cart'][$data['id']] = $data;
                echo $this->showAlert('已加入購物車', 'index.php?act=cart&op=view');
                $cart_data['cart'] = $_SESSION['cart'];
                $cart_data['total'] = $this->cartTotal($_SESSION['cart']);
                break;
            case 'upd':
                $_SESSION['cart'][$data['id']] = $data;
                echo $this->showAlert('項目已更新', 'index.php?act=cart&op=view');
                $cart_data['cart'] = $_SESSION['cart'];
                $cart_data['total'] = $this->cartTotal($_SESSION['cart']);
                break;
            case 'del':
                unset($_SESSION['cart'][$data['id']]);
                echo $this->showAlert('項目已刪除', 'index.php?act=cart&op=view');
                $cart_data['cart'] = $_SESSION['cart'];
                $cart_data['total'] = $this->cartTotal($_SESSION['cart']);
                break;
            case 'submit':
                $this->cartSubmit($data);
                echo $this->showAlert('訂單已送出', null);
                session_destroy();
                break;
            case 'query':
                $result = $this->orderAction($op, $data);
                $cart_data['cart'] = $this->analysisOrder($result['data']);
                $cart_data['total'] = $result['total'];
                $cart_data['user'] = $result;
                break;
            case 'clear':
                session_destroy();
                echo $this->showAlert('訂單已清除', 'index.php?act=cart&op=view');
                break;
        }
        return $cart_data;
    }

    /**
     * 購物車計算總合的方法
     *
     * @param array $data
     * @return float|int
     */
    private function cartTotal($data)
    {
        $total = 0;
        foreach ($data as $value) {
            $total += $value['price'] * $value['amount'];
        }
        return $total;
    }

    /**
     * 購物車提交的方法
     *
     * @param array $data
     */
    private function cartSubmit($data)
    {
        $data['total'] = $this->cartTotal($_SESSION['cart']);
        foreach ($_SESSION['cart'] as $value) {
            $data['data'] .= ($data['data']) ? '|' . $value['id'] . ':' . $value['amount'] : $value['id'] . ':' . $value['amount'];
        }
        if ($this->_db->insert($data, 'order') === false) {
            $this->showAlert('訂單送出失敗！請返回操作！', BACK);
        }
    }

    /**
     * 顯示訊息的方法
     *
     * @param null|string $msg
     * @param null|string $url
     * @return string
     */
    public function showAlert($msg = null, $url = null)
    {
        if ($msg != null) {
            $msg = 'alert(\'' . $msg . '\');';
        }

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
     * 處理購物車的方法
     *
     * @param string $op
     * @param null|array $data
     * @return array|bool|mysqli_result|null|resource
     */
    public function orderAction($op, $data = null)
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        switch ($op) {
            case 'view':
                $SQL = 'SELECT * FROM `order`';
                return $this->_db->all($SQL);
                break;
            case 'info':
                $SQL = 'SELECT * FROM `order` WHERE `id` = ' . $data['id'];
                $result = $this->_db->row($SQL);
                $result['data'] = $this->analysisOrder($result['data']);
                return $result;
                break;
            case 'upd':
                $_SESSION['cart'][$data['id']] = $data;
                echo $this->showAlert('項目已更新', 'index.php?act=cart&op=view');
                break;
            case 'del':
                unset($_SESSION['cart'][$data['id']]);
                echo $this->showAlert('項目已刪除', 'index.php?act=cart&op=view');
                break;
            case 'submit':
                $this->cartSubmit($data);
                echo $this->showAlert('訂單已送出', 'index.php');
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
                echo $this->showAlert('訂單已清除', 'index.php?act=cart&op=view');
                break;
        }
    }

    /**
     * 購物車訂單分析
     *
     * @param string $data
     * @return array
     */
    private function analysisOrder($data)
    {
        $result = [];
        foreach (explode('|', $data) as $row) {
            $order = explode(':', $row);
            $SQL = 'SELECT `p`.*, `c`.`title` AS `ctitle` FROM `product` AS `p` JOIN `product_category` AS `c` ON `p`.`category` = `c`.`id` WHERE `p`.`id` = ' . $order[0];
            $order_data = $this->_db->row($SQL);
            $order_data['amount'] = $order[1];
            $result[] = $order_data;
        }
        return $result;
    }
}
