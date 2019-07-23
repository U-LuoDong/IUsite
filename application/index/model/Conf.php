<?php
namespace app\index\model;
use think\Model;
class Conf  extends Model
{
    /**
     * 获取配置表中的中英文字段
     * @return mixed
     */
    public function getAllConf(){
    	$confres=$this::field('enname,cnname')->select();
    	return $confres;
    }
}
