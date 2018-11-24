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