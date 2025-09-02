<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    public function __construct()
    {
        helper(['form', 'url']);
    }
    public function register()
{
    $data = [];
    
    // Check if the request is a POST request (form submission)
    if ($this->request->getMethod() === 'post') {
        // Your existing form submission logic
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');
        $role = $this->request->getPost('role');
        
        // Simple validation
        if (empty($name) || empty($email) || empty($password) || empty($passwordConfirm) || empty($role)) {
            $data['error'] = 'All fields are required.';
            $data['debug_message'] = 'Missing required fields.';
        } elseif ($password !== $passwordConfirm) {
            $data['error'] = 'Passwords do not match.';
            $data['debug_message'] = 'Password confirmation failed.';
        } elseif (strlen($password) < 6) {
            $data['error'] = 'Password must be at least 6 characters.';
            $data['debug_message'] = 'Password too short.';
        } else {
            try {
                // Direct database connection using PDO
                $dsn = "mysql:host=localhost;dbname=lms_marquez;charset=utf8mb4";
                $pdo = new \PDO($dsn, 'root', '', [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
                ]);
                
                $data['debug_message'] = 'Database connected successfully.';
                
                // Check if email exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $data['error'] = 'Email already exists.';
                    $data['debug_message'] = 'Email duplicate found.';
                } else {
                    // Validate role
                    $allowedRoles = ['student', 'instructor', 'admin'];
                    if (!in_array($role, $allowedRoles)) {
                        $role = 'student'; // Default to student if invalid role
                    }
                    
                    // Insert new user
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
                    
                    if ($stmt->execute([$name, $email, $hashedPassword, $role])) {
                        session()->setFlashdata('success', 'Registration successful! You can now log in.');
                        return redirect()->to('/auth/login');
                    } else {
                        $data['error'] = 'Failed to insert user into database.';
                        $data['debug_message'] = 'Insert query failed.';
                    }
                }
                
            } catch (\PDOException $e) {
                $data['error'] = 'Database error: ' . $e->getMessage();
                $data['debug_message'] = 'PDO Exception: ' . $e->getMessage();
            } catch (\Exception $e) {
                $data['error'] = 'System error: ' . $e->getMessage();
                $data['debug_message'] = 'General Exception: ' . $e->getMessage();
            }
        }
    }

    // Ito ang missing na linya. Ilipat ito sa labas ng 'if' statement.
    return view('auth/register', $data);
}

    public function login()
    {
        $data = [];
        
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required|min_length[6]'
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');
                
                $userModel = new UserModel();
                $user = $userModel->where('email', $email)->first();
                
                if ($user && password_verify($password, $user['password'])) {
                    // Set session data
                    $sessionData = [
                        'id'        => $user['id'],
                        'name'      => $user['name'],
                        'email'     => $user['email'],
                        'role'      => $user['role'],
                        'isLoggedIn'=> true
                    ];
                    session()->set($sessionData);
                    
                    session()->setFlashdata('success', 'Login successful! Welcome ' . $user['name']);
                    
                    // Redirect based on role
                    if ($user['role'] === 'admin') {
                        return redirect()->to('/admin/dashboard');
                    } elseif ($user['role'] === 'instructor') {
                        return redirect()->to('/instructor/dashboard');
                    } else {
                        return redirect()->to('/student/dashboard');
                    }
                } else {
                    $data['error'] = 'Invalid email or password.';
                }
            }
        }
        
        return view('auth/login', $data);
    }

     public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login');
    }

    public function dashboard()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        return view('dashboard');
    }

     public function studentDashboard()
    {
        // Debug session data
        $sessionData = [
            'isLoggedIn' => session()->get('isLoggedIn'),
            'role' => session()->get('role'),
            'name' => session()->get('name'),
            'email' => session()->get('email')
        ];
        
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'student') {
            return redirect()->to('/auth/login');
        }
        
        $data = [
            'user' => [
                'name' => session()->get('name') ?: 'Test User',
                'email' => session()->get('email') ?: 'test@example.com',
                'role' => session()->get('role') ?: 'student'
            ],
            'debug_session' => $sessionData
        ];
        
        return view('dashboards/student', $data);
    }

     public function instructorDashboard()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'instructor') {
            return redirect()->to('/auth/login');
        }
        
        $data = [
            'user' => [
                'name' => session()->get('name'),
                'email' => session()->get('email'),
                'role' => session()->get('role')
            ]
        ];
        
        return view('dashboards/instructor', $data);
    }

     public function adminDashboard()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth/login');
        }
        
        $data = [
            'user' => [
                'name' => session()->get('name'),
                'email' => session()->get('email'),
                'role' => session()->get('role')
            ]
        ];
        
        return view('dashboards/admin', $data);
    }

    public function debug()
    {
        $data = [
            'method' => $this->request->getMethod(),
            'uri' => $this->request->getUri(),
            'post_data' => $this->request->getPost(),
            'get_data' => $this->request->getGet(),
            'headers' => $this->request->headers()
        ];
        return $this->response->setJSON($data);
    }

    public function testDb()
    {
        try {
            $db = \Config\Database::connect();
            
            // Test connection
            if (!$db->connID) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to connect to database']);
            }
            
            // Test table exists
            $tableExists = $db->tableExists('users');
            
            // Test insert capability
            $testData = [
                'name' => 'Test User ' . time(),
                'email' => 'test' . time() . '@example.com',
                'password' => password_hash('testpass', PASSWORD_DEFAULT),
                'role' => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $insertResult = $db->table('users')->insert($testData);
            
            // Clean up test data
            if ($insertResult) {
                $db->table('users')->where('email', $testData['email'])->delete();
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'database_connected' => true,
                'table_exists' => $tableExists,
                'insert_test' => $insertResult ? 'success' : 'failed',
                'database_name' => $db->getDatabase()
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}