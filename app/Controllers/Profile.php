<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = (int) ($session->get('user_id') ?? 0);
        $userModel = new UserModel();
        $user = null;
        if ($userId > 0) {
            $user = $userModel->find($userId);
        }

        if (!$user) {
            // Fallback to session values if DB lookup fails
            $user = [
                'id'    => $userId,
                'name'  => (string) ($session->get('name') ?? $session->get('user_name') ?? ''),
                'email' => (string) ($session->get('email') ?? $session->get('user_email') ?? ''),
                'role'  => (string) ($session->get('role') ?? $session->get('user_role') ?? ''),
            ];
        }

        return view('profile', [
            'user' => $user,
        ]);
    }
}
