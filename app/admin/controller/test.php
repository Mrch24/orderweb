<?php
namespace app\controller;


use think\facade\View;
use think\facade\Db;

class test{
    public function test(){
        $select = Db::table('shop_goods')->select();
        if($select->isEmpty()){
            echo '未找到数据';
        }
        print_r($select->toArray());
        
    }
}