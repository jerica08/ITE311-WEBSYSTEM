<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $isLoggedIn = (bool) ($session->get('isLoggedIn') ?? $session->get('logged_in') ?? false);
        $role = strtolower((string) ($session->get('role') ?? $session->get('user_role') ?? ''));

        // If not authenticated, send to login
        if (!$isLoggedIn) {
            $session->setFlashdata('error', 'Please login to continue.');
            return redirect()->to('/auth/login');
        }

        $uri = trim((string) $request->getUri()->getPath(), '/');

        // Admin can access /admin/*
        if (str_starts_with($uri, 'admin')) {
            if ($role !== 'admin') {
                $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
                return redirect()->to('/announcements');
            }
            return;
        }

        // Teacher can access /teacher/*
        if (str_starts_with($uri, 'teacher')) {
            if (!in_array($role, ['teacher', 'instructor'], true)) {
                $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
                return redirect()->to('/announcements');
            }
            return;
        }

        // Students are allowed to /student/* and /announcements (handled by routes if needed)
        if (str_starts_with($uri, 'student')) {
            if ($role !== 'student') {
                $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
                return redirect()->to('/announcements');
            }
            return;
        }

        // For all other routes this filter should not block
        return;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing
    }
}
