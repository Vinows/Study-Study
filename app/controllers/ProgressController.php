<?php
namespace App\Controllers;

class ProgressController {
    public function index() {
        // Memanggil view progress
        require_once '../app/views/home/progress.php';
    }
}