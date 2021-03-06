<?php
namespace app\portal\controller;
use think\Db;
use think\facade\Cache;
use app\portal\model\PortalArticle;
use app\common\controller\Common;


class Article extends Common{

	public function index($aid){
        Db::name('portal_article') ->where('aid', $aid) ->setInc('click');
        $status = Db::name('portal_article') -> where('aid',$aid) -> value('status');
        if(empty($status) || $status < 1){
            $this -> error("该文章未通过审核或不存在");
        }
            
        if(empty(Cache::get('article_'.$aid))){
            //$sql = PortalArticle::get($aid);
            $sql = PortalArticle::where('aid',$aid) -> find();
            $sql -> addonarticle;
            $sql -> attachment;
            //dump($sql -> toArray());
            if($sql['mod']>0){
                $mod = Db::name('portal_mod') -> cache(true) -> where('id',$sql['mod']) -> find();
                $mod_list = Db::name('portal_mod_'.$sql['mod']) -> where('aid',$aid) -> cache(true) -> find();
                $mod = json_decode($mod['data'],true);
                foreach($mod as $key => $val){
                    //dump($mod);
                    $mod[$key] = [
                        'name' => $val[0],
                        'type' => $val[1],
                        'param' => $val[2],
                        'value' => $mod_list[$key],
                    ];
                    if($val[1] == 'select' || $val[1]== 'radio'){
                        $mod[$key]['param'] = explode(',',$mod[$key]['param']);
                    }else if($val[1] == 'checkbox'){
                        $mod[$key]['param'] = explode(',',$mod[$key]['param']);
                        $mod[$key]['value'] = explode(',',$mod[$key]['value']);
                    }
                }
                //$this -> assign('mod',$mod);
                $sql['mod'] = $mod;
            }
            Cache::set('article_'.$aid,$sql->toArray());
        }
        $sql = Cache::get('article_'.$aid);
        $this -> _G['menu'] = Db::name('portal_menu') -> cache('menu_'.$sql['tid']) -> where('tid',$sql['tid']) -> find();
        $this -> _G['user'] = Db::name('member_user') -> cache('user_'.$sql['uid']) -> where('uid',$sql['uid']) -> find();
        $this -> _G['article'] = $sql;
		//dump($this);
        
		return $this-> fetch($this -> _G['menu']['template_article']);
	}

}