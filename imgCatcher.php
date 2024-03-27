<?php

class imgCatcher
{
    public function download($arcurl){
        $fileimgname = str_replace("https://mmbiz.qpic.cn/sz_mmbiz_jpg/", "", $arcurl);

        $fileMd5 = md5($fileimgname);
        $fileimgname = $fileMd5 . '.jpeg';
        $dirslsitss = './wechat';
        $dirslsitssPath = '/wechat';
        $path = $dirslsitss . "/" . $fileimgname;

        if (!file_exists($path)) {
            $img = file_get_contents($arcurl);
            if (!empty($img)) {
                //保存图片到服务器
                file_put_contents($path, $img);
            }
        }

        $insteadName = $dirslsitssPath . "/" . $fileimgname;
        return $insteadName;
    }
}

echo json_encode(['path'=>(new imgCatcher())->download($_POST['img_path'])]);
