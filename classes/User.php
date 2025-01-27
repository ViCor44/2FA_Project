<?php
class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user; // Login successful
        }
        return false; // Login failed
    }

    public function save2FASecret($userId, $secretKey) {
        $query = "UPDATE " . $this->table . " SET google_2fa_secret = :secret, two_factor_enabled = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':secret', $secretKey);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    public function register($username, $email, $password, $security_question, $security_answer, $enable_2fa, $google2faSecret) {
        $query = "INSERT INTO " . $this->table . " (username, email, password, security_question, security_answer, google_2fa_secret, two_factor_pending) 
                  VALUES (:username, :email, :password, :security_question, :security_answer, :google2faSecret, :two_factor_pending)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':security_question', $security_question);
        $stmt->bindParam(':security_answer', $security_answer);
        $stmt->bindParam(':google2faSecret', $google2faSecret);
        $stmt->bindParam(':two_factor_pending', $enable_2fa);
    
        return $stmt->execute();
    }

    // Método para buscar o utilizador pelo ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isTwoFactorPending($user_id) {
            $query = "SELECT two_factor_pending FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

    public function getByUsername($username) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateGoogle2FASecret($id, $secret) {
        $query = "UPDATE " . $this->table . " SET google_2fa_secret = :secret WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':secret', $secret);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updatePassword($email, $password) {
        $query = "UPDATE " . $this->table . " SET password = :password WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    public function updateProfile($user_id, $username, $email, $hashed_password = null, $enable_2fa) {
        $query = "UPDATE users SET 
                    username = :username, 
                    email = :email, 
                    two_factor_pending = :two_factor_pending, 
                    two_factor_enabled = :two_factor_enabled";
    
        // Adicionar a alteração de senha apenas se o valor for fornecido
        if ($hashed_password) {
            $query .= ", password = :password";
        }
        $query .= " WHERE id = :id";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    
        // Calcular valores antes de vincular
        $two_factor_pending = $enable_2fa ? 1 : 0;
        $two_factor_enabled = $enable_2fa ? 1 : 0;
    
        // Vincular os valores de two_factor_pending e two_factor_enabled
        $stmt->bindParam(':two_factor_pending', $two_factor_pending, PDO::PARAM_INT);
        $stmt->bindParam(':two_factor_enabled', $two_factor_enabled, PDO::PARAM_INT);
    
        // Vincular a senha, se fornecida
        if ($hashed_password) {
            $stmt->bindParam(':password', $hashed_password);
        }
    
        // Executar a consulta e retornar o resultado
        return $stmt->execute();
    }
    
    

    
    public function enableTwoFactorAuthentication($id) {
        $query = "UPDATE " . $this->table . " 
                  SET two_factor_enabled = 1, two_factor_pending = 0 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
        if (!$stmt->execute()) {
            // Debug SQL Errors
            error_log("Erro SQL: " . implode(", ", $stmt->errorInfo()));
            return false;
        }
    
        return true;
    } 
    
}
?>
