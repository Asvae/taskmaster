<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]> <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]> <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Login Form</title>
  <link rel="stylesheet" href="src/stylesheets/login_form.css">
  <!--[if lt IE 9]><script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->


<?php


//Process Login
if(count($_POST)){
  include_once 'vendor\autoload.php' ;

  $user = new ptejada\uFlex\myUser();
  
  $user->start();

  /*
   * Covert POST into a Collection object
   * for better value handling
   */
  $input = new ptejada\uFlex\Collection($_POST);

  $registered = $user->register(array(
    'Username'  => $input->Username,
    'Password'  => $input->passsword,
    'Password2' => $input->passsword2,
    'email'     => $input->email,
    'groupID'   => $input->groupID,
  ),false);
  print_r($user);

  if($registered){
    echo "User Registered";
  }else{
    //Display Errors
    foreach($user->log->getErrors() as $err){
        echo "<b>Error:</b> {$err} <br/ >";
    }
  }
}



?>

</head>
<body>
  <section class="container">
    <div class="login">
      <h1>Login, task will wait</h1>
      <form method="post">
        <label>Username:</label>
        <input type="text" name="username" />

        <label>Password:</label>
        <input type="Password" name="password" />

        <label>Re-enter Password:</label>
        <input type="Password" name="password2" />

        <label>Email: </label>
        <input type="text" name="email" />

        <label>Group: </label>
        <select name="groupID">
          <option value="1">User</option>
          <option value="2">Developer</option>
          <option value="3">Designer</option>
        </select>

        <input type="submit" value="Register" />
      </form>
    </div>
  </section>
</body>
</html>

