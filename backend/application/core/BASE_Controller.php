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
    	print_r("dddd");
        parent::__construct();
        //$m = new Memcached();
        //$m->addServer('127.0.0.1', 11211);
        $this->load->driver('cache');
        $accessTokenRaw=http_request("http://180.76.103.247:9090/NB/api/login","appId=".APPID."&secret=".SECRET,'POST');
        $accessToken = "";
        if ($accessTokenRaw['data']) {
            $accessToken = $this->cache->memcached->get('accessToken');
            if (empty($accessToken)) {
                $this->cache->memcached->save('accessToken', $accessTokenRaw['data'], 3600);
            }
            $accessToken = $accessTokenRaw['data'];
        } else {
            echo json_encode(array("state" => 1, "msg" => "access error"));
            die;
        }
        
        $this->session->set_userdata('accessToken', $accessToken);
        //return $accessToken;
    }
}
