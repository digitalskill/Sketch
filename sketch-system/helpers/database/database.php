<?php
/*
 * Database
 * Created By Kevin Dibble, (c) 2010
 * Part of the sketch framework
 * 
 * Purpose - To Allow the quick creation of database inserts, additions,updates and deletes for the sketch framework
 */
class ACTIVERECORD{
	private $record 	= "";
	private $result 	= "";
	private $tablename	= "";
	private $query		= "";
	private $data		= "";
	private $quData		= "";
	private $dbType		= "";
	function __construct($r,$data=""){
		$this->dbType = getSettings("dbtype");
		if($this->dbType=="pgsql"){
			$r = str_replace(array("`","column_key"),array('"',"column_default"),$r);
		}
		try{
			$this->query = $r;
			$this->data = $data;
			$this->clearQuestions();
			$this->record = sketch("db")->prepare($this->query);
			if(is_array($this->data)){
				foreach($this->data as $key => $value){
					if(stripos($this->query,$key)!==false || is_numeric($key)){
						$this->record->bindParam($key, $value, PDO::PARAM_STR);
					}
				}
			}
			if($this->record){
				$this->record->execute();
			}else{
				$string = 'PDO Exception Caught. ';
				$string .= 'Error with the database: <br />';
				if(adminCheck() || strpos($_SERVER['HTTP_HOST'],"localhost")!==false){
					$string .= 'SQL Query: '.$this->query ."<br />";
					$string .= "DATA<br />";
					foreach((array)$data as $key => $value){
						$string .= $key ." = ".$value."<br />";	
					}
				}
				loadError("db".$string);
			}
		}catch (PDOException $e){
			$string = "Database Error<br />";
			if(adminCheck() || strpos($_SERVER['HTTP_HOST'],"localhost")!==false){
				$string = 'PDO Exception Caught. ';
				$string .= 'Error with the database: <br />';
				$string .= 'SQL Query: '.$this->query ."<br />";
			}
			$string .= 'Error: ' . $e->getMessage();
			rollBack();
			loadError("db",$string);
		}
	}
	public function clearQuestions(){
		$this->query = str_replace("?","_##-",$this->query);
	}
	public static function keeprecord($r,$data=''){
		$me = new ACTIVERECORD($r,$data);
    	return $me;
	}
	function __get($item){
		if($item=="column_key" || $item=="data_type" || $item=="column_name"){
			if($this->dbType=="pgsql" && $item=="column_key"){
				$item = "column_default";
				if(isset($this->result[$item]) && strpos(strtolower($this->result[$item]),"nextval")!==false){
					$this->result[$item]="pri";
				}	
			}else{
				if($this->dbType=='mysql'){
					$item = $item=="column_name"? "Field" : ($item=="column_key"? "Key" : ($item=="data_type"?"Type": $item));
				}
			}
		}
		if(isset($this->result[$item])){
			$content = str_replace("_##-","?",trim(stripslashes($this->result[$item])));
			return strpos($content,"}") === false ? $content : preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $content);
		}else{
			if(isset($this->$item)){
				return $this->$item;
			}else{
				return false;
			}
		}
	}
	function __set($item,$value){
		$this->result[$item]=$value;
	}
	function rowCount(){
		return $this->record->rowCount();	
	}
	function advance(){
		$this->result = $this->record->fetch();
		return $this->result;
	}
	function seek($row=0){
		try{
			$this->record = sketch("db")->prepare($this->query);
			if(is_array($this->data)){
				foreach((array)$this->data as $key => $value){
					if(strpos($this->query,$key)!==false){
						$this->record->bindParam($key, $value, PDO::PARAM_STR);
					}
				}
			}
			$this->record->execute();
		}catch (PDOException $e){
			$string = "Database Error<br />";
			if(adminCheck() || strpos($_SERVER['HTTP_HOST'],"localhost")!==false){
				$string = 'PDO Exception Caught. ';
				$string .= 'Error with the database: <br />';
				$string .= 'SQL Query: '.$this->query ."<br />";
			}
			$string .= 'Error: ' . $e->getMessage();
			rollBack();
			loadError("db",$string);
		}
	}
	function free(){
		unset($this->result);
		unset($this->record);
		unset($this);
	}
}
function lastInsertId(){
	return sketch("db")->lastInsertId();
}
function startTransaction(){
	sketch("db")->beginTransaction();
}
function commitTransaction(){
	sketch("db")->commit();
}
function rollBack(){
	try{
		sketch("db")->rollBack();
	}catch (PDOException $e){
		// Ignore Rollback error (will errors if no transaction has started)
	}
}
function getTable($table,$id){
	return	getData($table,"",$id,"",1);
}

