<?php
class storage {

	protected $connection = array(
		'host' => '',
		'user' => '',
		'pass' => '',
		'dbname' => ''
	);

	public function do_insert($table, $data, $preformatted = false){
		// Create connection
		$conn = new mysqli($this->connection['host'], $this->connection['user'], $this->connection['pass'], $this->connection['dbname']);
		// Check connection
		$success = false;
		$bug = '';
		$sql = 'Conenction Error: SQL Statement can\'t be formed';

		if ($conn->connect_error) {
			$bug = 'Connection Error: '.$conn->connect_error;
		}
		else{
			$indices = array();
			$values = array();

			foreach($data as $index => $val) {
				array_push($indices, '`'.$index.'`');
				if(!$preformatted) {
					array_push($values, '\''.mysqli_real_escape_string($conn, $val).'\'');
				}
				else{
					if ($val['type'] == 'numeric') {
						array_push($values, $val['data']);
					}
					else {
						array_push($values, '\''.mysqli_real_escape_string($conn, $val['data']).'\'');
					}
				}
			}

			$sql = "INSERT INTO ".$table." (".implode(', ', $indices).")
				VALUES (".implode(', ', $values).")";
			
			if ($conn->query($sql) === TRUE) {
				$success = true;
			} else {
				$bug = "Error: ".$conn->error;
			}

			$conn->close();
		}

		return array(
			'status' => $success,
			'debug' => $bug,
			'statement' => $sql
		);
	}

	public function do_remove($table, $field, $target) {
		$conn = new mysqli($this->connection['host'], $this->connection['user'], $this->connection['pass'], $this->connection['dbname']);
		$success = false;
		$bug = '';
		$sql = 'Conenction Error: SQL Statement can\'t be formed';

		if ($conn->connect_error) {
			$bug = 'Connection Error: '.$conn->connect_error;
		}
		else{
			$sql = "DELETE FROM `".$table."` WHERE ".$field." LIKE '".$target."';";

			if($conn->query($sql) === TRUE) {
				$success = true;
				$bug = '';
			}
			else{
				$bug = "Error: ".$conn->error;
			}

			$conn->close();
		}

		return array(
			'status' => $success,
			'debug' => $bug,
			'statement' => $sql
		);
	}

	public function do_get($table, $fields = '*', $where = '', $specifics = false) {
		if($where != '') {
			$where = " WHERE $where";
		}

		$conn = new mysqli($this->connection['host'], $this->connection['user'], $this->connection['pass'], $this->connection['dbname']);
		
		if(!$specifics){
			$sql = "SELECT ".$fields." FROM `".$table."`".$where;
		}
		else if($specifics == 'distinct'){
			$sql = "SELECT DISTINCT ".$fields." FROM `".$table."`".$where;
		}
		else if($specifics == 'count'){
			$sql = "SELECT COUNT(".$fields.") FROM `".$table."`".$where;
		}

		$success = false;
		$bug = '';

		$data = array();

		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
		// output data of each row
			$success = true;
			while($row = $result->fetch_assoc()) {
				array_push($data, $row);
			}
		}
		else
		{
			$success = false;
			$bug = 'Query yielded zero results.';	
		}

		return array(
			'status' => $success,
			'debug' => $bug,
			'statement' => $sql,
			'results' => $data
		);
	}

	public function do_write($table, $changes, $where, $preformatted = false){
		$change_statement = array();
		$conn = new mysqli($this->connection['host'], $this->connection['user'], $this->connection['pass'], $this->connection['dbname']);
		foreach($changes as $ch => $ge) {
			if(!$preformatted) {
				array_push($change_statement, $ch.'='.'\''.mysqli_real_escape_string($conn, $ge).'\'');
			}
			else{
				if($ge['type'] == 'numeric') {
					array_push($change_statement, $ch.'='.$ge['data']);
				}
				else{
					array_push($change_statement, $ch.'='.'\''.mysqli_real_escape_string($conn, $ge['data']).'\'');
				}
			}
		}

		$sql = "UPDATE ".$table."
		   SET ".implode(', ', $change_statement)."
		WHERE ".$where;

		$success = false;
		$bug = '';

		if ($conn->query($sql) === TRUE) {
			$success = true;
		} else {
			$bug = "Error: ".$conn->error;
		}

		$conn->close();

		return array(
			'status' => $success,
			'debug' => $bug,
			'statement' => $sql
		);
	}
	
	public function __construct($user = false, $pass = false, $host = false, $dbname = false) {
		if(!$user){
			$user = DB_USER;
		}
		if(!$pass) {
			$pass = DB_PASSWORD;
		}
		if(!$host) {
			$host = DB_HOST;
		}
		if(!$dbname) {
			$dbname = DB_NAME;
		}

		$this->connection['host'] = $host;
		$this->connection['user'] = $user;
		$this->connection['pass'] = $pass;
		$this->connection['dbname'] = $dbname;
	}

	protected function return_constants() {
		return $this->connection;
	}
}
?>