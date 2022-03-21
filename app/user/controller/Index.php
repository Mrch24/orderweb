<?php 
namespace app\user\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;

class Index extends BaseController
{
    public function login(){
        if(Request::param() != null){            
            $u_id = Request::param('u_id');            
            $u_password = Request::param('u_password'); // 获取当前请求的所有变量                           
            $data=Db::table('user')->where('u_id',$u_id)->find();/**查询数据**/
            if(!$data){                
                echo('该用户不存在');
            }elseif($u_password == $data['u_password']){                
                Session::set('u_id',$u_id);
                return redirect('/index.php/index/index');                
            }else{
                echo('密码输入错误');
            }
        }       
        return View::fetch();
    }

    public function index()
    {
        if(Session::get('u_id') == null){
            $login = '您尚未登陆！';
        }else{
            $login =  Session::get('u_id');             
        };        
        $title = '天行健';
        #左侧菜单
        $menu = Db::table('user_menu')->where('fid',0)->select();
        $left = $menu->toArray();
        foreach($left as &$left_v){
            $left_v['lists'] = Db::table('user_menu')->where('fid',$left_v['id'])->select();
        }        
        #右侧列表
        $param = Request::param();
        #分类搜索        
        if(isset($param['cat']) && $param['cat'] == 1){
            $where['cat'] = 1;
        }else if(isset($param['cat']) && $param['cat'] == 2){
            $where['cat'] = 2;
        }else if(isset($param['cat']) && $param['cat'] == 3){
            $where['cat'] = 3;
        }else if(isset($param['cat']) && $param['cat'] == 4){
            $where['cat'] = 4;
        }else if(isset($param['cat']) && $param['cat'] == 5){
            $where['cat'] = 5;
        }else if(isset($param['cat']) && $param['cat'] == 6){
            $where['cat'] = 6;
        }else if(isset($param['cat']) && $param['cat'] == 7){
            $where['cat'] = 7;
        }else{
            $where = true;
        }
        #分页
        $p = isset($param['p']) ? $param['p'] : 1;
        $count = Db::table('shop_goods')->where($where)->count();   
        $list = Db::table('shop_goods')
                ->where($where)                
                ->order('id','DESC')
                ->page($p,3)
                ->select();
        $right = $list->toArray();
        foreach($right as &$right_v){
            $right_v['cat'] = Db::table('shop_cat')->where('id',$right_v['cat'])->value('name');
        }
        View::assign([
            'title'  => $title,
            'login' => $login,
            'left' => $left,
            'right' => $right,
            'count' => ceil($count/3),
            'p' => $p,
            'cat' => isset($param['cat'])? $param['cat'] : 0
        ]);
        return View::fetch();
    }
    
    public function orders()
    {
        if(Session::get('u_id') == null){
            $login = '您尚未登陆！';        
            return '您尚未登陆';
        }else{
            $login =  Session::get('u_id');             
        };        
        $title = '天行健';
        #左侧菜单
        $menu = Db::table('user_menu')->where('fid',0)->select();
        $left = $menu->toArray();
        foreach($left as &$left_v){
            $left_v['lists'] = Db::table('user_menu')->where('fid',$left_v['id'])->select();
        }
        
        #右侧列表
        $param = Request::param();        
        #分页
        $p = isset($param['p']) ? $param['p'] : 1;
        $count = Db::table('orders')->where('u_id',$login)->count();   
        $list = Db::table('orders')
                ->where('u_id',$login)                
                ->order('o_id','DESC')
                ->page($p,10)
                ->select();
        $right = $list->toArray();        
        View::assign([
            'title'  => $title,
            'login' => $login,
            'left' => $left,
            'right' => $right,
            'count' => ceil($count/10),
            'p' => $p,
        ]);
        return View::fetch();
    }

