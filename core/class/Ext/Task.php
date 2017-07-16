<?php
/**
 * 进程任务管理器
 */
class Ext_Task {
	/**
	 * UNIX/LINUX 下获取正在运行的进程 ID 数组
	 * 
	 * @param string $script 执行的脚本名
	 * @param string $bin 命令中的解析器路径, 如 ./php  python
	 * @return array 
	 */
	public static function getTaskId ($script, $bin) {
		$grepScript = preg_quote($script);
		exec ("ps -ef | grep '$grepScript'", $output);
		$procIds = array ();
		foreach ($output as $opKey => $opItem) {
			if (strstr ($opItem, "$bin $script")) {
				preg_match ("/^[^ ]+[ ]+([0-9]+).*$/", $opItem, $pregMatch);
				array_push ($procIds, $pregMatch[1]);
			}
		}
		return $procIds;
	}

	/**
	 * UNIX/LINUX 下获取正在运行的进程的信息
	 * 
	 * @param string $script 执行的脚本名
	 * @param string $bin 命令中的解析器路径, 如 ./php  python
	 * @return array 
	 */
	public static function getTaskInfo($script, $bin) {
		$grepScript = preg_quote($script);
		exec ("ps -ef | grep '$grepScript'", $output);
		$countProc = 0;
		$taskInfo = array('count' => 0, 'pid' => array(), 'cid' => array());
		foreach ($output as $opKey => $opItem) {
			if (strstr ($opItem, "$bin $script")) {
				preg_match ("/^[^ ]+\s+(\d+).*cid\=(\d+)$/", $opItem, $pregMatch);
				if (isset($pregMatch[1])) $taskInfo['pid'][] = $pregMatch[1];
				if (isset($pregMatch[2])) $taskInfo['cid'][] = $pregMatch[2];
				$taskInfo['count']++;
			}
		}
		return $taskInfo;			
	}
	
	/**
	 * UNIX/LINUX 下获取正在运行的进程数量
	 * 
	 * @param string $script 执行的脚本名
	 * @param string $bin 命令中的解析器路径, 如 ./php  python
	 * @return integer  
	 */
	public static function getTaskCount ($script, $bin) {
		$grepScript = preg_quote($script);
		exec ("ps -ef | grep '$grepScript'", $output);
		$countProc = 0;
		foreach ($output as $opKey => $opItem) {
			if (strstr ($opItem, "$bin $script")) $countProc ++;
		}
		return $countProc;
	}
	
	/**
	 * 分析任务执行时间
	 * 
	 * @param string $execTime 任务执行时间 
	 * @return array 
	 */
	public static function paserExecTime($execTime) {
		$execTime = Ext_Array::serialToArray($execTime);
		foreach ($execTime as $key => $value) {
			$execTime[$key] = explode(',', $value);	
		}
		return $execTime;
	}	
	
	/**
	 * 检测是否到达执行时间
	 * 
	 * @param mixed $nowTime 当前时间
	 * @param mixed $execTime 执行时间 
	 * @return Boolean 
	 */
	public static function checkExecTime($nowTime, $execTime) {
		$checked = true;
		foreach ($nowTime as $key => $val) {
			if (isset($execTime[$key]) && !in_array($val, $execTime[$key])) {
				$checked = false;	
			}
		}
		return $checked;
	}	
	
