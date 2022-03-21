<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;

class Index extends BaseController
{
    public function login(){
        if(Request::param() != null){            
            $a_id = Request::param('a_id');            
            $a_password = Request::param('a_password'); // 获取当前请求的所有变量                           
            $data=Db::table('admin')->where('a_id',$a_id)->find();/**查询数据**/
            if(!$data){                
                echo('该用户不存在');
            }elseif($a_password == $data['a_password']){                
                Session::set('a_id',$a_id);
                return redirect('/index.php/index/index');                
            }else{
                echo('密码输入错误');
            }
        }       
        return View::fetch();
    }

    public function orders()
    {
        if(Session::get('a_id') == null){
            $login = '您尚未登陆！';
            return '您尚未登陆';
        }else{
            $login =  Session::get('a_id');             
        };        
        $title = '天行健';
        #左侧菜单
        $menu = Db::table('shop_menu')->where('fid',0)->select();
        $left = $menu->toArray();
        foreach($left as &$left_v){
            $left_v['lists'] = Db::table('shop_menu')->where('fid',$left_v['id'])->select();
        }
        
        #右侧列表
        $param = Request::param();        
        #分页
        $p = isset($param['p']) ? $param['p'] : 1;
        $count = Db::table('orders')->count();   
        $list = Db::table('orders')                
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

    public function index()
    {
        if(Session::get('a_id') == null){
            $login = '您尚未登陆！';
        }else{
            $login =  Session::get('a_id');             
        };        
        $title = '天行健';
        #左侧菜单
        $menu = Db::table('shop_menu')->where('fid',0)->select();
        $left = $menu->toArray();
        foreach($left as &$left_v){
            $left_v['lists'] = Db::table('shop_menu')->where('fid',$left_v['id'])->select();
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

    public function add(){
        if(Session::get('a_id') == null){
           return '您尚未登陆！';
        }
        if(Request::method() == 'POST'){
            $all = Request::param();            
            $insert = Db::table('shop_goods')->insert($all);
            if($insert){
                echo json_encode(['code'=>0,'msg'=>'添加成功']);
            }else{
                echo json_encode(['code'=>1,'msg'=>'添加失败']);
            }
        }else{
            $cat = Db::table('shop_cat')->select();
            View::assign([
                'cat' => $cat
            ]);
            return View::fetch();
        }
    }

    public function edit(){
        if(Session::get('a_id') == null){
            return '您尚未登陆！';
         }
        if(Request::method() == 'POST'){
            $all = Request::param();
            $update = Db::table('shop_goods')->where('id',$all['id'])->update($all);
            if($update){
                echo json_encode(['code'=>0,'msg'=>'修改成功']);
            }else{
                echo json_encode(['code'=>1,'msg'=>'修改失败']);
            }
        }elseif(Request::method() == 'GET'){
            $id = Request::param('id');
            $shop = Db::table('shop_goods')->where('id',$id)->find();
            $cat = Db::table('shop_cat')->select();
            View::assign([
                'shop' => $shop,
                'cat' => $cat
            ]);
            return View::fetch();
        }else{
            echo '您的操作有误';
        }
    }

    public function del(){
        if(Session::get('a_id') == null){
            echo "<script>alert('您尚未登陆')</script>";
        }
        $id = Request::param('id');
        $delete = Db::table('shop_goods')->where('id',$id)->delete();
        if($delete){
            echo json_encode(['code'=>0,'msg'=>'删除成功']);
        }else{
            echo json_encode(['code'=>1,'msg'=>'删除失败']);
        }
    }
    
    public function paid(){
        if(Session::get('a_id') == null){
            echo "<script>alert('您尚未登陆')</script>";
        }
        $o_id = Request::param('id');
        $data =['o_status'=>'已支付'];
        $update = Db::table('orders')->where('o_id',$o_id)->update($data);
        if($update){
            echo json_encode(['code'=>0,'msg'=>'修改成功']);
        }else{
            echo json_encode(['code'=>1,'msg'=>'修改失败']);
        }
    }

    public function exsit(){
        Session::delete('a_id');
        return redirect('/index.php/index/index');
    }

    public function register(){
        if(Request::method() == 'POST'){
            $all = Request::param();
            $find = Db::table('admin')->where('a_id',$all['a_id'])->find();
            if($find == null){
                $insert = Db::table('admin')->insert($all);
                return redirect('/index.php/index/index');
            }else{
                echo '该用户名已被注册';
            }
        }
        return View::fetch();  
    }

//POSTID
    public function postid(){
        $oid = Request::param('id');
        $o_id = intval($oid);
        Session::set('o_id',$o_id);
    }
    
//查询detail
    public function detail(){
        if(Session::get('a_id') == null){
            return '您尚未登陆！';
        };
        $o_id = Session::get('o_id');
        $list = Db::table('shopcar')->where('o_id',$o_id)->select();
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
    
}
