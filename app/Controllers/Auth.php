<?php
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    public function register()
    {
        $data = [];
        
        // Force debug output to see what's happening
        $method = $this->request->getMethod();
        $postData = $this->request->getPost();
        $allData = $_POST;
        
        $data['debug_message'] = "Method: $method | CodeIgniter POST: " . json_encode($postData) . " | Raw POST: " . json_encode($allData);
        
        if ($this->request->getMethod() === 'post' || $this->request->getMethod() === 'POST') {
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
                    $dsn = "mysql:host=localhost;dbname=lms_latangga;charset=utf8mb4";
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
        } else {
            $data['debug_message'] = 'Registration form loaded.';
        }

        return view('auth/register', $data);
    }

     public function login()
    {
        $data = [];
        
        // Enhanced debug info
        $method = $this->request->getMethod();
        $postData = $this->request->getPost();
        $rawPost = $_POST;
        
        $data['debug_message'] = "Method: $method | Time: " . date('H:i:s') . " | POST data: " . json_encode($postData) . " | Raw POST: " . json_encode($rawPost);
        
        if ($this->request->getMethod() === 'post' || strtolower($this->request->getMethod()) === 'post') {
            $data['debug_message'] .= ' | POST REQUEST RECEIVED';
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            
            // Simple validation
            if (empty($email) || empty($password)) {
                $data['error'] = 'Email and password are required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['error'] = 'Please enter a valid email address.';
            } elseif (strlen($password) < 6) {
                $data['error'] = 'Password must be at least 6 characters.';
            } else {
                try {
                    // Direct database connection using PDO (same as registration)
                    $dsn = "mysql:host=localhost;dbname=lms_latangga;charset=utf8mb4";
                    $pdo = new \PDO($dsn, 'root', '', [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
                    ]);
                    
                    // Find user by email
                    $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
                    $stmt->execute([$email]);
                    $user = $stmt->fetch();
                    
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
                        
                        // Debug: Check if session was set
                        $data['debug_message'] = 'Login successful! Session set. Role: ' . $user['role'] . ' | Redirecting...';
                        
                        // Add success message and redirect
                        session()->setFlashdata('success', 'Login successful! Welcome ' . $user['name']);
                        
                        // Redirect based on role using CodeIgniter's redirect
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
                    
                } catch (\PDOException $e) {
                    $data['error'] = 'Database error: ' . $e->getMessage();
                } catch (\Exception $e) {
                    $data['error'] = 'System error: ' . $e->getMessage();
                }
            }
        } else {
            $data['debug_message'] .= ' | GET REQUEST - FORM LOADED';
        }
        
        return view('auth/login', $data);
    }

}