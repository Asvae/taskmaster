<?php
namespace asva\tasker;

class User
{
    function __construct()
    {
        $this -> conn         = Database::connect();
        $this -> userID       = 2;
        $this -> login        = "guest";
        $this -> password     = "";
        $this -> table_name   = "xfE5_tasks_".$this->login; // Tasks table
        $this -> options_name = "xfE5_options_".$this->login; // Options etc.
        $this -> current_name = "xfE5_current_".$this->login; // Dailty task statistics
    }



    // Validation function
    function validate ($POST)
    {
        foreach ($POST as $key => &$value)
        {
            switch ($key)
            {
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
        $conn = $this -> conn;

        $POST = $this -> validate ($POST);
        // {"func":"add","task":"","сategory":"Option 0","info":"","priority":"","repeatable":"not"}
        // query

        $sql = "INSERT INTO `task` (`userID`, `taskName`, `taskInfo`, `categoryID`, `priority`, `repeatable`) VALUES (:userID,:taskName,:taskInfo,:categoryID,:priority,:repeatable)";
        $q = $conn->prepare($sql);
        $q->execute(array(':userID'  => $this -> userID,
                        ':taskName'  => $POST -> taskName,
                        ':taskInfo'  => $POST -> taskInfo,
                        ':categoryID'=> $POST -> categoryID,
                        ':priority'  => $POST -> priority,
                        ':repeatable'=> $POST -> repeatable));

        echo (print_r($POST));
        echo ($this -> getTaskForm());
    }

    // Get partial priorities
    function getPartialPriorities ()
    {
        // Fetch priorities from DB
        $conn = Database::connect();
        $sql = "SELECT id, priority FROM ". $this -> table_name;
        $stmt = $conn->query($sql);
        $priorities = $stmt->fetchAll(\PDO::FETCH_NUM);

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

    // Get partial priorities
    function categoryList ()
    {
        $padding = [array('<option value = "','">','</option>'),
        array('<label for="taskCategory">Category</label><p id="taskCategory"><select name = "categoryID"">','','</select></p>')];

        $categories = Database::getItems("categoryID, categoryName",
            "category", $this -> userID)["body"];
        echo $this -> userID;
        return (insertPadding ($categories, $padding));
    }

    // Task addition html shortcut
    function getTaskForm()
    {
        $taskName = '<label for="taskName">Name of task</label><p id="taskName"><input name = "taskName" placeholder="Build a fort out of..."></p>';
        $taskInfo = '<label for="taskInfo">Further explanation if required</label><p id="taskInfo"><textarea name = "taskInfo" placeholder="Reconsider using cushions." ></textarea></p>';
        $category = $this -> categoryList ();
        $priority = '<label for="priority">Priority</label><p id="priority"><input name = "priority" placeholder="1-100"></p>';
        $repeatable = '<label for="taskCategory">Repeatable every:</label><p id = "radioRepeatable"><input type="radio" name="repeatable" value="week">week<input type="radio" name="repeatable" value="day">day<input type="radio" name="repeatable" value="hour">hour<input type="radio" name="repeatable" value="not" checked>not repeatable</p>';
        $submit = '<script src="/javascripts/application.js" type="text/javascript" charset="utf-8" async defer>html_button("Submit","#addTask", function (){ $("code").html(ajax_call("func=add"))});</script>';
         // Add row to table

        $full = '<form id="addTask">'. $taskName . $taskInfo .
        $category . $priority . $repeatable . $submit . '</form>';

        return ($full);
    }
}