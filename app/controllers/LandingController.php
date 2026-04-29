<?php

namespace App\Controllers;

class LandingController {
    public function index() {
        // Render Duolingo-style landing page
        require __DIR__ . '/../views/Landing.php';
    }
}
