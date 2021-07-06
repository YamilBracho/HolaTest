<?php

// --------------------------------------------------------------------
// testdb - Test de la funcionabilidad de acecso a datos para user
//
// Yamil Bracho
// yamilbracho@hotmail.com
// --------------------------------------------------------------------


require_once "../Utility/Database.php";
require_once "Roles.php";
require_once "User.php";
require_once "UserRepository.php";

$db = new Database();
$ur = new UserRepository($db->getConnection());

testFindAll($ur);
echo "---------------<br>";
testInsert($ur);
echo "---------------<br>";
testUpdate($ur);
echo "---------------<br>";
TestDelete($ur, "YamilBracho");

// --------------------------------------------------------------------
// test findAll
// --------------------------------------------------------------------
function testFindAll($ur) 
{
    try 
    {
        echo "Test findAll...<br>";
        $users = $ur->findAll();
        foreach ($users as $user) 
        {
            print_r($user);
            echo "<br>";
        } 
    } 
    catch (PDOException $ex) 
    {
        echo "exit catch" . $ex->getMessage();
    }
}

// --------------------------------------------------------------------
// Test Insert
// --------------------------------------------------------------------
function testInsert($ur) 
{
    // Adds new User
    try 
    {
        echo "Test Insert...<br>";
        $newUser = new User();
        $newUser->withName("Yamil")
                ->withUsername("YamilBracho")
                ->withPassword("123456")
                ->withRole(ROLE_PAGE_1);
        $newUser = $ur->insert($newUser);
        echo "{$newUser} - Inserted<br>";
    } 
    catch (PDOException $ex) 
    {
        echo $ex->getMessage();
        echo "<br>";
    }

    // Try to add again
    echo "Test Insert an existing user...<br>";
    try 
    {
        $newUser = $ur->findByUserName("YamilBracho");
        if ($newUser == FALSE) 
        {
            echo "{$newUser} not found...";
            return;
        }
        $newUser = $ur->insert($newUser);
        echo "{$newUser} - Inserted<br>";
    } 
    catch (PDOException $ex) 
    {
        echo "{$ex->getMessage()}<br>";
    }
}

// --------------------------------------------------------------------
// Test Update
// --------------------------------------------------------------------
function TestUpdate($ur)
 {
        // Updates a exiting user
        try 
        {
            echo "Test Update...<br>";
            $user = $ur->findByUserName("YamilBracho");
            if ($user == FALSE) 
            {
                echo "{$user} not found...";
            } 
            else 
            {
                $user->password = "987654321";
                $user->role = ROLE_PAGE_2;
            
                echo ($ur->update($user)) ?  "{$user} updated..." : "{$user} not updated<br>";
            }
        } 
        catch (PDOException $ex) 
        {
            echo "{$ex->getMessage()}<br>";
        }
       
        // updates a non exsiting user
        try 
        {
            echo "Try to update a non existing user...<br>";
            $user = new User();
            $user->withId(100);
            
            echo ($ur->update($user))  ? "{$user} updated..." : "{$user} not updated<br>";
        } 
        catch (PDOException $ex) 
        {
            echo "{$ex->getMessage()}<br>";
        }
    
        // Try to update admin
        try 
        {
            echo "Try to update admin...<br>";
            $user = $ur->findById(1);
            $user->withRole(ROLE_PAGE_1);
        
            echo $ur->update($user) ? "{$user} updated..." : "{$user} not updated<br>";
        } 
        catch (PDOException $ex) 
        {
            echo "{$ex->getMessage()}<br>";
        }
}

// --------------------------------------------------------------------
// Test Delete
// --------------------------------------------------------------------
function TestDelete($ur, $username) 
{
    echo "Testing Delete {$username}...<br>";

    try 
    {
      // Busca registro
      
      $user = $ur->findByUserName($username);
      if ($user == FALSE) 
      {
        echo "{$username} not found<br>";
      } 
      else 
      {
          echo $ur->deleteById($user->id) ? "{user} deleted.<br>" : "unable to delete {user}.<br>";
      }
    }  
     catch (PDOException $ex) 
     {
        echo $ex->getMessage();
        echo "<br>";
    }
}
