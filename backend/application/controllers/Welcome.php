<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends AdminBase
{
    public function __construct()
    {
        parent::__construct();
    }
    //获取设备列表服务
    public function index()
    {

        $result=getOceanConnectAPI($this->session->userdata('accessToken'),"/iocm/app/dm/v1.4.0/devices/","select=imsi&pageNo=1");
        echo $result;
        die;
    }
}
