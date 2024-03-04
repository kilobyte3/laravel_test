<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * home controller
 */
class HomeController extends Controller
{
    /**
     * index
     */
    public function index(): View
    {
        return view('home');
    }
}
