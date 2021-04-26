<?php
namespace app\common\model;
use think\Model;

/**
 * 图片处理
 */
class Image extends Model
{
	
	public function Upload($image)
	{
		    //$image= $content;
            $imageName = md5(date("His",time())."_".rand(1111,9999)).'.png';
            if (strstr($image,",")){
                $image = explode(',',$image);
                $image = $image[1];
            }

            $path = "uploads/".date("Ymd",time());
            if (!is_dir($path)){ //判断目录是否存在 不存在就创建
                mkdir($path,0777,true);
            }
            $imageSrc=  $path."/". $imageName;  //图片名字

            $r = file_put_contents("../public/".$imageSrc, base64_decode($image));//返回的是字节数
            //dump($imageSrc);
            $content = "<img src='$imageSrc'>";
            $status = (["img"=>$content,"status"=>0,"msg"=>"发送成功!"]);
            if(!$r)
            {
            	$status = (["status"=>1,"msg"=>"发送失败!"]);
            }

            return $status;
	}

    public function UpImg($request){
     //var_dump($request->file('file'));
     // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('pic');
       // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move('./uploads');
      //dump($info);
      if($info){
        $content ='/uploads/'.str_replace('\\', '/',$info->getSaveName());
         
        return (['status'=>0,'img'=>$content,'msg'=>'上传成功!']);
      }else{
        return (['status'=>1,'img'=>'null','msg'=>$file->getError()]);
      }
    }
}