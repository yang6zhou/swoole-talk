<?php
ini_set("display_errors", "On"); 
class sws { 
	
	private  $serv;
	private $redis;
	//报告所有错误
	public function __construct(){ 
		echo '初始化参数...';
		$this->serv = new swoole_websocket_server("0.0.0.0", 9501); 
		$this->redis = new Redis();
		$this->redis->connect('127.0.0.1', 6379);
		//$redis->flushAll();exit;
		$this->serv->set([
		            'daemonize' => 1, //是否开启守护进程
		            'worker_num' => 8, //实际需要去设定
		            'log_file' => '/var/www/html/swoole.log'
		        ]); 
		        
		$this->serv->on('open', function($server, $req) {
		   
		    $this->redis->sAdd('fd', $req->fd);
		});
		
			$this->serv->on('message', function($server, $frame) {
 
var_dump($frame);
 
			
			foreach($server->connections  as $fd){
				echo "received message: {$frame->data}\n";
	    			$server->push($fd, json_encode($frame->data." : ".date('Y-m-d h:i:s', time()).'\n'));
			}
		    
		});
		
	   $this->serv->on('WorkerStart', array($this, 'onWorkerStart'));
	   $this->serv->on('Connect', array($this, 'onConnect'));
	  
		
	   $this->serv->on('close', function($server, $fd) {
		    echo "connection close: {$fd}\n";
		});
		
	   	$this->serv->start();
	}
	public function onWorkerStart( $serv , $worker_id) {
		 swoole_set_process_name("apathyss");
		 echo '执行到这里....1\n';
		 
	}
	public function onConnect( $serv, $fd, $from_id ) {
		echo '执行到这里..2\n';
		echo "Client {$fd} connect\n";
		
	}
	public function onReceive(swoole_websocket_server $serv, $fd, $from_id, $data ) {
		echo '执行到这里..3\n';
		echo "Get Message From Client {$fd}:{$data}\n";
	}
 
}
new sws();
 
 