function describeTable($table){
	if(getSettings("dbtype")=="mysql"){
		$SQL = "DESCRIBE ".$table;
	}else{
		$table = str_replace("`","",$table);
		$SQL = "SELECT column_name,data_type,column_key FROM INFORMATION_SCHEMA.COLUMNS where table_name = ".sketch("db")->quote($table);
	}
	return  ACTIVERECORD::keeprecord($SQL);	
}

function getData($tables,$sel="*",$where="",$sort="",$limit=""){
	$oldwhere	= ltrim(str_replace("where","",strtolower(trim($where))),"and");
	$where		= "";
	$tables 	= explode(",",$tables);
	$tablerows  = array();
	$from		= "";
	$lastTable	= "";
	$sort		= ($sort!= "" && strpos(strtolower($sort),"order by")===false)?  " ORDER BY ".$sort : $sort;
	$thisTable	= array();	
	$allTables	= "";
	foreach($tables as $key => $value){
		if(strpos($where,$value.".")!==false){
			$where = str_replace($value.".",getSettings('prefix').$value.".",$where);	
		}
		if(strpos($sort,$value.".")!==false){
			$sort = str_replace($value.".",getSettings('prefix').$value.".",$sort);	
		}
		if(strpos($sel,",".$value.".")!==false && strpos($sel,"*")===false){
			$sel = str_replace($value.".",getSettings('prefix').$value.".",$sel);	
		}
		if(strpos($oldwhere,$value.".")!==false){
			$oldwhere = str_replace($value.".",getSettings('prefix').$value.".",$oldwhere);	
		}
		$value = (strpos($value,getSettings('prefix'))!==false)? $value : getSettings('prefix').$value;
		$from .= (($from=="")? "": ",")."`".$value."`";
		
		if(isset($tables[1])){
			$r = describeTable("`".$value."`");
			$thisTable[$value] = array();
			while($r->advance()){
				if(strpos($sort,$r->column_name)!==false && strpos($sort,"`".$r->column_name."`")===false){
						$sort = str_replace($r->column_name,"`".$value."`.`".$r->column_name."`",$sort);
				}
				if($r->column_key!=""){
					$thisTable[$value][$r->column_name] = "`".$value."`.`".$r->column_name."`";	
					foreach($thisTable as $k => $v){
						if(isset($v[$r->column_name])){
							if($v[$r->column_name] != "`".$value."`.`".$r->column_name."`"){
								$where .= (($where=="")? "" : " AND "). $v[$r->column_name]."="."`".$value."`.`".$r->column_name."`";
							}
						}
					}
				}
				if(strtolower($r->column_key)=="pri" && $sel=="" && strpos($sel,"*")===false){
					$sel .= $value."."."`".$r->column_name."`";
				}
			}
			$r->free();
		}
	}
	$limit		= ($limit != "")? "LIMIT ".$limit : "";
	$where		= ($where!= "")? "WHERE ".$where : "";
	$oldwhere	= ($oldwhere!="")? (($where=="")? " WHERE " : " AND "  ).$oldwhere : "";
	$sel		= ($sel=="")? "*" : $sel;
	$SQL		= trim(str_replace(array("  ",getSettings('prefix').getSettings('prefix')),array(" ",getSettings('prefix')),"SELECT ".$sel." FROM ".$from." ".$where." ".$oldwhere." ".$sort." ".$limit));
	return		ACTIVERECORD::keeprecord($SQL);
}
function setData($tablename,$data="text",$where=""){
	$SQL = updateDB($tablename,$data,$where);
	if($SQL){
		return ACTIVERECORD::keeprecord($SQL);
	}
	return false;
}
function addData($tablename,$data){
	$SQL = insertDB($tablename,$data);
	if($SQL){
		return ACTIVERECORD::keeprecord($SQL);
	}
	return false;
}
function removeData($tablename,$data){
	$SQL = deleteRecord($tablename,$data);
	if($SQL){
		$SQL = str_replace("LIMIT 1","",$SQL); 
		return ACTIVERECORD::keeprecord($SQL);
	}else{
		return false;	
	}
}
function deleteRecord($tablename,$data){
	$tablename 	= "`". ((strpos($tablename,getSettings('prefix'))!==false)? $tablename : getSettings('prefix').$tablename) ."`";
	$SQL ="DELETE FROM ".$tablename." WHERE ";
	$where = "";
	if(is_array($data)){
		$start 	= microtime(true);
		$r = describeTable($tablename);
		setQueryData($start,$SQL);
		while($r->advance()){
			if(isset($data[$r->column_name])){
				$where = "`".$r->column_name."`=".escapeDBInsert($data[$r->column_name],$r->data_type);
			}
		}
	}else{
		$where = $data;
	}
	return $SQL.$where." LIMIT 1";
}
function updateDB($tablename,$data="text",$where=""){
	$tablename 	= "`". ((strpos($tablename,getSettings('prefix'))!==false)? $tablename : getSettings('prefix').$tablename) ."`";
	$updateSQL ="UPDATE ".$tablename." SET ";
	$string = "";
	$r = describeTable($tablename);
	while($r->advance()){
		if(strtolower(trim($r->column_key))== "pri"){
			if(stripos($where,"WHERE")===false && isset($data[$r->column_name])){
				$where = " WHERE `".$r->column_name."`=".escapeDBInsert($data[$r->column_name],$r->data_type);
			}
		}else{
			if(isset($data[$r->column_name])){
				$string .= (($string=="")? "" : ", ")."`".$r->column_name."`=";
				$string .= escapeDBInsert($data[$r->column_name],$r->data_type);
			}
		}
	}
	if(trim($where) != ""){
		return $updateSQL.trim($string,",")." ".$where;
	}else{
		return false;	
	}
}
function insertDB($tablename,$data){
	$tablename 	= "`". ((strpos($tablename,getSettings('prefix'))!==false)? $tablename : getSettings('prefix').$tablename) ."`";
	$updateSQL 	="INSERT INTO ".$tablename." (#1) VALUES (#2)";
	$feilds 	= "";
	$values 	= "";
	$r 			= describeTable($tablename);
	while($r->advance()){
		if(isset($data[$r->column_name]) && strtolower(trim($r->column_key))!= "pri"){
			$values .= ($values=="")? "" : ", ";
			$values .= "`".$r->column_name."`";
			$feilds	.= ($feilds=="")? "" : ", ";
			$feilds .= escapeDBInsert($data[$r->column_name],$r->data_type);
		}
	}
	$SQL = str_replace(array("#1","#2",),array($values,$feilds),$updateSQL);
	return $SQL;
}

