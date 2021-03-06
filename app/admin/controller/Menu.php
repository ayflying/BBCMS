<?php
namespace app\admin\controller;
use think\facade\Config;
use think\Db;
Use app\portal\model\PortalMenu;

class Menu extends Common{
	
	public function index(){
		$db = Db::name('PortalMenu');
		$sql = $db -> order('tid') ->  order('weight desc') -> select();
		//$sql = $db -> where("pid = 0") -> select();
		$this -> assign('sql',$sql);
		return $this -> fetch('./portal_menu');
	}
	
	public function menu_add(){
		
		if(request()->isPost()){
			$post = input('post.');
			/*
            empty($post['template']) and $post['template'] = './Portal/list';
			empty($post['template2']) and $post['template2'] = './Portal/article';
            */
			Db::name('PortalMenu') -> insert($post);
			$this -> success('添加成功');
			
		}else{
			$type = Db::name('PortalMod') -> select();
			$this -> assign('type',$type);
			
			$menu = Db::name('PortalMenu') -> where(["pid" => 0]) -> select();
			$this -> assign('menu',$menu);
			//dump($menu);
			$this -> assign('sql',['mod' => null, 'pid' => null]);
			return $this-> fetch('./portal_menu_edit');
		}
		
	}
	
	function menu_edit($tid){
		
		$type = Db::name('PortalMod') -> select();
		$this -> assign('type',$type);
		/*
		$menu_db = Db::name('PortalMenu');
		$menu = $menu_db -> where(["pid" => 0]) -> select();
        */
        $menu =  PortalMenu::where('pid',0)-> select();
		$this -> assign('menu',$menu);
		
		$sql = PortalMenu::find($tid);
		$this -> assign('sql',$sql);
		
		if(request()->isPost()){
			$post = input('post.');
            
			//print_r(input('post.'));
			//$menu_db -> create();
			$save = $post;
            
            
            empty($post['template_article']) and $save['template_article'] = './portal/article';
            empty($post['template_list']) and $save['template_list'] = './portal/list';
            empty($post['template_add']) and $save['template_add'] = './post/add';
            empty($post['template_edit']) and $save['template_edit'] = './post/edit';
			//默认值
			
            PortalMenu::where('tid',$tid) -> update($save);
			if($post['pid'] > 0){
				PortalMenu::where('pid',$tid) -> update(['pid' => $post['pid']]);	//修改下级栏目
			}
			
            return $this -> success('编辑成功',null,null,1);
			
		}else{
			return $this-> fetch('./portal_menu_edit');
		}
	}
    
    public function menu_delete($tid){
        
        $pid = Db::name('portal_menu') -> where("pid",$tid) -> find();
        if(isset($pid)){
            $this -> error("请先删除下级栏目再删除该栏目");
        }
        
        Db::name('portal_menu') -> delete($tid);
        $aid = Db::name('portal_article') -> where('tid',$tid) -> column('aid');
        $portal = new Portal;
        foreach($aid as $val){
            $portal -> delete($val);
        }
        return $this -> success("删除完成",null,null,1);
    }
	
}