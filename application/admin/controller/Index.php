<?php
namespace app\admin\controller;
use think\Config;
use think\Db;

class Index extends Common{
	
	public function index(){
		
		$list = [
			"服务器IP" => GetHostByName($_SERVER['SERVER_NAME']),
			"操作系统" => PHP_OS,
			"当前主机名:端口" => $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'], //当前主机名
			"当前时间" => date("Y-m-d H:i:s"),
			"框架版本" => THINK_VERSION,
			"语言" => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
			"PHP版本" => PHP_VERSION,
			'Zend版本' => Zend_Version(),
			"DG库版本" => $this -> GD('GD Version'),
			'系统版本' =>  'V'.THINK_VERSION,
            '最大执行时间' => ini_get("max_execution_time")."秒",
			"数据库版本" =>  $this->_mysql_version(),
			"数据库大小" => $this->_mysql_db_size(),
			"服务器类型" =>  $_SERVER["SERVER_SOFTWARE"],
			"最大上传尺寸" => ini_get("file_uploads") ? ini_get("upload_max_filesize") : "Disabled",
			"最大执行时间" => ini_get("max_execution_time")."秒",
			"当前登录IP" => request()->ip(),
		];
		//dump($arr);
		
		
		$this -> assign('list',$list);
		
		$num = [
			'article' => $this -> article(),
			'user' => $this -> user(),
		];
		$this -> assign('num',$num);
		return $this->fetch('./index');
		
	}
	
	private function article(){
		$db = Db::name('PortalArticle');
		$sql['num'] = $db ->  count();
		$sql['today'] = $db -> whereTime('create_time', 'today') -> count();
        $sql['yesterday'] = $db -> whereTime('create_time', 'yesterday') -> count();
        $sql['week'] = $db -> whereTime('create_time', 'week') -> count();
        $sql['month'] = $db -> whereTime('create_time', 'month') -> count();
		return $sql;
	}
	
	private function GD($string){
		$gd = gd_info();
		return $gd[$string];
	}
	
	private function user(){
		$db = Db::name('member_user');
		$sql['num'] = $db ->  count();
		$sql['today'] = $db -> whereTime('create_time', 'today') -> count();
        $sql['yesterday'] = $db -> whereTime('create_time', 'yesterday') -> count();
        $sql['week'] = $db -> whereTime('create_time', 'week') -> count();
        $sql['month'] = $db -> whereTime('create_time', 'month') -> count();
		return $sql;
	}
	
    
    private function _mysql_version()
    {
        $version = Db::query("select version() as ver");
        return $version[0]['ver'];
    }
	private function _mysql_db_size(){
        $database = config('database');
		$sql = "SHOW TABLE STATUS FROM ".$database['database'];
		$tblPrefix = $database['prefix'];
		if($tblPrefix != null) {
			$sql .= " LIKE '{$tblPrefix}%'";
		}
		$row = Db::query($sql);
		$size = 0;
		foreach($row as $value) {
			$size += $value["Data_length"] + $value["Index_length"];
		}
        return format_bytes($size);
	}
	
	
	function body(){
		$this-> display('./index_body');
	}
	
	function login(){
		$this-> display('./login');
	}
	
}