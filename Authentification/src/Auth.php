<?php
namespace App;

use PDO;

class Auth{

    private $pdo;
    private $loginPath;

    public function __construct(PDO $pdo, string $loginPath)
    {
        $this->pdo=$pdo;
        $this->loginPath=$loginPath;
    }

    public function user(): ?User
    {
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        } 
        $id = $_SESSION['auth'] ?? null;
        if($id === null){
            return null;
        }
        $query = $this->pdo->prepare('SELECT * FROM users WHERE id= ?');
        $query->execute([$id]);
        $query->setFetchMode(PDO::FETCH_CLASS, User::class);
        $user = $query->fetch();
        return $user ?: null;
    }

    public function login(string $username, string $password): ?User
    {
        //trouve l'utilisateur correspondant à l'username
        $query = $this->pdo->prepare('SELECT * FROM users WHERE username = :username');
        $query->execute(['username'=>$username]);
        $query->setFetchMode(PDO::FETCH_CLASS, User::class);
        $user = $query->fetch();

        if($user === false){
            return null;
        }
        //vérifie password_verify que l'utilisateur correspond
        if(password_verify($password, $user->password)){
            if(session_status() === PHP_SESSION_NONE){
                session_start();
            } 
            $_SESSION['auth'] = $user->id;
            return $user;
        }
        return null;
    }

    //les ... permet d'avoir un tableau de taille 1 afin d'autoriser pls rôle a voir l'accès à une page.
    public function requireRole(string ...$roles): void
    {
        //dd($roles);
        $user = $this->user();
        if($user === null || !in_array($user->role, $roles)){
            header("Location: {$this->loginPath}?forbid=1");
            exit();
        }
    }
}
?>