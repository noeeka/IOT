<?php
class BASE_Controller extends CI_Controller
{
    function  __construct()
    {
        parent::__construct();
    }
}

/**
 * 管理基类
 */
class AdminBase extends BASE_Controller
{
    function  __construct()
    {
        parent::__construct();
        //$m = new Memcached();
        //$m->addServer('127.0.0.1', 11211);
        $this->load->driver('cache');
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
            $accessToken = $this->cache->memcached->get('accessToken');
            if (empty($accessToken)) {
                $this->cache->memcached->save('accessToken', $raw['accessToken'], $raw['expiresIn']);
            }
            $accessToken = $raw['accessToken'];
        } else {
            echo json_encode(array("state" => 1, "msg" => "access error"));
            die;
        }
        $this->session->set_userdata('accessToken', $accessToken);
        //return $accessToken;
    }
}