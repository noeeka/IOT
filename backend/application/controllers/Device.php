<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Device extends CI_Controller {
	public function __construct()
	{
		parent::__construct();


	}
	public function deviceList()
	{
		$result = array();
		foreach ($this->db->query("SELECT * FROM `devices`")->result_array() as $k => $v)
		{
			$tb_temp = array();
			foreach ($v as $tb_k => $tb_v)
			{
				if ( ! $this->is_json($tb_v))
				{
					$tb_temp[$tb_k] = json_decode($tb_v, TRUE);
				} else
				{
					$tb_temp[$tb_k] = $tb_v;
				}
			}
			$result[$k] = $tb_temp;
		}
		echo json_encode(array("state" => 0, "ret" => array_values($result)));
		die;
	}

	//获取设备详细信息服务
	public function getDeviceDetail()
	{
		$device_id = $this->input->get('deviceId');
		$json_string = file_get_contents('./include/device.json');
		$data = json_decode($json_string, TRUE);

		foreach ($data['devices'] as $k => $v)
		{
			if ($v['deviceId'] == $device_id)
			{
				echo json_encode(array("state" => 0, "ret" => $data['devices'][$k]));
				die;
			}
		}
	}

	//设备开锁服务
	public function deviceUnlock()
	{
		$accessTokenRaw=http_request("http://180.76.103.247:9090/NB/api/login","appId=".APPID."&secret=".SECRET,'POST');
		file_put_contents("/root/log","健全成功:".serialize($accessTokenRaw),FILE_APPEND);
		$deviceId = $this->input->post('deviceId');
		$serviceId = "CoverLock";
		$method = "UNLOCK";
		$service_result = http_request("http://180.76.103.247:9090/NB/api/sendCmd", "deviceId=" . $deviceId . "&serviceId=" . $serviceId . "&method=" . $method, 'POST');


		file_put_contents("/root/log",serialize($service_result),FILE_APPEND);
		$result = $this->db->query("UPDATE `devices` SET `lockStatus`=2 WHERE `deviceId` = '{$deviceId}'");

		if ($result)
		{
			echo json_encode(array("state" => 0, "ret" => "success"));
			die;
		}
	}

	//设备关锁服务
	public function deviceLock()
	{
		$deviceId = $this->input->post('deviceId');
		$serviceId = "CoverLock";
		$method = "LOCK";
		$service_result = http_request("http://180.76.103.247:9090/NB/api/sendCmd", "deviceId=" . $deviceId . "&serviceId=" . $serviceId . "&method=" . $method, 'POST');
		$result = $this->db->query("UPDATE `devices` SET `lockStatus`=0 WHERE `deviceId` = '{$deviceId}'");
		if ($result)
		{
			echo json_encode(array("state" => 0, "ret" => "success"));
			die;
		}
	}


	//设备锁杆关闭服务
	public function lockLeverStateOff()
	{
		$deviceId = $this->input->post('deviceId');
		$serviceId = "CoverLock";
		$method = "LOCK";
		$service_result = http_request("http://180.76.103.247:9090/NB/api/sendCmd", "deviceId=" . $deviceId . "&serviceId=" . $serviceId . "&method=" . $method, 'POST');
		$result = $this->db->query("UPDATE `devices` SET `lockLeverState`=0 WHERE `deviceId` = '{$deviceId}'");
		if ($result)
		{
			echo json_encode(array("state" => 0, "ret" => "success"));
			die;
		}
	}

	//设备锁杆打开服务
	public function lockLeverStateOn()
	{
		$deviceId = $this->input->post('deviceId');
		$serviceId = "CoverLock";
		$method = "LOCK";
		$service_result = http_request("http://180.76.103.247:9090/NB/api/sendCmd", "deviceId=" . $deviceId . "&serviceId=" . $serviceId . "&method=" . $method, 'POST');
		$result = $this->db->query("UPDATE `devices` SET `lockLeverState`=1 WHERE `deviceId` = '{$deviceId}'");
		if ($result)
		{
			echo json_encode(array("state" => 0, "ret" => "success"));
			die;
		}
	}
	//判断数据是否为json服务
	private function is_json($string)
	{
		return is_null(json_decode($string));
	}
}
