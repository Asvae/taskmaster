<?php
namespace asva\tasker;

include "functions.php";
include "../autoload.php";

if (isset($_POST))
  $POST = json_decode($_POST['myjson']);

if (isset($POST) && isset($POST->func))
{
  $user = new User();
  switch ($POST->func) {
    case 'add':
        $user -> addTask($POST);
        break;
    case 'addForm':
        echo ($user -> getTaskForm());
        break;
    case 'update':
        echo (array2Table(Database::getItems("taskName, taskInfo, categoryName, priority","task", $user -> userID, "category")));
        break;
    case 'prior':
        $user -> getPartialPriorities();
        break;
    case 'options':
        echo (array2Table(Database::getItems("*","option")));
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
