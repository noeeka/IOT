<?php
#通用获取token服务
function getAccessToken()
{
    $m = new Memcached();
    $m->addServer('127.0.0.1', 11211);
    $url = "https://" . URI . "/iocm/app/sec/v1.1.0/login";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
    curl_setopt($ch, CURLOPT_SSLCERT, dirname(dirname(dirname(__FILE__))) . '/include/client.crt');
    curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
    curl_setopt($ch, CURLOPT_SSLKEY, dirname(dirname(dirname(__FILE__))) . '/include/client.key');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("appId" => APPID, "secret" => SECRET)));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded'));
    $data = curl_exec($ch);

    $raw = json_decode($data, true);
    $accessToken = "";
    if ($raw['accessToken']) {
        $accessToken = $m->get('accessToken');
        if (empty($accessToken)) {
            $m->set('accessToken', $raw['accessToken'], $raw['expiresIn']);
        }
        $accessToken = $raw['accessToken'];
    } else {
        echo json_encode(array("state" => 1, "msg" => "access error"));
        die;
    }
    return $accessToken;
}

#通用API请求方法
function getOceanConnectAPI($accessToken, $uri, $param)
{

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_URL, "https://".URI."/iocm/app/dm/v1.4.0/devices/9308b496-7836-4cd1-8a41-85191fb79097/?appid=".APPID."&select=imsi");
    curl_setopt($ch, CURLOPT_URL, "https://" . URI . $uri . "?appid=" . APPID . "&" . $param);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
    curl_setopt($ch, CURLOPT_SSLCERT, dirname(dirname(dirname(__FILE__))) . '/include/client.crt');
    curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
    curl_setopt($ch, CURLOPT_SSLKEY, dirname(dirname(dirname(__FILE__))) . '/include/client.key');
    $headers[] = "app_key:" . APPID;
    $headers[] = "Authorization: Bearer " . $accessToken;
    $headers[] = "Content-Type:application/json";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

//通用请求http服务
function http_request($url, $post = '', $method = 'GET', $limit = 0, $returnHeader = FALSE, $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE)
{
    $return = '';
    $matches = parse_url($url);
    !isset($matches['host']) && $matches['host'] = '';
    !isset($matches['path']) && $matches['path'] = '';
    !isset($matches['query']) && $matches['query'] = '';
    !isset($matches['port']) && $matches['port'] = '';
    $host = $matches['host'];
    $path = $matches['path'] ? $matches['path'] . ($matches['query'] ? '?' . $matches['query'] : '') : '/';
    $port = !empty($matches['port']) ? $matches['port'] : 80;
    if (strtolower($method) == 'post') {
        $post = (is_array($post) and !empty($post)) ? http_build_query($post) : $post;
        $out = "POST $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        $out .= "Host: $host\r\n";
        $out .= 'Content-Length: ' . strlen($post) . "\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cache-Control: no-cache\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
        $out .= $post;
    } else {
        $out = "GET $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
    }
    $fp = fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
    if (!$fp) return ''; else {
        $header = $content = '';
        stream_set_blocking($fp, $block);
        stream_set_timeout($fp, $timeout);
        fwrite($fp, $out);
        $status = stream_get_meta_data($fp);
        if (!$status['timed_out']) {//未超时
            while (!feof($fp)) {
                $header .= $h = fgets($fp);
                if ($h && ($h == "\r\n" || $h == "\n")) break;
            }
            $stop = false;
            while (!feof($fp) && !$stop) {
                $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
                $content .= $data;
                if ($limit) {
                    $limit -= strlen($data);
                    $stop = $limit <= 0;
                }
            }
        }
        fclose($fp);
        return $returnHeader ? array($header, $content) : json_decode($content,true);
    }
}