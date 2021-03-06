<?php
namespace app\portal\model;
use think\Model;
use app\member\model\MemberUser;

class PortalArticle extends Model
{

	protected $name = 'portal_article';
    protected $pk = 'aid';
	protected $insert = ['status'=>1];
	protected $autoWriteTimestamp = true;

    //字段类型
	protected $type = [
        'thumb'    =>  'json',
    ];

	public function addonarticle(){
		//return $this->hasOne('PortalAddonarticle','aid')->field('aid,content');
		return $this->hasOne('PortalAddonarticle','aid');

	}
	public function attachment(){
		return $this->hasMany('PortalAttachment','aid');
	}
    public function menu(){
		//return $this->hasOne('PortalMenu','tid','tid');
        return $this->belongsTo('PortalMenu','tid');
	}
    /*
    public function user(){
		//return $this->hasOne('PortalMenu','tid','tid');
        return $this->belongsTo('MemberUser','tid');
	}
    */


    static public function info($aid){
        $sql = $this -> where('aid',$aid) -> find();
        return $sql;
    }




}