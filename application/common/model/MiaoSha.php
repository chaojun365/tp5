<?php
namespace app\common\model;
use think\Model;
use think\Session;
use think\Db;
use think\Cache;
  //模拟秒杀
/**
 * 
 */
class MiaoSha extends Model
{
  
  
  public function miaoshav($goods)
  {  
     $redis = new \Redis();
     $redis->connect('localhost', 6379);
     $redis->AUTH(123456);
     $redis->select(14);
     //redisl列名
     $redis_name = 'miaosha';
     //商品名
     //$goods = 'IPHONE X';
            //模拟大量用户去请求  --只能模拟高压力,不能模拟高并发的
            /*for ($i=0;$i<20;$i++){
                $uid = rand(1000,9999);*/
                      //接受用户id
                        $uid = [Session('user.id')];
                        $len = $redis->lLen($redis_name);
                        $alluser = $redis->Lrange($redis_name,0,-1); 
                        //dump($alluser);
                       /* for ($i=0; $i < $len; $i++) { 

                              $j =$redis->lPop($redis_name);
                              dump ('已删除'.$j.'<br/>');
                              exit;
                        }  */          
                        $user = $redis->Lrange($redis_name,0,-1);  
                        foreach ($user as  $value) {
                                $user_arr    = explode('%',$value);
                                $aa = $user_arr[0];
                            //dump($aa);
                            if (in_array($aa,$uid)) {
                                $status = (['status'=>1,'msg'=>"每个用户只有一个抢购名额哦"]);
                                return json($status);
                            } 
    
                        }
                      //检查用户是否已经抢购到了
                      /*for ($i=0; $i < $len; $i++) { */
                          //从队列最左侧取出一个值来
                         // $user = $redis->Lrange($redis_name,0,-1);   
                         
                          //判断用户id是否已经抢到了
                          
                        /*  if ($user) {

                             $status = (['status'=>1,'msg'=>"每个用户只有一个抢购名额哦"]);
                             return json($status);

                          } */
                      /*}
                      */
                      
          
                      //获取一下redis里面已有的数量
                      $num = 10;
                      //如果当天人数少于十的时候，则加入这个队列,
                      if( $len < $num){
                        //dump($len);
                          $redis->rPush($redis_name,$uid[0].'%'.$goods.'%'.microtime());
                          //echo ($i+1).'用户id:'.$uid.$goods.' 秒杀成功 '.'<br/>';
                          $status = (['status'=>0,'msg'=>"恭喜用户id:'$uid[0]'成功秒到'$goods'"]);
                      }else{
                          //如果当天人数已经到达了十个人，则返回秒杀已完成
                          // echo "秒杀已经结束".'<br/>';
                          $status = (['status'=>1,'msg'=>"秒杀已经结束"]);
                      }
                      return json($status);
            /*}*/

       /*$msgqq = Cache::store('redis')->del("1");
       dump($msgqq);
*/  }

    
  //存入数据库
  public function sqlsave()
  {
                //redis连接
            $redis = new \Redis();
            $redis->connect('127.0.0.1','6379');
            $redis->auth(123456);
            $redis->select(14);
            //列表名
            $redis_name = 'miaosha';
          while (1){
            //从队列最左侧取出一个值来
            $user = $redis->lPop($redis_name);
            //然后判断这个值是否存在
                if(!$user || $user == ''){
                    sleep(2);
                    continue;
                }
             //切割出时间,uid
                $user_arr    = explode('%',$user);
                $insert_data = [
                    'uid'=>$user_arr[0],
                    'goods'=>$user_arr[1],
                    'time_stamp'=>$user_arr[2],
                ];
                //保存到数据库中
                $res = Db::name('miaosha')->insert($insert_data);
                //数据库插入的失败的时候的回滚机制
                if(!$res){
                    $redis->rPush($redis_name,$user);
                }
                sleep(2);
                echo $user_arr[0].'储存秒杀';
          }
  } 

   //删除redis列
  public function listdel()
  {
              $redis = new \Redis();
              $redis->connect('127.0.0.1','6379');
              $redis->auth(123456);
              $redis->select(14);
              //redisl列名
              $redis_name = 'miaosha';
              //列长度
              $len = $redis->lLen($redis_name);
              if ($len==0) {
                echo $redis_name.'中没有数据'.'<br/>';
              } else {

                for ($i=0; $i < $len; $i++) { 

                    $j =$redis->lPop($redis_name);
                    echo '已删除'.$j.'<br/>';
                }

              }   
      
  }

}