<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  Permission extends CI_Controller{
	public function __construct()
	{
		parent::__construct();
	}

//	public function permissionList(){
//		$rpp = $this->input->get('rpp');
//		$page = $this->input->get('page');
//		if (empty($rpp)) {
//			$rpp = 20;
//		} else {
//			$rpp = $this->input->get('rpp');
//		}
//		if (empty($page)) {
//			$page = 1;
//		} else {
//			$page = $this->input->get('page');
//		}
//		$this->db->limit($rpp, ($page - 1) * $rpp);
//		$offset = ($page - 1) * $rpp;
//		$total = $this->db->count_all_results('base_permission_info');
//		foreach ($this->db->query("SELECT * FROM `base_permission_info` LIMIT {$offset},{$rpp}")->result() as $k => $v) {
//			$result[$k]['permission'] = $v->permission;
//			$result[$k]['role_name'] = $this->db->query("SELECT * FROM `base_role_info` WHERE id=" . $v->base_role_info_id)->row()->name;
//		}
//
//		echo json_encode(array("state" => 0, "ret" => $result, "total" => $total));
//
//		die;
//	}
//
//	public function addPermission(){
//		$base_role_info_id = $this->input->post("base_role_info_id");
//		$permission = $this->input->post("permission");
//		$result = $this->db->query("INSERT INTO `base_permission_info` SET `base_role_info_id`={$base_role_info_id},`permission`={$permission}")->result();
//		if ($result) {
//			echo json_encode(array("state" => 0, "ret" => 'ok'));
//			die;
//		} else {
//			echo json_encode(array("state" => 1, "ret" => 'db error'));
//			die;
//		}
//	}


}
