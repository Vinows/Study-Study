<?php
namespace App\Controllers;

class ProgressController {
    public function Progress() {
        // Memanggil view progress
        require_once '../app/views/home/progress.php';
    }
}