<?php

class db {
	/**
	 * 設定成員
	 */
	private $_config = array();
	/**
	 * 取得資料的型式
	 * 在db::init()方法中設定
	 */
	private $_fetch_mode = Null;
	/**
	 * 連線代碼
	 */
	private $_connection = Null;
	/**
	 * 存取資源的成員
	 */
	private $_resource = Null;
	/**
	 * debug模式
	 */
	private $_debug;
	/**
	 * 建構函式
	 * 傳入參數為boolean，如為True則會開啟DEBUG模式
	 */
	public function __construct($DEBUG_MODE) {
		$this->_debug = $DEBUG_MODE;

		$this->_config['HOST'] =DB_HOST;
		$this->_config['USER'] = DB_USER;
		$this->_config['PASS'] = DB_PASS;
		$this->_config['DATABASE'] = DB_NAME;
		$this->_config['CHARSET'] = DB_CHARSET;

		if(is_null($this->_connection)) {
			$this->_connect();
		}
		$this->init();
	}
	/**
	 * 解構方法
	 */
	public function __destruct(){
		$this->_disconnect();
	}
	/**
	 * 連線方法
	 */
	protected function _connect() {
		$this->_connection = mysql_connect($this->_config['HOST'],
			$this->_config['USER'],
			$this->_config['PASS']) or die('Error with MySQL connection');
	}
	/**
	 * 斷線方法
	 */
	protected function _disconnect() {
		mysql_close($this->_connection);
	}
	/**
	 * 初始化方法
	 */
	public function init() {
		$this->_fetch_mode = MYSQL_ASSOC;
		mysql_query("SET NAMES '" . $this->_config['CHARSET'] . "'");
		mysql_select_db ($this->_config['DATABASE'] , $this->_connection) or die('Error with MySQL db select');
	}
	/**
	 * 基本查詢方法
	 * 需傳入一SQL語法
	 * 如為SELECT查詢方法則會return一個resource
	 */
	public function query($SQL) {
		if (!$resource = mysql_query($SQL, $this->_connection)) {
			if ($this->_debug) {
				echo mysql_errno().": ".mysql_error()."<BR>";
				die ("MySQL Query Error");
			}
			else {
				die ("Error");
			}
		}
        $this->_resource = $resource;
        return $resource;
	}
	/**
	 * 查詢方法-ALL
	 * 需傳入一SELECT的SQL語法
	 * 會return一個二維Array
	 */
	public function all($SQL) {
		$data = '';
		$this->query($SQL);
		while( $row = mysql_fetch_array($this->_resource, $this->_fetch_mode) ){
			if($row) {
				$data[] = $row;
			}
		}
		if(!$data) {
			$data = Null;
		}
		return $data;
	}
	/**
	 * 查詢方法-ROW
	 * 需傳入一SELECT的SQL語法
	 * 會return一個一維Array，為列的所有資料
	 */
	public function row($SQL) {
		$this->query($SQL);
		return mysql_fetch_array($this->_resource, $this->_fetch_mode);
	}
	/**
	 * 查詢方法-COL
	 * 需傳入一SELECT的SQL語法
	 * 會return一個一維Array，為欄的資料
	 */
	public function col($SQL) {
		$this->query($SQL);
		return mysql_fetch_array($this->_resource, $this->_fetch_mode);
	}
	/**
	 * 查詢方法-One
	 * 需傳入一SELECT的SQL語法
	 * 會return一個單一資料
	 */
	public function one($SQL) {
		$this->query($SQL);
		$row = mysql_fetch_array($this->_resource, $this->_fetch_mode);
		return array_pop($row);
	}
	/**
	 * 查詢有幾筆資料
	*/
	public function get_num_rows() {
		if ($this->_resource === Null) {return Null;}
		return mysql_num_rows($this->_resource);
	}
	/**
	 * 查詢前一個INSERT動作的ID
	 */
	public function get_insert_id($table) {
		$status = $this->row("SHOW TABLE STATUS WHERE `NAME` = '" . $table . "'");
		return $status['Auto_increment'];
	}
	/**
	 * escape方法
	 * 傳入一字串，回傳一加入escape字元的字串
	 */
	public function escape($str) {
		return mysql_real_escape_string($str);
	}
	/**
	 * 自動產生Insert SQL方法
	 * $data為資料，格式如下
	 *	$data = array (
	 *		'field1' => 'value1',
	 *		'field2' => 'value2',
	 *		'field3' => 'value3'
	 *	)
	 * $table為表格名稱
	 * 會回傳成功與否的boolean值
	 */
	public function insert($data, $table){
		if (!is_array($data)) { return False; }
		$SQLColumn = '';
		$SQLValue = '';
		foreach ($data as $column => $value){
			$SQLColumn .= ($SQLColumn == '')? ' ': ', ';
			$SQLColumn .= "`$column`";
			$SQLValue .= ($SQLValue == '')? ' ': ', ';
			if ($value == Null)
				$SQLValue .= "NULL";
			else
				$SQLValue .= "'$value'";
		}
		$this->query("INSERT INTO `$table` ($SQLColumn) VALUES ($SQLValue)");
		return True;
	}
	/**
	 * 自動產生Delete SQL方法
	 * $where的格式請參考_createWhere
	 * 會回傳成功與否的boolean值
	 */
	public function delete($where, $table) {
		if (is_null($table)) { return False; }
		if (!is_array($where)) { return False; }
		$this->query("DELETE FROM `" . $table . "` WHERE " . $this->_createWhere($where));
		return True;
	}
	/**
	 * 自動產生Update SQL方法
	 * $where的格式請參考_createWhere
	 * $update為資料，格式參考Insert
	 * $table為表格名稱
	 * 會回傳成功與否的boolean值
	 */
	public function update($where, $update, $table){
		if (is_null($table)) { return False; }
		if (!is_array($where)) { return False; }
		if (!is_array($update)) { return False; }
		$updateSQL = '';
		foreach ($update as $dataColumn => $dataValue){
			$updateSQL .= ($updateSQL == '')? '' : ', ';
			if ($dataValue == Null)
				$updateSQL .= "`$dataColumn` = NULL";
			else
				$updateSQL .= "`$dataColumn` = '$dataValue'";
		}
		$this->query("UPDATE `" . $table . "` SET $updateSQL WHERE " . $this->_createWhere($where));
		return TRUE;
	}
	/**
	 *	產生where SQL方法
	 *	$where的格式範例：
	 *	`id` = '4' AND `name` = 'tails'
	 *	$where = array(
	 *		array(null,'AND'),
	 *		array('id','name'),
	 *		array('4','tails')
	 *	);
	 */
	private function _createWhere($where){
		$result = '';
		if(is_array($where) && count($where)>=2){
			$count = count($where[0]);
			$conditions = $where[0];
			$field = $where[1];
			$context = $where[2];
			for($i=0;$i<$count;$i++){
				if (is_null($conditions[$i]))
					$result .= "`$field[$i]` = '$context[$i]' ";
				else
					$result .= "$conditions[$i] `$field[$i]` = '$context[$i]' ";
			}
		}
		return $result;
	}
}

?>
