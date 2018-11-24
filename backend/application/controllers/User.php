<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    //用户登录服务
    public function login()
    {

        if( $this->input->post('username')=='admin' && $this->input->post('password')=='admin')
        {
            $this->session->set_userdata('username', 'admin');
            echo json_encode(array("state"=>0,"msg"=>'ok'));
            die;
        }else{
            echo json_encode(array("state"=>1,"msg"=>'username password error'));
            die;
        }
    }

    //用户登出服务
    public function logout(){
        $this->session->unset_userdata('username');
        echo json_encode(array("state"=>0,"msg"=>'ok'));
        die;
    }
}
