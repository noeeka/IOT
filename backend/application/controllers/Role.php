<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role extends CI_Controller{
	public function __construct()
	{
		parent::__construct();
	}
    //角色列表
	public function roleList(){
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

		$total = $this->db->count_all_results('base_role_info');
		$result = $this->db->query("SELECT * FROM `base_role_info` LIMIT {$offset},{$rpp}")->result();

		echo json_encode(array("state" => 0, "ret" => $result, "total" => $total));

		die;
	}
    //新增角色
	public function addRole(){
		$name = $this->input->post("name");
		$remark = $this->input->post("remark");
		$IsExsit	= $this->isExsitRole("",$name);
		if ($IsExsit)
		{
			echo json_encode(array("state" => 2, "ret" => $name."已存在"));
			die;
		}
		$result = $this->db->query("INSERT INTO `base_role_info` SET `name`='{$name}',`remark`='{$remark}'");
		if ($result) {
			echo json_encode(array("state" => 0, "ret" => 'ok'));
			die;
		} else {
			echo json_encode(array("state" => 1, "ret" => 'db error'));
			die;
		}
	}
    //修改角色
	public function editRole(){
		$id = $this->input->post("id");
		$name = $this->input->post("name");
		$remark = $this->input->post("remark");
		$IsExsit	= $this->isExsitRole($id,$name);
		if ($IsExsit)
		{
			echo json_encode(array("state" => 2, "ret" => $name."已存在"));
			die;
		}
		$result = $this->db->query("UPDATE `base_role_info` SET `name`='{$name}',`remark`='{$remark}' WHERE `id` = ".$id);
		if ($result) {
			echo json_encode(array("state" => 0, "ret" => 'ok'));
			die;
		} else {
			echo json_encode(array("state" => 1, "ret" => 'db error'));
			die;
		}
	}
    //判重
	private function isExsitRole($roleid,$rolename)
	{
		$sql="SELECT  count(*) count FROM `base_role_info` where   `name` ='{$rolename}'";
		$sql_condition = '';
		if($roleid!="")
		{
			$sql_condition  =" and id<> {$roleid}";
		}
		$row = $this->db->query($sql.$sql_condition)->row();
		if ($row->count == 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
    //删除角色
	public function deleteRole(){
		$id = $this->input->post("id");
		$this->db->where('id', $id);
		$result = $this->db->delete('base_role_info');
		if ($result) {
			echo json_encode(array("state" => 0, "ret" => 'ok'));
			die;
		} else {
			echo json_encode(array("state" => 1, "ret" => 'db error'));
			die;
		}
	}
    //获取角色
	public function getRole(){
        $id = $this->input->post("id");
        $row = $this->db->query("SELECT * FROM `base_role_info` WHERE `id` = {$id}")->row();
        echo json_encode(array("state" => 0,"ret"=>$row));
        die;
	}
}
