<?php

class DBCONFIG{
    private $dbHost     = "localhost";
    private $dbUsername = "root";
    private $dbPassword = "admin";
    private $dbName     = "advloginsystem";
    
    public function __construct(){ 
        define('DEV_MODE', TRUE);
        ini_set('display_errors', DEV_MODE?1:0);
        ini_set('disply_startup_errors', DEV_MODE?1:0);
        ini_set('log_errors', DEV_MODE?0:1);
        DEV_MODE?error_reporting( E_ALL & ~E_NOTICE ):error_reporting(0);
        if(!isset($this->dbconfig)){
            
            $conn = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);

            $errno = $conn->connect_errno;
            $error = $conn->connect_error;

            if ($errno) {
                echo "Error: [ ".$errno." ] ".$error;
            } elseif(!$errno) {
                $this->dbconfig = $conn;                
            } else {
                if ( !DEV_MODE ) {
                    error_log( "[ " . date('m-d-Y H:i:s') . " ]: #" . $errno . " : " . $error . PHP_EOL, 3, 'errors/error_log.log' );
                    // 1 and Email to email messages
                } else {
                    die("Error: [ " . $errno . " ] " . $error);
                }
            }
        
        }
        return $this->dbconfig;     
    }
}

/* 
 * DB Class 
 * This class is used for database related (connect, insert, update, and delete) operations 
 */ 
class DB EXTENDS DBCONFIG { 
    public function __construct(){ 
        $this->db = parent::__construct();
    }
    public function __destruct() {
        if ( $this->db !== null ) { $this->db = null; }
    }
     
    /**
     * Returns rows from the database based on the conditions 
     * @param string name of the table 
     * @param array select, where, order_by, limit and return_type conditions 
     */ 
    public function getRows($table,$conditions = array()){ 
        $sql = 'SELECT '; 
        $sql .= array_key_exists("select",$conditions)?$conditions['select']:'*'; 
        $sql .= ' FROM '.$table; 
        if(array_key_exists("where",$conditions)){ 
            $sql .= ' WHERE '; 
            $i = 0; 
            foreach($conditions['where'] as $key => $value){ 
                $pre = ($i > 0)?' AND ':''; 
                $sql .= $pre.$key." = '".$value."'"; 
                $i++; 
            } 
        }
        
        if(array_key_exists("like", $conditions) && !empty($conditions['like'])) {
            $sql .= (strpos($sql, 'WHERE') !== FALSE)?' AND ':' WHERE ';
            $i = 0;
            $likeSQL = '';
            foreach ($conditions['like'] as $key => $value) {
                $pre = ($i > 0)?' AND ':'';
                $likeSQL .= $pre.$key." LIKE '%".$value."%'";
                $i++;
            }
            $sql .= '('.$likeSQL.')';
        }

        if(array_key_exists("like_or", $conditions) && !empty($conditions['like_or'])){
            $sql .= (strpos($sql, 'WHERE') !== FALSE)?' AND ':' WHERE ';
            $i = 0;
            $likeSQL = '';
            foreach ($conditions['like_or'] as $key => $value) {
                $pre = ($i > 0)?' OR ':'';
                $likeSQL .= $pre.$key." LIKE '%".$value."%'";
                $i++;
            }
            $sql .= '('.$likeSQL.')';
        }
         
        if(array_key_exists("order_by",$conditions)){ 
            $sql .= ' ORDER BY '.$conditions['order_by'];  
        } 
         
        if(array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){ 
            $sql .= ' LIMIT '.$conditions['start'].','.$conditions['limit'];  
        }elseif(!array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){ 
            $sql .= ' LIMIT '.$conditions['limit'];  
        } 
        
        $query = $this->db->query($sql);
         
        if(array_key_exists("return_type",$conditions) && $conditions['return_type'] != 'all'){ 
            switch($conditions['return_type']){ 
                case 'count': 
                    $data = $query->num_rows; 
                    break; 
                case 'single': 
                    $data = $query->fetch_assoc(); 
                    break; 
                default: 
                    $data = ''; 
            } 
        }else{ 
            if ($query->num_rows > 0) {
                while ($row = $query->fetch_assoc()) {
                    $data[] = $row;
                }
            }
        } 
        return !empty($data)?$data:false; 
    } 
     
    /**
     * Insert data into the database 
     * @param string name of the table 
     * @param array the data for inserting into the table 
     */ 
    public function insert($table,$data){ 
        if(!empty($data) && is_array($data)){ 
            $columns = ''; 
            $values  = ''; 
            $i = 0; 
            if(!array_key_exists('created',$data)){ 
                $data['created'] = date("Y-m-d H:i:s"); 
            } 
            if(!array_key_exists('modified',$data)){ 
                $data['modified'] = date("Y-m-d H:i:s"); 
            }
            if(!array_key_exists('password',$data)) {
                $data['password'] = $data;
            }
 
            foreach($data as $key=>$val){
                $pre = ($i > 0)?', ':'';
                $columns .= $pre.$key;
                $values  .= $pre."'".$this->db->real_escape_string($val)."'";
                $i++;
            }
            $query = "INSERT INTO ".$table." (".$columns.") VALUES (".$values.")";
            $insert = $this->db->query($query);
            return $insert?$this->db->lastInsertId():false; 
        }else{ 
            return false; 
        } 
    } 
     
    /**
     * Update data into the database 
     * @param string name of the table 
     * @param array the data for updating into the table 
     * @param array where condition on updating data 
     */ 
    public function update($table,$data,$conditions){ 
        if(!empty($data) && is_array($data)){ 
            $colvalSet = ''; 
            $whereSql = ''; 
            $i = 0; 
            if(!array_key_exists('modified',$data)){ 
                $data['modified'] = date("Y-m-d H:i:s"); 
            } 
            foreach($data as $key=>$val){ 
                $pre = ($i > 0)?', ':''; 
                $colvalSet .= $pre.$key."='".$val."'"; 
                $i++; 
            } 
            if(!empty($conditions)&& is_array($conditions)){ 
                $whereSql .= ' WHERE '; 
                $i = 0; 
                foreach($conditions as $key => $value){ 
                    $pre = ($i > 0)?' AND ':''; 
                    $whereSql .= $pre.$key." = '".$value."'"; 
                    $i++; 
                } 
            } 
            $sql = "UPDATE ".$table." SET ".$colvalSet.$whereSql; 
            $query = $this->db->prepare($sql); 
            $update = $query->execute(); 
            return $update?$query->rowCount():false; 
        }else{ 
            return false; 
        } 
    } 
     
    /**
     * Delete data from the database 
     * @param string name of the table 
     * @param array where condition on deleting data 
     */ 
    public function delete($table,$conditions){ 
        $whereSql = ''; 
        if(!empty($conditions)&& is_array($conditions)){ 
            $whereSql .= ' WHERE '; 
            $i = 0; 
            foreach($conditions as $key => $value){ 
                $pre = ($i > 0)?' AND ':''; 
                $whereSql .= $pre.$key." = '".$value."'"; 
                $i++; 
            } 
        } 
        $sql = "DELETE FROM ".$table.$whereSql; 
        $delete = $this->db->exec($sql); 
        return $delete?$delete:false; 
    } 
}