<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;

class Announcement extends BaseController
{
    public function index()
    {
        $announcements = [];
        try {
            $model = new AnnouncementModel();
            $announcements = $model->orderBy('created_at', 'DESC')->findAll();
        } catch (\Throwable $e) {
            $announcements = [];
        }

        return view('announcements', [
            'announcements' => $announcements,
        ]);
    }
}
