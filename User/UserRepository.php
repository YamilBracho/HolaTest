<?php

// --------------------------------------------------------------------
// UserRepository - Clase que maneja la persitrencia de los datos 
// de un usuario
//
// Yamil Bracho
// yamilbracho@hotmail.com
// --------------------------------------------------------------------

class UserRepository 
{
    private $db;

    // ----------------------------------------------------------------
    // Constructor
    // ----------------------------------------------------------------
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // ----------------------------------------------------------------
    // REtorna todos los usuario
    // ----------------------------------------------------------------
    public function findAll() 
    {
        try 
        {
            $sql ='SELECT id, name, username, password, role FROM user';
            $stmt = $this->conn->prepare( $sql );
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_CLASS, 'User');

            return $rows;
        } 
        catch (PDOException $ex) 
        {
            throw $ex;
        }
    }
    
    // ----------------------------------------------------------------
    // Busca usuario usando username y password
    // Si no lo consigue, retorna FALSE
    // ----------------------------------------------------------------
    public function findByUserAndPassword($username, $password) 
    {
        $sql ="SELECT id, name, username, password, role 
               FROM user 
               WHERE username=? AND password=? 
               LIMIT 0,1";

        $stmt = $this->conn->prepare( $sql );
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'User');
        $stmt->execute(array($username, $password));

        $row = $stmt->fetch();
        return $row;
    }

    // ----------------------------------------------------------------
    // Busca usuario usando el user.
    // Si no lo consigue, retorna FALSE
    // ----------------------------------------------------------------
    public function findByUserName($username) 
    {
        $sql ="SELECT id, name, username, password 
               FROM user 
               WHERE username=?
               LIMIT 0,1";

        $stmt = $this->conn->prepare( $sql );
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'User');
        $stmt->execute(array($username));
        $row = $stmt->fetch();

        return $row;
    }

    // ----------------------------------------------------------------
    // Busca usuario por su id.
    // Si no lo consigue, retorna FALSE
    // ----------------------------------------------------------------
    public function findById($id) 
    {
        $sql ="SELECT id, name, username, password, role 
               FROM user 
               WHERE id=?  
               LIMIT 0,1";

        $stmt = $this->conn->prepare( $sql );
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'User');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
       return $row;
    }

    // ----------------------------------------------------------------
    // Agrega usuario a la tabla de user.
    // Arroja excepcion si ya existe o cualquier otro error en la BD
    // ----------------------------------------------------------------
    public function insert($newUser) 
    {
        $foundUser = $this->findByUserName($newUser->username);
        if (!$foundUser) 
        {
            try 
            {
                $newUser->name = htmlspecialchars(strip_tags( $newUser->name));
                $newUser->username = htmlspecialchars(strip_tags( $newUser->username));
                $newUser->password = htmlspecialchars(strip_tags( $newUser->password));
                $newUser->role = htmlspecialchars(strip_tags( $newUser->role));

                $sql = "INSERT INTO user(name, username, password, role) VALUES(?, ?, ?, ?)";
                $stmt = $this->conn->prepare( $sql );
                $stmt->execute( array( $newUser->name, $newUser->username,  $newUser->password, $newUser->role));
                $newUser->id = $this->conn->lastInsertId();
                
                return $newUser;

            } 
            catch (PDOException $ex) 
            {
                throw $ex;
            }
        } 
        else 
        {
            throw new PDOException("{$newUser->username} already exists.");
        }
    }

    // ----------------------------------------------------------------
    // Actualiza datos de un usuario existente.
    // Arroja excepcion si no existe o cualquier otro error en la BD
    // Retorna true si se actualizo el dato, false E.C.C
    // ----------------------------------------------------------------
    public function update($existingUser) 
    {
        try 
        {
            $foundUser = $this->findById($existingUser->id);
            if ($foundUser == FALSE) 
            {
                throw new PDOException("{$existingUser->username} not found.");
            } 
            else 
            {
                $sql ="UPDATE user SET ";

                // Solo se actualiza el password del admin
                if ($existingUser->isAdmin()) 
                {
                    $data = array($existingUser->password);
                    $sql .= "password = ?";
                } 
                else 
                {
                    $data = array($existingUser->name, $existingUser->username, $existingUser->password, $existingUser->role);
                    $sql .= "name = ?, username = ?, password = ?, role = ?";
                }    
                $sql .= " WHERE id = ?";
                $data [] = $existingUser->id;

                $stmt = $this->conn->prepare( $sql );
                $stmt->execute($data);
                $updated = $stmt->rowCount();
                return $updated != 0;
            }
        }  
        catch (PDOException $ex) 
        {
            throw $ex;
        }
    }

    // ----------------------------------------------------------------
    // Elimina un usuario existente
    // Arroja excepcion si no existe o cualquier otro error en la BD
    // Retorna true si se elimino el dato, false E.C.C
    // ----------------------------------------------------------------
    public function deleteById($id) 
    {
        $foundUser = $this->findById($id);
        if ($foundUser == FALSE) 
        {
            throw new PDOException("{$id} not found.");
        } 
        else 
        {
            try 
            {
                // Si el usuario es admin, no lo puede eliminar
                if ($foundUser->isAdmin()) 
                {
                    throw new PDOException("admin can not be deleted");
                } 
                else
                 {
                    $sql ="DELETE FROM user WHERE id=?";
                    $stmt = $this->conn->prepare( $sql );
                    $stmt->execute([$id]);
                    $deleted = $stmt->rowCount();
                    return $deleted != 0;
                }
            }  
            catch (PDOException $ex) 
            {
                throw $ex;
            }
        }
   }
}