<?php
include "functions.php";

class user
{
	function __construct()
	{	
		$this->login = "guest";
		$this->password = "";
		$this->table_name = "xfE5_tasks_".$this->login; // Tasks table
		$this->options_name = "xfE5_options_".$this->login; // Options etc.
		$this->current_name = "xfE5_current_".$this->login; // Dailty task statistics
	}

	// Establish connection.
	function connect ()
	{
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "asva_db";

		// Check connection
		try{
			$conn = new PDO( "mysql:host=$servername;dbname=$dbname", $username, $password);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		catch(exception $e){
			die(print_r($e));
		}
		return $conn;
	}

	// Create table. Assume database already exists.
	function createTable ()
	{
		$conn = $this -> connect();
		
		$sql = "CREATE TABLE IF NOT EXISTS ".$this -> table_name."(
			id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			task VARCHAR,
			category VARCHAR,
			info VARCHAR DEFAULT NULL,
			priority TINYINT(100) UNSIGNED,
			repeatable TINYINT(100) UNSIGNED,
			is_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			is_completed BOOL DEFAULT FALSE)";

		try{
			$conn->query($sql);
		}
		catch(exception $e){
			print_r($e);
		}

		echo ("Table '".$this -> table_name."' has been created.");
	}

	// Drop table. Assumes table already exists
	function dropTable ()
	{
		$conn = $this -> connect();
		$sql = "DROP TABLE IF EXISTS ".$this -> table_name;
		try{
			$conn->query($sql);
		}
		catch(exception $e){
			print_r($e);
		}
		
	}

	// Validation function
	function validate ($POST)
	{
		foreach ($POST as $key => &$value)
		{
			switch ($key) {
				case 'priority':
					if (!$value)
						$value = 10;
					break;
				
				default:
					# code...
					break;
			}
		}
		return $POST;
	}

	// Adds a new entry.
	function addTask ($POST)
	{
		$POST = $this -> validate ($POST);
		// {"func":"add","task":"","сategory":"Option 0","info":"","priority":"","repeatable":"not"}


		$conn = $this -> connect();
		$sql = "INSERT INTO ".$this -> table_name." (task, category, info, priority, repeatable) VALUES (?, ?, ?, ?, ?)";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(1, $POST->task       );
		$stmt->bindValue(2, $POST->category   );
		$stmt->bindValue(3, $POST->info       );
		$stmt->bindValue(4, $POST->priority   );
		$stmt->bindValue(5, $POST->repeatable );
		$stmt->execute();

		echo (print_r($POST));
	}

	// Get table content.
	function getTable ()
	{

		$padding = array(
		array('<tr id=','><td>','</td><td>','</td></tr>'), array('',''),
		array(
		'<table id = "taskTable">
			<thead>
				<th>Name</th><th>Category</th><th>Priority</th><th>Repeatable</th>
			</thead>
			<tbody>',
			'</tbody>
		</table>'
		));

		echo ( insertPadding( $this->getAllItems(), $padding ));
	}	

	// Get MySQL table content into array
	function getAllItems ()
	{
		$conn = $this -> connect();
		$sql = "SELECT id, task, category, priority, repeatable FROM ". $this -> table_name;
		$stmt = $conn->query($sql);
		return $stmt->fetchAll(PDO::FETCH_NUM);
	}

	// Get partial priorities
	function getPartialPriorities ()
	{
		// Fetch priorities from DB
		$conn = $this -> connect();
		$sql = "SELECT id, priority FROM ". $this -> table_name;
		$stmt = $conn->query($sql);
		$priorities = $stmt->fetchAll(PDO::FETCH_NUM);

		// Convert into hash array
		$priorities_tmp = [];
		foreach ($priorities as $priority)
		{
			$priorities_tmp [$priority[0]] = $priority[1];
		};
		$priorities = $priorities_tmp;

		// Main computations
		$priority_tmp = 0;
		$rand = rand (1, array_sum($priorities));
		
		foreach ($priorities as $id => $priority)
		{
			$priority_tmp += $priority;
			if ($rand <= $priority_tmp)
			{
				$lucky_id = $id; break;
			}

		}

		echo $rand." / ".$lucky_id;

	}	

}


	

if (isset($_POST))
	$POST = json_decode($_POST['myjson']);

if (isset($POST) && isset($POST->func))
{
	$user = new user();
	switch ($POST->func) {
		case 'create':
			$user -> createTable();
			break;
		case 'drop':
			$user -> dropTable();
			break;
		case 'add':
			$user -> addTask($POST);
			break;
		case 'update':
			$user -> getTable();
			break;
		case 'prior':
			$user -> getPartialPriorities();
			break;
		default:
			# code...
			break;
	}
}

//echo ("<br>Here's your POST, master: <br>");
//print_r($_POST);


