# webspider
a web spider catch content and images from a url
通过提交一个制定网页的连接 采集网页内容和图片到自己的站点上面
示例代码中采集了微信公众号内容以及一个网页的内容  

脚本通过pq进行范围区分，通过jq对数据采集内容的数据进行再处理 比如采集图片的触发，因为使用php的单进程模式，如果用脚本直接采集图片，耗时太长,所以用浏览器的并发事件进行触发