	/**
	 * 启动主控进程
	 * 
	 * @param array $taskConfig 进程控制参数
	 * @param object $db 进程控制表所在的数据库对象引用
	 * @return void 
	 */
	public static function start($taskConfig, & $db) {
		$phpBin = $taskConfig['php_bin'];
		$waitTime = $taskConfig['wait_time'];
		$taskControlTbale = $taskConfig['table'];
		$logPath = Fn::$config['data_path'] . 'log/task_log/';
		
		// 检查主控运行状态
		//if ($_SERVER['argc'] > 1) {
		//	exit('I do not need parameters...' . chr(10));	
		//}
		$mainScript = $_SERVER['PHP_SELF'];
		$mainInfo = Ext_Task::getTaskInfo($mainScript, $phpBin);
		$mainCount  = $mainInfo['count'];
		if ($mainCount > 1) {
			echo "已经在运行中..";
			exit();
			//exit('I am Already Run...' . chr(10));	
		}
		echo "正在执行任务中<br/>";
		// 监控任务进程
		while(true) {
			$startUsec = microtime(true);
			$time  = time();
			$nowTime = Ext_Date::getInfo($time);
			
			// 获取未暂停的进程列表
			$taskList = $db->table($taskControlTbale)->getAll('id');
			foreach ($taskList as $taskId => $taskControl) {			
				$maxCount = max(1, $taskControl['max_count']);
				$taskInfo = Ext_Task::getTaskInfo($taskControl['script'], $phpBin);
				$taskCount = $taskInfo['count'];
				
				// 暂停状态
				if ($taskControl['status'] < 0) {
					if ($taskCount > 0) {
						$pids = Ext_Task::getTaskId($taskControl['script'], $phpBin);
						foreach ($taskInfo['pid'] as $pid) {
							$cmd = "kill -9 $pid";
							echo '[' . Ext_Date::format($time) . ']' . $cmd . chr(10);
							exec($cmd);	
						}
					}
					continue;	
				}
				
				// 子进程已经存在
				if ($taskCount >= $maxCount) {
					continue;
				}
				
				
				// 定时执行的进程验证执行时间
				if ('keep' != $taskControl['type']) { 
					//检查条件是否满足
					/*
						  分     小时    日    月      星期  年  第几周
						  0-59   0-23   1-31   1-12     0-6    
						 “*”代表取值范围内的数字, “/”代表”每”,  “-”代表从某个数字到某个数字, “,”分开几个离散的数字 
					*/
					$timePart=0;
					switch($taskControl['type']){
						case "crontab" :if(!Ext_Task::checkCrontab($taskControl['exec_time'],$time))continue;break;//Linux crontab
						case "minute" :$timePart = 60;break;
						case "hour" :$timePart = 3600;break;
						case "day" :$timePart = 86400;break;
						case "month" :$timePart = 86400*30;break;//本来想按月份的，但参数不多，无法做了，只能暂用30天了,要不然就用当前月来计算，但月底时又不好计
						case "Fnk" :continue;break;
						default:continue;break;
					}
					if($timePart>0){
						// 当前时间已经执行过
						if ($time < strtotime($taskControl['last_exec_time']) + $timePart) {
							continue;	
						}
						//检查是否满足条件
						// 未满足执行时间
						$checked = Ext_String::formula($taskControl['exec_time'], $nowTime);
						if (false == $checked) {
							continue;
						}
					}
				}
			
				// 更新进程最后执行时间
				$timeStr = Ext_Date::format($time);
				$db->begin();
				$db->table($taskControlTbale)
					->where(array('id' => $taskId))
					->update(array('last_exec_time' => $timeStr, 'status' => 1));
				$db->commit();
				
				// 启动进程
				$logFile = Logs::getLogFile('task_' . $taskId, $time);
				$cids = array_diff(range(1, $maxCount), $taskInfo['cid']);
				foreach ($cids as $cid) {
					$cmd = "$phpBin {$taskControl['script']} cid=$cid >> $logFile &";
					echo '[' . Ext_Date::format($time) . ']' . $cmd . chr(10);
					exec($cmd);
				}
			}
			$endUsec = microtime(true);
			$waitUsec = 1000000 * ($waitTime - ($endUsec - $startUsec));
			if ($waitUsec > 0) {
				usleep($waitUsec);	
			}
		}
	}
	
	
	
	/*
		echo Ext_Task::checkCrontab($exec_time,time()) ? "OK" : "no";
		  分     小时    日    月      星期  年  第几周
		  0-59   0-23   1-31   1-12     0-6    
		 “*”代表取值范围内的数字, “/”代表”每”,  “-”代表从某个数字到某个数字, “,”分开几个离散的数字 
	*/
	public static function checkCrontab($rule,$now_time){
		$now_y=intval(date("Y",$now_time));$now_m=intval(date("m",$now_time));$now_d=intval(date("d",$now_time));
		$now_h=intval(date("H",$now_time));$now_i=intval(date("i",$now_time));
		$now_W=intval(date("W",$now_time));$now_w=intval(date("w",$now_time));
		$time_a=split(" ",$rule);$oknum=0;$al=count($time_a);
		if(Ext_Task::checkCrontabRule($time_a[0],$now_i,0,59,0))$oknum++;//分
		if(isset($time_a[1]))if(Ext_Task::checkCrontabRule($time_a[1],$now_h,0,23,0))$oknum++;//时
		if(isset($time_a[2]))if(Ext_Task::checkCrontabRule($time_a[2],$now_d,1,31,1))$oknum++;//日
		if(isset($time_a[3]))if(Ext_Task::checkCrontabRule($time_a[3],$now_m,1,12,1))$oknum++;//月
		if(isset($time_a[4]))if(Ext_Task::checkCrontabRule($time_a[4],$now_w,0,6,0))$oknum++;//周几
		if(isset($time_a[5]))if(Ext_Task::checkCrontabRule($time_a[5],$now_y,1,9999,0))$oknum++;//年
		if(isset($time_a[6]))if(Ext_Task::checkCrontabRule($time_a[6],$now_y,1,54,0))$oknum++;//第几周
		//echo "-------------<br/>oknum:{$oknum}/{$al}<br/>-------------<br/>";
		return $oknum==$al ? true : false;
	}
	public static function checkCrontabRule($cn,$nn,$min,$max,$di){
		if($cn=="*")return true;//*,全部通过
		if(is_numeric($cn))if($nn==$cn){return true;}else{return false;}//数字，==
		$cl=array();if(!!strstr($cn,"/")){list($c1,$c2)=split("/",$cn);for($i=0;$i<100;$i++){if(($i*$c2+$di)<=$max){$cl[]=$i*$c2+$di;}else break;}
		}elseif(!!strstr($cn,"-")){list($c1,$c2)=split("-",$cn);for($i=$c1;$i<=$c2;$i++)$cl[]=$i+$di;
		}elseif(!!strstr($cn,",")){$cl=split(",",$cn);}else{return false;}
		//echo "<br/>";print_r($cl);echo "<br/>";
		return in_array($nn,$cl) ? true : false;
	}
}