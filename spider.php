<?php
require_once "phpQuery/phpQuery.php";

class Spider
{
    protected $jsTemplate = '<script
  src="https://code.jquery.com/jquery-2.2.4.js"
  integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI="
  crossorigin="anonymous"></script>

  <script>

  $("img").each(function(i, item) {
      var img_path = $(item).attr("data-src");
       if(!img_path){
           img_path = $(item).attr("src")
       }
      $.ajax({
      url:"./imgCatcher.php",
      data:{
          img_path:img_path
      },
      type:"post",
      success:function (res){
          res = JSON.parse(res)
          console.log("赋值", $(item),res)
           $(item).attr("src",res.path)
      }
      })
                       //$(n).val()
    });
</script>';


    function getContent()
    {
        $url = $_GET['url'];
        $domain = $this->GetTopDomain($url);

        $response = $this->http_request($url); // 发起带有代理的请求获取接口数据
//        echo $url;

        $response = $this->convert_gbk_to_utf8($response);
//        echo $response;

// 步骤5：过滤处理数据
        $response = str_replace("gbk",'utf-8',$response);

        $doc = phpQuery::newDocument($response); // 使用phpQuery处理接口返回的数据

        $range = [
            'mp.qq.com' => "#js_content",
            'baijiahao.baidu.com' => ".EaCvy",
            "www.zgsjlm.cn" => ".MoBodyM",
        ];

        $selector = empty($range[$domain]) ? "body" : $range[$domain];

        $content = $doc->find($selector)->html(); // 假设音乐数据在接口返回的JSON中以.music-item为类名的元素存在
//        var_dump($doc->find($selector));

        $xstr = $content;

        //通过js用浏览触发并发效果
        $script = $this->jsTemplate;
        $xstr = $xstr . $script;

        echo $xstr;
    }


    /**
     *PHP字符串GBK编码转换工具
     * @paramstring$str需要转换的字符串
     * @paramstring$from_charset原始字符集，默认为GBK
     * @paramstring$to_charset目标字符集，默认为UTF-8
     * @returnstring$str转换后的字符串
     */
    function convert_gbk_to_utf8($str, $from_charset = 'GBK', $to_charset = 'UTF-8')
    {
        if (empty($str)) return '';
        if (mb_check_encoding($str, $from_charset)) {
            return mb_convert_encoding($str, $to_charset, $from_charset);
        } else {
            return $str;
        }
    }


    /**
     * 获取顶级域名
     * @param string|null $url
     * @return TopDomain
     */
    function GetTopDomain(string $url = null)
    {
        // 判断网址是否带http://或https://
        if (preg_match('/^http(s)?:\/\/.+/', $url)) {
            $hosts = parse_url(strtolower($url));
            $host = $hosts['host'];
        } else {
            $host = strtolower($url);
        }

        // 查看是几级域名
        $data = explode('.', $host);
        $n = count($data);
        // 判断是否是双后缀
        $preg = '/[w].+.(com|net|org|gov|edu).cn$/';
        // 双后缀取后3位
        if (($n > 2) && preg_match($preg, $host)) $host = $data[$n - 3] . '.' . $data[$n - 2] . '.' . $data[$n - 1];
        // 非双后缀取后两位
        else $host = $data[0] . '.' . $data[$n - 2] . '.' . $data[$n - 1];
        return $host;
    }


    /**
     * 通用CURL请求
     * @param $url  需要请求的url
     * @param null $data
     * return mixed 返回值 json格式的数据
     */
    public function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $info = curl_exec($curl);
        curl_close($curl);
        return $info;
    }

}


(new Spider())->getContent();








//echo $xstr;


// return $xstr;
