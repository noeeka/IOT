<?php
include 'phpmysql.php';
file_put_contents("./log",file_get_contents("php://input")."\r\n",FILE_APPEND);
$raw = json_decode(file_get_contents("php://input"), TRUE);

//$test='{"notifyType":"deviceDataChanged","deviceId":"01a65662-dc5d-47a1-9eda-98959d37f6e9","gatewayId":"01a65662-dc5d-47a1-9eda-98959d37f6e9","requestId":null,"service":{"serviceId":"CoverLock","serviceType":"CoverLock","data":{"countLockTongueError":0,"reason":1,"countCommunicationError":1,"openTooLongTime":2,"countMotorBoardReset":0,"lockTongueState":0,"grantCancelTime":1,"eventCode":9,"utc_dataTime":1544181970,"lockArmTime":120,"countInnerOpen":0,"hardwareVersion":1,"lockLeverState":1,"softwareVersion":12,"countOpenClose":18,"dataTime":"2018-12-07T19:26:10+0800"},"eventTime":"20181207T112606Z"}}';
$configArr = array('host' => 'localhost', 'port' => '3306', 'user' => 'root', 'passwd' => 'figbot123', 'dbname' => 'iot');
//$raw = json_decode($test, TRUE);
$mysql = new MMysql($configArr);
file_put_contents("/root/raw_log",json_encode($raw)."\r\n",FILE_APPEND);
//唤醒状态判断
if ($raw['service']['data']['eventCode'] == 1)
	{
	file_put_contents("/root/log","井盖唤醒状态\r\n",FILE_APPEND);
	$deviceId = $raw['deviceId'];
	$mysql->doSql("UPDATE `devices` SET `lockStatus`=1 WHERE `deviceId`='{$deviceId}'");
	die;
}
//命令下发
if ($raw['status'] == "PENDING")
{
	file_put_contents("/root/log","命令下发\r\n",FILE_APPEND);
	$deviceId = $raw['deviceId'];
	$mysql->doSql("UPDATE `devices` SET `lockStatus`=2 WHERE `deviceId`='{$deviceId}'");
	die;
}
//命令执行完毕
if ($raw['result']['resultCode'] == "SUCCESSFUL")
{
	file_put_contents("/root/log","命令执行完毕\r\n",FILE_APPEND);
	$deviceId = $raw['deviceId'];
	$mysql->doSql("UPDATE `devices` SET `lockStatus`=4 WHERE `deviceId`='{$deviceId}'");
	die;
}

//锁销打开
if ($raw['service']['data']['eventCode'] == 9)
{
	file_put_contents("/root/log","锁销打开\r\n",FILE_APPEND);
	$deviceId = $raw['deviceId'];
	$mysql->doSql("UPDATE `devices` SET `lockStatus`=5 WHERE `deviceId`='{$deviceId}'");
	die;
}


//开锁
if ($raw['service']['data']['eventCode'] == 2)
{

	file_put_contents("/root/log","开锁\r\n",FILE_APPEND);
	$deviceId = $raw['deviceId'];
	$mysql->doSql("UPDATE `devices` SET `lockStatus`=6 WHERE `deviceId`='{$deviceId}'");
	die;
}

//井盖长时间打开
if ($raw['service']['data']['eventCode'] == 6)
{
	file_put_contents("/root/log","井盖长时间打开\r\n",FILE_APPEND);
	$deviceId = $raw['deviceId'];
	$mysql->doSql("UPDATE `devices` SET `lockStatus`=7 WHERE `deviceId`='{$deviceId}'");
	die;
}
//关锁
if ($raw['service']['data']['eventCode'] == 3)
{
	file_put_contents("/root/log","关锁\r\n",FILE_APPEND);
	$deviceId = $raw['deviceId'];
	$mysql->doSql("UPDATE `devices` SET `lockStatus`=8 WHERE `deviceId`='{$deviceId}'");
	die;
}

//睡眠
if (isset($raw['service']['data']['isOnline']) && $raw['service']['data']['isOnline'] == 0)
{
	file_put_contents("/root/log","睡眠\r\n",FILE_APPEND);
	$deviceId = $raw['deviceId'];
	$mysql->doSql("UPDATE `devices` SET `lockStatus`=0 WHERE `deviceId`='{$deviceId}'");
	die;
}


//


