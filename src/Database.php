<?php

namespace asva\tasker;

//
// Static class
class Database
{
	public static $sqls =
	[
		"user" => "CREATE TABLE `user` (
			`userID` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`login` varchar(255) NOT NULL,
			`email` varchar(255) NOT NULL,
			`password` varchar(51) NOT NULL,
			PRIMARY KEY (`userID`),
			UNIQUE KEY `name` (`name`,`email`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Users table'",

        "category" => "CREATE TABLE `category` (
            `categoryID` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `categoryName` varchar(255) NOT NULL,
            `userID` int(10) unsigned NOT NULL,
            PRIMARY KEY (`categoryID`),
            FOREIGN KEY (userID) REFERENCES user(userID)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Options table'",

		"option" => "CREATE TABLE `option` (
			`userID` int(10) unsigned NOT NULL,
			`isNumber` tinyint(1) NOT NULL DEFAULT '0',
			`optionKey` varchar(255) NOT NULL,
			`optionValue` varchar(255) NOT NULL,
            FOREIGN KEY (userID) REFERENCES user(userID)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Options table'",

        "task" => "CREATE TABLE `task` (
            `taskID` int(10) unsigned NOT NULL AUTO_INCREMENT,
            PRIMARY KEY (`taskID`),
            `userID` int(10) unsigned NOT NULL,
            FOREIGN KEY (userID) REFERENCES user(userID),
            `categoryID` int(10) unsigned NOT NULL,
            FOREIGN KEY (categoryID) REFERENCES category(categoryID),
            `taskName` varchar(255) NOT NULL,
            `taskInfo` varchar(10000) NOT NULL,
            `priority` int(100) unsigned DEFAULT '5',
            `tsCreated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `repeatable` tinyint(1) NOT NULL DEFAULT '0',
            `isCompleted` tinyint(1) NOT NULL DEFAULT '0',
            `tsCompleted` TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tasks table'",
	];

	// Config db access here
	public static $db_params =
	[
		"servername" => "localhost",
		"username"   => "root",
		"password"   => "",
		"dbname"     => "asva_db",
	];

	// Establish connection.
	public static function connect ()
	{
		$db_params  = self::$db_params;
		$servername = $db_params["servername"];
		$username   = $db_params["username"  ];
		$password   = $db_params["password"  ];
		$dbname     = $db_params["dbname"    ];

		// Check connection
		try{
			$conn = new \PDO( "mysql:host=$servername;dbname=$dbname", $username, $password);
			$conn->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
		}
		catch(exception $e){
			die(print_r($e));
		}
		return $conn;
	}

	// Create table. Assume database already exists.
	//
	public static function createTable ($type)
	{
		$sql = self::$sqls[$type];
		$conn = Database::connect();

		try{
			$conn->query($sql);
		}
		catch(exception $e){
			print_r($e);
		}

		echo ("Table `".$type."` has been created.");
	}

    // Drop table. Assumes table already exists
    public static function dropTable ($type)
    {
        $conn = Database::connect();

        $sql = "DROP TABLE IF EXISTS `". $type ."`;";
        echo ($sql);
        try{
            $conn->query($sql);
        }
        catch(exception $e){
            print_r($e);
        }
        echo ("Table `".$type."` has been deleted.");
    }

    // Get from specified table
    public static function getItems ($columns = "*", $table = "task",
        $userID = 1, $naturalJoin = "")
    {
        $conn = Database::connect();

        // if @columns set — use as header
        if ($columns !== "*"){
            $columns = explode (", ", $columns);
            $padding = array(array("`","`, `","`"));
        }
        else
        {   // Otherwise — get from db
            $columns_sql = "SELECT `COLUMN_NAME`
                FROM `INFORMATION_SCHEMA`.`COLUMNS`
                WHERE `TABLE_SCHEMA`='asva_db'
                AND TABLE_NAME='". $table ."';";

            $stmt = $conn->query($columns_sql);
            $columns = $stmt->fetchAll(\PDO::FETCH_NUM);
            $padding = array(array("`"),array("",", ",""));
        }
        // Insert ticks
        $columns = insertPadding ($columns, $padding);

        $header = str_replace ("`","",$columns);
        $header = explode (", ", $header);

        if ($naturalJoin)
            $naturalJoin = "NATURAL JOIN `$naturalJoin`";
        $table = "`$table`";

        $sql = "SELECT ". $columns ." FROM $table $naturalJoin WHERE userID='$userID'";
        echo ($sql . '<br>');
        $stmt = $conn->query($sql);
        $body = $stmt->fetchAll(\PDO::FETCH_NUM);
        //array_unshift ($body, $header);
        return ["body"=>$body, "header"=>$header];
    }
}
?>
