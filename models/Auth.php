<?php
require_once __DIR__ . '/../config/config.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    /**
     * Login user
     */
    public function login($username, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && verifyPassword($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['class'] = $user['class'];
                
                return ['success' => true, 'role' => $user['role']];
            }
            
            return ['success' => false, 'message' => 'Username or password is incorrect'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        session_destroy();
        return true;
    }
    
    /**
     * Check if user is logged in
     */
    public function check() {
        return isLoggedIn();
    }
    
    /**
     * Get current user data
     */
    public function user() {
        if (!$this->check()) {
            return null;
        }
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }
}
?>