    public function shopcar()
    {
        if(Session::get('u_id') == null){
            $login = '您尚未登陆！';
            return '您尚未登陆';            
        }else{
            $login =  Session::get('u_id');             
        };        
        $title = '天行健';
        #左侧菜单
        $menu = Db::table('user_menu')->where('fid',0)->select();
        $left = $menu->toArray();
        foreach($left as &$left_v){
            $left_v['lists'] = Db::table('user_menu')->where('fid',$left_v['id'])->select();
        }
        
        #右侧列表
        $param = Request::param();        
        $list = Db::table('shopcar')                
                ->order('id','DESC')
                ->where('u_id',$login)
                ->where('o_id',NULL)
                ->select();
        $right = $list->toArray();   
        foreach($right as &$right_v){
            $right_v['price'] = Db::table('shop_goods')->where('id',$right_v['id'])->value('price_now');
            $right_v['name'] = Db::table('shop_goods')->where('id',$right_v['id'])->value('title');
        }
             
        View::assign([
            'title'  => $title,
            'login' => $login,
            'left' => $left,
            'right' => $right,            
        ]);
        return View::fetch();
    }

    
//POSTID
    public function postid(){
        $oid = Request::param('id');
        $oo_id = intval($oid);
        Session::set('oo_id',$oo_id);
    }
    
//查询detail
    public function detail(){
        if(Session::get('u_id') == null){
            return '您尚未登陆！';
        };
        $oo_id = Session::get('oo_id');
        $list = Db::table('shopcar')->where('o_id',$oo_id)->select();
        $right = $list->toArray();
        foreach($right as &$right_v){
            $right_v['price'] = Db::table('shop_goods')->where('id',$right_v['id'])->value('price_now');
            $right_v['name'] = Db::table('shop_goods')->where('id',$right_v['id'])->value('title');
        }           
        
        View::assign([
                'right' => $right,         
            ]);

        return View::fetch();
    }

    public function add(){
        if(Session::get('u_id') == null){
            echo json_encode(['code'=>1,'msg'=>'您还未登陆']);
        };
        $id = Request::param('id');
        $uid = Request::param('uid');
              
        $data = ['o_id' => NULL,'id' => intval($id),'u_id' => $uid];
        $insert = Db::table('shopcar')->insert($data);
        if($insert){
            echo json_encode(['code'=>0,'msg'=>'添加到购物车成功！']);
        }else{
            echo json_encode(['code'=>1,'msg'=>'添加失败']);
        }
    }
    
    public function exsit(){
        Session::delete('u_id');
        return redirect('/index.php/index/index');
    }

    public function del_car(){        
        $id = Request::param('id');
        $delete = Db::table('shopcar')->where('iid',$id)->delete();
        if($delete){
            echo json_encode(['code'=>0,'msg'=>'删除成功']);
        }else{
            echo json_encode(['code'=>1,'msg'=>'删除失败']);
        }
    }

    public function register(){
        if(Request::method() == 'POST'){
            $all = Request::param();
            $find = Db::table('user')->where('u_id',$all['u_id'])->find();
            if($find == null){
                $insert = Db::table('user')->insert($all);
                return redirect('/index.php/index/index');
            }else{
                echo '该用户名已被注册';
            }
        }
        return View::fetch();  
    }

    public function settle(){
        $login =  Session::get('u_id');
        $data['u_id'] =  $login;
        $data['o_time'] =  date('Y-m-d H:i:s');
        $data['o_status'] = NULL;
        $data['o_price'] = 0;
        $list = Db::table('shopcar')                
                ->order('id','DESC')
                ->where('u_id',$login)
                ->where('o_id',NULL)
                ->select();
        $right = $list->toArray();
        foreach($right as &$right_v){
            $right_v['price'] = Db::table('shop_goods')->where('id',$right_v['id'])->value('price_now');            
        }
        foreach($right as &$right_v){
            $data['o_price'] = $data['o_price'] + $right_v['price'];            
        }
        $insert = Db::table('orders')->insertGetId($data);
        foreach($right as &$right_v){           
            $update = Db::table('shopcar')->where('iid',$right_v['iid'])->update(['o_id' => $insert]);
        }
        
        if($insert){
            echo json_encode(['code'=>0,'msg'=>'提交订单成功']);
        }else{
            echo json_encode(['code'=>1,'msg'=>'提交订单失败']);
        }
    }
    
}
