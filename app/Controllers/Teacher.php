<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Teacher extends BaseController
{
    public function dashboard()
    {
        return view('teacher_dashboard');
    }
}
