<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	//部门列表
	public function departmentList()
	{
		$result = $this->db->query("SELECT * FROM `base_department_info` ")->result_array();
//		foreach ($result as $k =>$v){
//            $result[$k]['department'] = $this->db->query("SELECT * FROM `base_department_info` WHERE id=" . $v['parent_id'])->row()->name;
//        }
		$result = self::unlimitedForLayer($result);
//		foreach ($result as $k => $v) {
//			$finial_result[$k]['name'] = $v['name'];
//			$finial_result[$k]['id'] = $v['id'];
//			$finial_result[$k]['parent_id'] = $v['parent_id'];
//            $finial_result[$k]['parent_department'] = '';
////            $finial_result[$k]['department'] = $this->db->query("SELECT * FROM `base_department_info` WHERE id=" . $v['parent_id'])->row()->name;
//
//            if (!empty($v['child'])) {
//				foreach ($v['child'] as $k_child => $v_child) {
//					$finial_result[$k]['children'][$k_child]['name'] = $v_child['name'];
//					$finial_result[$k]['children'][$k_child]['id'] = $v_child['id'];
//					$finial_result[$k]['children'][$k_child]['parent_id'] = $v_child['parent_id'];
//                    $finial_result[$k]['children'][$k_child]['parent_department'] = $this->db->query("SELECT * FROM `base_department_info` WHERE id=" . $v_child['parent_id'])->row()->name;
//				}
//
//			}else{
//                $finial_result[$k]['children']=array();
//            }
//		}

		// 调整 data 的顺序
		echo json_encode(array("state" => 0, "ret" => $result));
		die;

	}

	public function departmentListForUser()
	{
		$result = $this->db->query("SELECT * FROM `base_department_info` ")->result_array();
		$result1 = self::unlimitedForLayerNew($result);
//		foreach ($result1 as $k => $v) {
//			$finial_result[$k]['text'] = $v['name'];
//			$finial_result[$k]['nodeid'] = $v['id'];
//			if (!empty($v['child'])) {
//				foreach ($v['child'] as $k_child => $v_child) {
//					$finial_result[$k]['nodes'][$k_child]['text'] = $v_child['name'];
//					$finial_result[$k]['nodes'][$k_child]['nodeid'] = $v_child['id'];
//				}
//
//			}
//		}
        $nodeid = $this->unlimitedForLevel($result);
        $nodeid = array_column($nodeid,'id');
		// 调整 data 的顺序
		echo json_encode(array("state" => 0, "ret" => $result1,'nodeid'=>$nodeid));
		die;

	}

    function getChild($arr){
        foreach($arr as $key=>$value) {
            $array[]=$value['nodeid'];
            if (is_array($value['nodes'])) {
                foreach ($value['nodes'] as $k => $v) {
                    $array[] = $v['nodeid'];
                }
            }
        }
        return $array;
    }


    //新增部门
	public function addDepartment()
	{
		$name = $this->input->post("name");
		$parent_id = $this->input->post("parent_id") ? $this->input->post("parent_id") : 0;
		$IsExsit = $this->isExsitDepartment($parent_id, $name);
		if ($IsExsit) {
			echo json_encode(array("state" => 2, "ret" => $name . "已存在"));
			die;
		}
		$result = $this->db->query("INSERT INTO `base_department_info` SET `name`='{$name}',`parent_id`='{$parent_id}'");
		if ($result) {
			echo json_encode(array("state" => 0, "ret" => 'ok'));
			die;
		} else {
			echo json_encode(array("state" => 1, "ret" => 'db error'));
			die;
		}
	}

	//修改部门
	public function editDepartment()
	{
		$id = $this->input->post("id");
		$name = $this->input->post("name");
		$parent_id = $this->input->post("parent_id") ? $this->input->post("parent_id") : 0;
		$IsExsit = $this->isExsitDepartment($parent_id, $name);
		if ($IsExsit) {
			echo json_encode(array("state" => 2, "ret" => $name . "已存在"));
			die;
		}
		$result = $this->db->query("UPDATE `base_department_info` SET `name`='{$name}',`parent_id`='{$parent_id}' WHERE id = {$id}");
		if ($result) {
			echo json_encode(array("state" => 0, "ret" => 'ok'));
			die;
		} else {
			echo json_encode(array("state" => 1, "ret" => 'db error'));
			die;
		}
	}

	//删除部门
	public function delDepartment()
	{
		$id = $this->input->post("id");
		$this->db->where('id', $id);
        $this->db->or_where('parent_id',$id);
		$result = $this->db->delete('base_department_info');
		if ($result) {
			echo json_encode(array("state" => 0, "ret" => 'ok'));
			die;
		} else {
			echo json_encode(array("state" => 1, "ret" => 'db error'));
			die;
		}
	}

	//获取部门
	public function getDepartment()
	{
		$id = $this->input->post("id");
		$row = $this->db->query("SELECT * FROM `base_department_info` WHERE `id` = {$id}")->row();
		if ($row->parent_id) {
			$row->parent_department = $this->db->query("SELECT * FROM `base_department_info` WHERE `id` =" . $row->parent_id)->row()->name;
		}
		echo json_encode(array("state" => 0, "ret" => $row));
		die;
	}

	//判重
	private function isExsitDepartment($parent_id, $dept_name)
	{
		$sql = "SELECT  count(*) count FROM `base_department_info` where   `name` ='{$dept_name}'";
		$sql_condition = '';
		if ($parent_id != "") {
			$sql_condition = " and parent_id = {$parent_id}";
		}
		$row = $this->db->query($sql . $sql_condition)->row();
		if ($row->count == 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	//组合多维数组
	function unlimitedForLayer($cate, $name = 'children', $pid = 0)
	{
		$arr = array();
		foreach ($cate as $v) {
			if ($v['parent_id'] == $pid) {
				$v[$name] = $this->unlimitedForLayer($cate, $name, $v['id']);
				$arr[] = $v;
			}
		}
		return $arr;
	}

    function unlimitedForLayerNew($cate, $name = 'nodes', $pid = 0){
        $arr = array();
        foreach ($cate as $k => $v) {
//            $arr[]['text'] = $v['name'];
//            $arr[]['nodeid'] = $v['id'];
            if ($v['parent_id'] == $pid) {
                $v['text'] = $v['name'];
                $v['nodeid'] = $v['id'];
                $v[$name] = $this->unlimitedForLayerNew($cate, $name, $v['id']);
                unset($v['id'],$v['name'],$v['parent_id']);
                $arr[] = $v;
            }
		}
        return $arr;
    }

    //组合一维数组
    function unlimitedForLevel ($cate, $html = '--', $pid = 0, $level = 0) {
        $arr = array();
        foreach ($cate as $k => $v) {
            if ($v['parent_id'] == $pid) {
                $v['level'] = $level + 1;
                $v['html']  = str_repeat($html, $level);
                $arr[] = $v;
                $arr = array_merge($arr, $this->unlimitedForLevel($cate, $html, $v['id'], $level + 1));
            }
        }
        return $arr;
    }


}
