<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}


	//用户列表服务
	public function userList()
	{

		$rpp = $this->input->get('rpp');
		$page = $this->input->get('page');
		if (empty($rpp)) {
			$rpp = 20;
		} else {
			$rpp = $this->input->get('rpp');
		}
		if (empty($page)) {
			$page = 1;
		} else {
			$page = $this->input->get('page');
		}
		$this->db->limit($rpp, ($page - 1) * $rpp);
		$offset = ($page - 1) * $rpp;

		$total = $this->db->count_all_results('base_user_info');

		foreach ($this->db->query("SELECT * FROM `base_user_info` LIMIT {$offset},{$rpp}")->result() as $k => $v) {
			$result[$k]['id'] = $v->id;
			$result[$k]['name'] = $v->name;
			$result[$k]['privilege'] = $v->privilege;
			$result[$k]['department'] = $this->db->query("SELECT * FROM `base_department_info` WHERE id=" . $v->base_department_info_id)->row()->name;
            $result[$k]['role'] = $this->db->query("SELECT * FROM `base_role_info` WHERE id=" . $v->base_role_info_id)->row()->name;
            $result[$k]['department_id'] =  $v->base_department_info_id;
            $result[$k]['role_id'] = $v->base_role_info_id;

        }

		echo json_encode(array("state" => 0, "ret" => $result, "total" => $total));

		die;


	}

	//添加用户
	public function addUser()
	{
		$name = $this->input->post("name");
		$base_role_info_id = $this->input->post("base_role_info_id");
		$base_department_info_id = $this->input->post("base_department_info_id");
		$password = md5($this->input->post("password"));
		$privilege =2;
		$result = $this->db->query("INSERT INTO `base_user_info` SET `base_role_info_id`={$base_role_info_id},`base_department_info_id`={$base_department_info_id},`name`='{$name}',`password`='{$password}',`privilege`={$privilege}");
		if ($result) {
			echo json_encode(array("state" => 0, "ret" => 'ok'));
			die;
		} else {
			echo json_encode(array("state" => 1, "ret" => 'db error'));
			die;
		}
	}

	//编辑用户
	public function editUser()
	{
		$id = $this->input->post("id");
		$name = $this->input->post("name");
		$base_role_info_id = $this->input->post("base_role_info_id");
		$base_department_info_id = $this->input->post("base_department_info_id");
//		$password = $this->input->post("password");
//		$privilege = $this->input->post("privilege");
//		$result = $this->db->query("UPDATE `base_user_info` SET `base_role_info_id`={$base_role_info_id},`base_department_info_id`={$base_department_info_id},`name`='{$name}',`password`='{$password}',`privilege`={$privilege} WHERE `id`={$id}");
        $result = $this->db->query("UPDATE `base_user_info` SET `base_role_info_id`={$base_role_info_id},`base_department_info_id`={$base_department_info_id},`name`='{$name}' WHERE `id`={$id}");

        if ($result) {
			echo json_encode(array("state" => 0, "ret" => 'ok'));
			die;
		} else {
			echo json_encode(array("state" => 1, "ret" => 'db error'));
			die;
		}
	}

	//用户登录服务
	public function login()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$row = $this->db->query("SELECT * FROM `base_user_info` WHERE `name` = '{$username}'")->row();
		if($row && $row->password == md5($password)){
			$this->session->set_userdata('username', $row->name);
            $this->session->set_userdata('user_id', $row->id);
            $_SESSION['user_id']=$row->id;
			echo json_encode(array("state" => 0, "msg" => 'ok'));
			die;
		} else {
			echo json_encode(array("state" => 1, "msg" => 'username password error'));
			die;
		}
//		if ($this->input->post('username') == 'admin' && $this->input->post('password') == 'admin') {
//			$this->session->set_userdata('username', 'admin');
//			echo json_encode(array("state" => 0, "msg" => 'ok'));
//			die;
//		} else {
//			echo json_encode(array("state" => 1, "msg" => 'username password error'));
//			die;
//		}
	}

	//用户登出服务
	public function logout()
	{
		$this->session->unset_userdata('username');
		echo json_encode(array("state" => 0, "msg" => 'ok'));
		die;
	}

	//获取用户信息
    public function getUser(){
//        $id = $this->input->post("id");
//        $id = $this->session->userdata('username');
        $id = $_SESSION['user_id'];
        $row = $this->db->query("SELECT * FROM `base_user_info` WHERE `id` = {$id}")->row();
        echo json_encode(array("state" => 0,"ret"=>$row));
        die;
    }

    //删除用户
    public function deleteUser(){
        $id = $this->input->post("id");
        $this->db->where('id', $id);
        $result = $this->db->delete('base_user_info');
        if ($result) {
            echo json_encode(array("state" => 0, "ret" => 'ok'));
            die;
        } else {
            echo json_encode(array("state" => 1, "ret" => 'db error'));
            die;
        }
    }
}
