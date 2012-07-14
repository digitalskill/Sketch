<?
/*
 * sock Socket PHP Class
 * Copyright (C) 2006 Nic Stevens. 
 * sock Socket PHP communication class
 * 
 * Constructor:
 *    new sock(),
 *    new sock(array('address' => address of host,
 *                   'port' => port of host,
 *                   'timeout' (optional),
 *                   'transport', (tcp://, udp://, ssl://, tls://) (ssl and tls
 *                                 not tested)
 *                   ));
 * methods:
 *    bool connect(array('address' => address of host,
 *                   'port' => port of host,
 *                   'timeout' (optional),
 *                   'transport', (tcp://, udp://, ssl://, tls://) (ssl and tls
 *                                 not tested)
 *                   ));
 *    NOTE: connect is called when constructor is called with connection array
 *    bool send_data($data) : sends string $data
 *    bool get_data(&$data) : gets remote data into $data
 *    bool get_timeout() : returns timeout value
 *    bool set_timeout(integer) sets timeout
 *    bool is_conn() returns connect state
 *    void  disconnect();
 */
define('SC_TIMEOUT',10);
define('SC_ST_NCONN',1);
define('SC_ST_CONN',2);

class sock {
    var $con;
    var $parms;
    var $state;

    function __construct($cdata = array()) {
	$this->state = SC_ST_NCONN;
	if(count($cdata)) {
	   $this->parms = $cdata;
	   try {
	       $this->connect();
	   } catch(Exception $E) {
	       throw($E);
	   }
	}
    }
    function is_conn() {
	if(!isset($this->con) || !is_resource($this->con)) 
	  return false;
	return ($this->state == SC_ST_CONN) ? true : false;
    }
    function connect() {
	if(!is_array($this->parms))
	    throw new Exception("No connection data specified");
	extract($this->parms);
	if(!isset($address))
	    throw new Exception("No connection data specified");
	if(isset($transport))
	  $address = $transport . $address;
	if(!isset($port))
	  $port == 0;
	else 
	  $address .= ":" . $port;
	if(($this->con = fsockopen($address,$port,$errno,$errstr)) === false) {
	    $msg = "cannot open $address:$port: " . $errstr;
	    throw new Exception($msg,$errno);
	}
	
	if(!isset($timeout) || $timeout == 0)
	  $timeout = SC_TIMEOUT;
	$this->parms['timeout'] = $timeout;
	stream_set_timeout($this->con,$timeout);
	$this->state = SC_ST_CONN;
    }
    function get_timeout() {
	if(!isset($this->parms['timeout']))
	  return false;
	return $this->parms['timeout'];
    }
    function set_timeout($timeout) {
	$this->parms['timeout'] = ($timeout == 0) ? SC_TIMEOUT : timeout;
	if($this->is_conn())
	  stream_set_timeout($this->con,$timeout);
    }
    function send_data($data) {
	str_replace("\n","\r\n",$data);
	if(!$this->is_conn())
	  return false;
	$r = fwrite($this->con,$data);
	return $r;
    }
    function get_data(&$data) {
	$data = '';		
	$line   = '';
	if($this->is_conn()) {
	    while(strpos($data, "\n") === false) {
		if(($line = fgets($this->con, 512)) == false) {
		    if($data != '') 
			break;
		    return false;
		}
		$data .= $line;
	    }
	    $data = str_replace("\r\n","\n",$data);
	    return true;
	}
	return false;
    }
    function disconnect() {
	fclose($this->con);
	$this->con = false;
	$this->state = SC_ST_NCONN;
    }
}
?>
