<?php
namespace App\Controllers;

class ChallengeController {
    public function Challenge() {
        // Memanggil view challenge yang ada di app/views/challenge/index.php
        // Jika file anda bernama home.php, ubah nama file di bawah ini
        require_once '../app/views/home/challenge.php';
        
    }
    
}