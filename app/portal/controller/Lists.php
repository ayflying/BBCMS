<?php
namespace app\portal\controller;
use think\Db;
use think\Cache;
use think\facade\Config;
use app\portal\model\PortalArticle;
use app\common\controller\Common;

class Lists extends Common{
	public function index($tid=0,$search=null,$order='portal_article.update_time desc',$mod=null)
    {
        
		$sql = Db::name('portal_menu')-> cache("menu_".$tid) -> find($tid);
        
        if(!empty($sql['jump'])){
            header('Location: '.$sql['jump']);
        }
        $table_article = 'portal_article';
		$where[] = [$table_article.'.status','=',1];
        
		if($tid > 0){
			//获取下级目录文章
			$type = Db::name('portal_menu') -> where('pid',$tid) -> cache("list_pid_".$tid) -> column('tid');
			$type[] = (int)$tid;
			$where[] = ['tid','in',$type];
		}
		isset($search) and $where[] = ['title','like' , '%'.$search.'%'];
        
        
        
        
        
        
		if($sql['mod']>0){
            $table_mod = 'portal_mod_'.$sql['mod'];
            //mod筛选 ，格式为?mod=a,1|b,2|c,3
            if(isset($mod)){
                $mod_arr = explode('|',$mod);
                foreach($mod_arr as $val){
                    $mod_arr = explode(',',$val);
                    $where[] = [$table_mod.'.'.current($mod_arr),'=',next($mod_arr)];
                }
            }
			
            //$list = Db::view(['portal_article'=>'a'],'*')
			$list = Db::view($table_article)
            ->view($table_mod,'*',$table_article.'.aid = '.$table_mod.'.aid')
			->view('member_user','username,gid,headimgurl',$table_article.'.uid = member_user.uid')
            ->where($where) -> order($order)  //-> cache(true,null,'list')
            -> paginate(Config::get('bbcms.page_num'));
		}else{
            $list = Db::view($table_article)
            ->view('member_user','username,gid,headimgurl',$table_article.'.uid = member_user.uid')
			->where($where) -> order($order) //-> cache(true,null,'list')
            -> paginate(Config::get('bbcms.page_num'));
            
        }
        
        
        $page = $list->render();
        $this -> _G['menu'] = $sql;
        $this -> _G['page'] = $page;
        $this -> _G['list'] = $list;
        
		return $this-> fetch($this -> _G['menu']['template_list']);
	}


}