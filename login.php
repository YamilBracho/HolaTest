<?php
session_start(); 

define('SITE_ROOT', __DIR__);

require_once SITE_ROOT . "/utility/Database.php";
require_once SITE_ROOT . "./user/Roles.php";
require_once SITE_ROOT . "./user/User.php";
require_once SITE_ROOT . "./user/UserRepository.php";

$error=''; 
if (isset($_POST['submit'])) 
{
    if (empty($_POST['username']) || empty($_POST['password'])) 
    {
        $error = "Username o Password invalidos";
    }
    else
    {
        // Define $username and $password
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $db = new Database();
        $ur = new UserRepository($db->getConnection()); 
        $user = $ur->findByUserAndPassword($username, $password);
        if ($user == FALSE) 
        {
            $error = "Usuario no existe.";
        }
        else
        {
            $_SESSION['currentUser'] = strval($user);
            if ($user->role == ROLE_PAGE_1 || $user->role == ROLE_ADMIN) 
            {
                header("Location: http://localhost/holatest/page1.php", true, 301);
                exit();
            }
            
            if ($user->role == ROLE_PAGE_2) 
            {
                header("Location: http://localhost/holatest/page2.php", true, 301);
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Login Form</title>
    </head>
    <body>
        <div id="main">
            <h1>Login</h1>
            <div id="login">
                <form action="" method="post">
                    <label>UserName :</label>
                    <input id="name" name="username" placeholder="username" type="text">
                    <label>Password :</label>
                    <input id="password" name="password" placeholder="**********" type="password">
                    <input name="submit" type="submit" value=" Login ">
                    <span style="color: red;"><?php echo $error; ?></span>
                </form>
                </div>
            </div>
    </body>
</html>