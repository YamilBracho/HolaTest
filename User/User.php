<?php

// --------------------------------------------------------------------
// UserEntity - Clase que contiene los datos de un usuario
//
// Yamil Bracho
// yamilbracho@hotmail.com
// --------------------------------------------------------------------

class User
{
    public $id;
    public $name;
    public $username;
    public $password;
    public $role;

    // ----------------------------------------------------------------
    // presentacion string de la clase - json
    // ----------------------------------------------------------------
    public function __toString() 
    {
        return json_encode($this);
    }


    // ----------------------------------------------------------------
    // Retorna true si el current user es admin
    // ----------------------------------------------------------------
    public function isAdmin() 
    {
        return ($this->username == "admin");
    }

    // ----------------------------------------------------------------
    // Inicializa Id de la instancia
    // ----------------------------------------------------------------
    public function withId($id) 
    {
       $this->id = $id;
       return $this;
    }

    // ----------------------------------------------------------------
    // Inicializa name de la instancia
    // ----------------------------------------------------------------
    public function withName($name) 
    {
        $this->name = $name;
        return $this;
    }

    // ----------------------------------------------------------------
    // Inicializa UserName de la instancia
    // ----------------------------------------------------------------
    public function withUsername($username) 
    {
        $this->username = $username;
        return $this;
    }
    
    // ----------------------------------------------------------------
    // Inicializa Password de la instancia
    // ----------------------------------------------------------------
    public function withPassword($password) 
    {
        $this->password = $password;
        return $this;
    }
    
    // ----------------------------------------------------------------
    // Inicializa Rol de la instancia
    // ----------------------------------------------------------------
    public function withRole($role) 
    {
        $this->role = $role;
        return $this;
    }

    public static function createFromArray($input, $id=0) {
        $result = new User();
        $result->withRole($input['role'])
                ->withPassword($input['password'])
                ->withUsername($input['username'])
                ->withName($input['name']);
        if ($id > 0) 
        {
            $result->withId($id);
        } 

        return $result;
    }

    public static function fromJson($json) 
    {
        $stdObj = json_decode($json);
        $arr =(array) $stdObj;

        $result = new User();
        $result->withRole($arr['role'])
                ->withPassword($arr['password'])
                ->withUsername($arr['username'])
                ->withName($arr['name'])
                ->withId($arr["id"]);

        return $result;

    }


}



