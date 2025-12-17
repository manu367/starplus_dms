<?php
// used to get mysql database connection
class DatabaseService{

  private $db_host = "localhost";
    private $db_name = "cansale_dms";
    private $db_user = "cspl_demo_dms";
    private $db_password = "V351g?s6w";
    private $connection;

    public function getConnection(){

        $this->connection = null;
		$this->connection = new mysqli($this->db_host,$this->db_user,$this->db_password,$this->db_name);
        if ($this->connection->connect_error) {
		  die("Connection failed: " . $this->connection->connect_error);
		}    
        return $this->connection;
    }
}
?>