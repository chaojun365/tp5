<?php
namespace app\index\controller;
use think\Controller;
use think\Request;

/**
 * 
 */
class Miao extends Controller
{
   public function redis_config()
   {

   }
   //秒杀
   public function userMiao()
        {
            //redis连接
            $redis = new \Redis();
            $redis->connect('127.0.0.1','6379');
            $redis->auth('123456');
            $redis->select(15);
            //商品列
            $goods_list = 'goods';
            //成功用户集合
            $succ_user = 'succ';
            //失败用户列
            $fail_list = 'fail';
          for ($i=0;$i<20;$i++){
                $uid = rand(1000,9999);
            //判断商品列是否有库存
            if ($redis->lLen($goods_list) > 0) {
                
                //判断用户id是否在用户集合中       
               // dump($redis->ZRANK($user_id,1));
                if ($redis->ZRANK($succ_user,$uid ) !='nil') {
                        //dump($redis->ZRANK($user_id,1));
                        //减库存
                        $goodsid = $redis->lPop($goods_list);
                        
                        //将用户id存入集合
                        $aa = $redis->zAdd($succ_user,$goodsid,$uid);
                        //dump($aa);
                        echo $uid."恭喜抢到了!","<br/>";
                } else {
                   echo "每个用户只能抢一台","<br/>";
                }

            } else {
                //未抢到用户写入失败用户列
                //$redis->rPUSH($fail_list,$uid);
                echo $uid."商品已经售空","<br/>";
            }
          }  

            //$redis->close();
        } 

        //向商品列添加商品id
        public function addGoods($goodsid)
        {
           //redis连接
            $redis = new \Redis();
            $redis->connect('127.0.0.1','6379');
            $redis->auth('123456');
            $redis->select(15);
            $goods_list = 'goods';
            $res = $redis->rPUSH($goods_list,$goodsid);
            //dump($res);
            if ($res==true) {
                echo $goodsid.'商品添加成功!'.microtime();
            } else {
                echo '添加失败!';
            }
            

        }   
}