function escapeDBInsert($item,$type="text"){
	$item = trim(stripslashes($item));
	list($type,) = explode("(",strtolower(trim($type)));
	switch ($type){
				case "int":
				case "tinyint":
				case "bigint":
				case "mediumint":
				case "smallint":
							$item = intval($item);
							break;
				case "decimal":
				case "float":
				case "double":
				case "real":
							$item = floatval($item);
							break;
				case "date":
							if(strpos($item,"-")!==false){
								list($y,$m,$d) = explode("-",$item);
								$item = sketch("db")->quote(intval($y)."-".intval($m)."-".intval($d));
							}else{
								$item = "null"; //sketch("db")->quote("0000-00-00");
							}
							break;
				default:
							$item = sketch("db")->quote($item);
	}
	return $item;
}
function dbconnect($host,$database,$user,$pass,$type){
	if($type=="NO"){
	    return false;
	}
	global $sketch;
	try{
		$pdo = new PDO(
		   	$type.":host=".$host.";dbname=".$database,
		   	$user,
		  	$pass,
		 		array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",PDO::ATTR_PERSISTENT => true)
			);
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		return $pdo;
	}catch (PDOException $e){
		@ob_end_clean();								// Clear the output buffer and headers
		header("HTTP/1.1 500 Internal Server Error");
		$string ='<div style="border:1px solid #e2e2e2;padding:10px;margin:auto;width:80%;">
		<h3>The database has an error</h3>
		<p>Database not Found</p>
		</div>';
		die($string);
	}
}