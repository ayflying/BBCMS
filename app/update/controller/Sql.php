<?php
namespace app\update\controller;
use think\facade\Request;
use think\Controller;
use think\Db;

class Sql extends Controller
{
    public function index(){
        
        $version= Request::instance()->post('version');
        if(empty($version)){
			$this -> error("版本不存在");
		}
        $main_version = Db::name('system_settings') -> where('name','version') -> value('value');
        //列出sql文件目录，显示出哪些可更新
		$dir = APP_PATH.'/update/sql/';
		$filelist = scandir($dir);
		unset($filelist[0]);
		unset($filelist[1]);
        
		if($filelist == null){
            $this -> error('无需更新',null,['success'=>0]);
        }
        
        foreach($filelist as $val){
			$sql_list[] = basename($dir.$val,".sql");
		}
        
        
        foreach($sql_list as $k => $val){
			
            //echo $version.' < '.$val.' && '.$main_version.' >= '.$val.'<br/>';
			if($version < $val && $main_version >= $val){
				$file = $dir.$val;
                echo file_get_contents($file.'.sql');
                //echo $this -> sql($k);
			}
            //同步版本号
            if($main_version > $version){
                echo "update `bb_system_settings`set `value` = '".$main_version."' where `name` = 'version';";
            }
		}
        
    }
    
    
}