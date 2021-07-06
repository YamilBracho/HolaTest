<?php 
    session_start();

    if (isset($_GET['action']) && $_GET['action'] == 'logout')
     {
        unset($_SESSION['currentUser']);
        header("location: login.php");
        exit();
    }

    define('SITE_ROOT', __DIR__);
    require_once SITE_ROOT . "./user/Roles.php";
    require_once SITE_ROOT . "./user/User.php";

    $strObj = $_SESSION['currentUser'];
    $currentUser = User::fromJson($strObj);
    if (!$currentUser)
    {
        header('HTTP/1.1 302 Found');
        header("location: login.php");
    }

    // Chequea si el usuario esta autorizado para esta pagina
    // En este caso solo  con los roles ROLE_ADMIN y ROLE_PAGE_1
    if ($currentUser->role != ROLE_ADMIN && $currentUser->role != ROLE_PAGE_2) {
        header('HTTP/1.1 403 Found');
        header("location: login.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
 <p>Hello 2 <?php echo $currentUser->name ?></p>
 <a href="page2.php?action=logout">Logout</a>

    
</body>
</html>