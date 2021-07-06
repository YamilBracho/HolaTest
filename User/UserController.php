<?php


// --------------------------------------------------------------------
// UserController - Clase que las solicitudes al API
//
// Yamil Bracho
// yamilbracho@hotmail.com
// --------------------------------------------------------------------
class UserController 
{
    private $userRepository;
    private $requestMethod;
    private $userId;


    // ----------------------------------------------------------------
    // Constructor
    // ----------------------------------------------------------------
    public function __construct($userRepository, $requestMethod, $userId)
    {
        $this->userRepository = $userRepository;
        $this->requestMethod = $requestMethod;
        $this->userId = $userId;
    }

    // ----------------------------------------------------------------
    // Procesamiento centralizado de peticiones 
    // ----------------------------------------------------------------
    public function processRequest()
    {
        switch ($this->requestMethod) 
        {
            case 'GET':
                if ($this->userId) 
                {
                    $response = $this->getUser($this->userId);
                } 
                else 
                {
                    $response = $this->getAllUsers();
                };
                break;

            case 'POST':
                $response = $this->createUserFromRequest();
                break;

            case 'PUT':
                $response = $this->updateUserFromRequest($this->userId);
                break;

            case 'DELETE':
                $response = $this->deleteUser($this->userId);
                break;

            default:
                $response = $this->buildErrorResponse('Unknown action');
                break;
        }

        header("HTTP/1.1 200 OK");
        echo json_encode($response);
    }

    // ----------------------------------------------------------------
    // Retorna todos los usuarios registrados 
    // ----------------------------------------------------------------
    private function getAllUsers()
    {
        try 
        {
            $result = $this->userRepository->findAll();
            return $this->buildOkResponse('Records', $result);
        } 
        catch (PDOException $ex) 
        {
            return $this->buildErrorResponse($ex->getMessage());
        } 
    }

    // ----------------------------------------------------------------
    // Retorna datos de un usuario en particular 
    // ----------------------------------------------------------------
    private function getUser($id)
    {
        $result = $this->userRepository->findById($id);
        return (!$result) ?  $this->buildErrorResponse('User Not Found')
                          : $this->buildOkResponse('Record', $result);
    }

    // ----------------------------------------------------------------
    // Crea nuevo usuario 
    // ----------------------------------------------------------------
    private function createUserFromRequest()
    {
        try
        {
            $input = (array) json_decode(file_get_contents('php://input'), TRUE);
            $message = $this->validateInput($input);
            if (strlen($message) > 0) 
            {
                return $this->buildErrorResponse($message);
            }

            $inputUser = User::createFromArray($input);
            $newUser = $this->userRepository->insert($inputUser);
            
            return $this->buildOkResponse('Record', $newUser);
        }
        catch (PDOException $ex) 
        {
           return $this->buildErrorResponse($ex->getMessage());
        }
    }

    // ----------------------------------------------------------------
    // Actualzia datos de un usuario 
    // ----------------------------------------------------------------
    private function updateUserFromRequest($id)
    {
        try 
        {
            $input = (array) json_decode(file_get_contents('php://input'), TRUE);
            $message = $this->validateInput($input);
            if (strlen($message) > 0) 
            {
                return $this->buildErrorResponse($message);
            }

            $existingUser = User::createFromArray($input, $id);
            if ($this->userRepository->update($existingUser))
            {
                return $this->buildOkResponse();
            }
            return $this->buildErrorResponse('{$existingUser} not updated.');
        }
        catch (PDOException $ex) 
        {
           return $this->buildErrorResponse($ex->getMessage());
        }
    }

    // ----------------------------------------------------------------
    // Elimina usuario 
    // ----------------------------------------------------------------
    private function deleteUser($id)
    {
        try {
            if ($this->userRepository->deleteById($id))
            {
                return $this->buildOkResponse();
            } 
            
            $errorMesasage =  ($id == null) ? "Falta especificar el id" : "{$id} not deleted.";
            return $this->buildErrorResponse($errorMesasage);
        }
        catch (PDOException $ex) 
        {
            $errorMesasage =  ($id == null) ? "Falta especificar el id" : $ex->getMessage();
            return $this->buildErrorResponse($errorMesasage);
        }
    }

    // ----------------------------------------------------------------
    // Construye respuesta de OK
    // ----------------------------------------------------------------
    private function buildOkResponse($key = "", $value="")
    {
        $result['Status'] = 'OK';
        if (strlen($key) > 0) 
        {
            $result[$key] = $value;
        }

        return $result;
    }

    // ----------------------------------------------------------------
    // Construye respuesta de error
    // ----------------------------------------------------------------
    private function buildErrorResponse($message) 
    {
        return array('Status' => 'ERROR', 'Message' => $message);
    }

    // ----------------------------------------------------------------
    // Realiza validacion de los datos del usuario 
    // ----------------------------------------------------------------
    private function validateInput($input)
    {
        if (! isset($input['name'])) 
        {
            return 'name missing';
        }

        if (! isset($input['username'])) 
        {
            return 'username missing';
        }

        if (! isset($input['password'])) 
        {
            return 'password missing';
        }

        if (! isset($input['role'])) 
        {
            return 'role missing';
        }

        return '';
    }
}
