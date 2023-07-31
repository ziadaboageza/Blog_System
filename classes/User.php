<?php


class User {

    private $PdoConnectoin ;

    public function __construct(PDO $pdo)
    {
        $this->PdoConnectoin = $pdo ;
    }

    public function register ($username , $email , $password)
    {   
        $errors= array();
        if(empty($username))
        {
            $errors[] = "Please Enter a username.";
        }
        if(empty($email))
        {
            $errors[] = "Please Enter an Email.";
        }
        if(empty($password))
        {
            $errors[] = "Please Enter a Password.";
        }

        //Check if the username or email address is already in use.
        $SqlQuary = $this->PdoConnectoin->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
        $SqlQuary->execute(array(':username' => $username , ':email' => $email));
        $count = $SqlQuary->fetchColumn();

        if ($count  > 0 )
        {
            $errors [] = "username or email address already in use.";
        }
        if (empty($errors))
        {
            $hash = password_hash($password , PASSWORD_DEFAULT);
            $SqlQuary = $this->PdoConnectoin->prepare ( "INSERT INTO users (username , email ,password) VALUES (:username , :email , :password)");
            $SqlQuary->execute(array(':username' => $username , ':email' => $email , ':password' => $hash));

            return true;
        }else {
            return $errors; 
        }

    } 

    
    public function login ( $email , $password)
    {   
       
        $SqlQuary = $this->PdoConnectoin->prepare("SELECT * FROM users WHERE email = :email");
        $SqlQuary->execute(array( ':email' => $email));

        $userInformation = $SqlQuary->fetch(PDO::FETCH_ASSOC);

        if ($userInformation && password_verify($password , $userInformation['password'])) {
            $_SESSION['user_id'] = $userInformation['id'];
            $_SESSION['username'] = $userInformation['username'];
            header('Location: ../index.php');
            exit;
        } else {
            return "Email address or password is not Correct !"; 
        }

    } 
}

?>
