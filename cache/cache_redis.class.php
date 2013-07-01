<?php
/**
 * @name XiunoPHP Redis驱动类
 * 
 * @author Andy Lee
 * 
 * @copyright lostman.org
 * 
 * @since redis操作参考：http://www.cnblogs.com/ikodota/archive/2012/03/05/php_redis_cn.html   暂为对muti操作提供支持。
 * 
 */
if(!defined('FRAMEWORK_PATH')) {
	exit('FRAMEWORK_PATH not defined.');
}
class cache_redis implements cache_interface {
     public $conf;
     public function __construct($conf) {
		$this->conf = $conf;
     }
      public function __get($var) {
	    if($var == 'redis') {
		 if(extension_loaded('Redis')) {
		       $this->redis = new Redis;
		 }else {
			throw new Exception('Redis Extension not loaded.');
		 }
		 if(!$this->redis) {
			throw new Exception('PHP.ini Error: Redis extension not loaded.');
		 }
	 	 if($this->redis->connect($this->conf['host'], $this->conf['port'])) {
	 		return $this->redis;
	 	 }else{
	 		throw new Exception('Can not connect to Redis host.');
	 	 }
		}
	}
	
    public function get($key){
      $data = array();
      if(is_array($key)){
	      	foreach($key as $k) {
	      		$arr = $this->redis->get($k);
	      		$arr && $data[$k] = $arr;
	      	}
	      	$data = json_encode($data);
	      	return json_decode($data);	    
        }else{
	       $data =  $this->redis->get($key);
	       return json_decode($data);
       }
    }
    
    public function set($key,$val,$life = 0){
        $val = json_encode($val);
    	if($life==0){
        return  $this->redis->set($key,$val);
       }else{
         return  $this->redis->setex($key,$life,$val);
       }
    }
    public function update($key,$val){
       return $this->redis->set($key,$val);
    }
    public function delete($key){
       return $this->redis->del($key);
    }
    public function truncate($pre = '') {
       return $this->redis->flushdb();
    }
    public function maxid($table, $val = FALSE) {
		$key = $table.'-Auto_increment';
		if($val === FALSE) {
			return intval($this->get($key));
		} elseif(is_string($val) && $val{0} == '+') {
			$val = intval($val);
			$val += intval($this->get($key));
			$this->set($key, $val);
			return $val;
		} else {
			 $this->set($key, $val);
			 return $val;
		}
	}
	
    public function count($table, $val = FALSE) {
		$key = $table.'-Rows';
		if($val === FALSE) {
			return intval($this->get($key));
		} elseif(is_string($val)) {
			if($val{0} == '+') {
				$val = intval($val);
				$n = intval($this->get($key)) + $val;
				$this->set($key, $n);
				return $n;
			} else {
				$val = abs(intval($val));
				$n = max(0, intval($this->get($key)) - $val);
				$this->set($key, $n);
				return $n;
			}
		} else {
			$this->set($key, $val);
			return $val;
		}
	}    
    
}
?>
