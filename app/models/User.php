<?php

namespace App\Models;

class User {
    
    public static function findByEmail($email) {
        // TODO: Real DB query
        // Stub for testing
        if ($email === 'test@example.com') {
            return (object)[
                'id' => 1,
                'name' => 'Test User',
                'email' => $email,
                'password_hash' => password_hash('password', PASSWORD_DEFAULT)
            ];
        }
        return null;
    }
    
    public static function create($name, $email, $password) {
        // TODO: Real DB insert
        // Stub: return fake ID
        return time();
    }
}
