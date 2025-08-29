<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Auth extends BaseController
{
    public function register()
    {
        helper(['form']);
        $db = \Config\Database::connect();
        $data = [];

        if ($this->request->getMethod() === 'post') {
            // Debug: Log form submission
            log_message('info', 'Registration form submitted');
            log_message('info', 'POST data: ' . json_encode($this->request->getPost()));

            $rules = [
                'name'     => 'required|min_length[3]|max_length[100]',
                'email'    => 'required|valid_email|is_unique[users.email]',
                'role'     => 'required|in_list[student,teacher,admin]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]'
            ];
            
            if ($this->validate($rules)) {
                try {
                    // Hash password
                    $hashedPassword = password_hash($this->request->getVar('password'), PASSWORD_DEFAULT);

                    // Prepare user data
                    $userData = [
                        'name'       => $this->request->getVar('name'),
                        'email'      => $this->request->getVar('email'),
                        'password'   => $hashedPassword,
                        'role'       => $this->request->getVar('role'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    // Save user data
                    $builder = $db->table('users');
                    $result = $builder->insert($userData);

                    if ($result) {
                        $insertedId = $db->insertID();
                        log_message('info', 'User registered successfully with ID: ' . $insertedId);
                        
                        session()->setFlashdata('success', 'Registration successful! Please log in.');
                        return redirect()->to('login');
                    } else {
                        $error = $db->error();
                        log_message('error', 'Database insert failed: ' . json_encode($error));
                        $data['error'] = 'Failed to create account. Database error: ' . ($error['message'] ?? 'Unknown error');
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Registration exception: ' . $e->getMessage());
                    $data['error'] = 'Database error: ' . $e->getMessage();
                }
            } else {
                log_message('info', 'Validation failed: ' . json_encode($this->validator->getErrors()));
                $data['validation'] = $this->validator;
            }
        }

        return view('auth/register', $data);
    }
    
    

    public function login(){

        helper(['form']);
        $db =\Config\Database::connect();
        $data = [];

         if ($this->request->getMethod() === 'post'){

            $rules = [
               
                'email'    => 'required|valid_email',
                'password' => 'required|min_length[6]|max_length[200]',
                                
            ];

            if ($this->validate($rules)) {
                                
                
                $email    = $this->request->getVar('email');
                $password = $this->request->getVar('password');

                //CHECK THE DATABASE FOR USER
                $builder = $db->table('users');
                $user = $builder->where('email', $email)->get()->getRowArray();
                
                if($user && password_verify($password,$user['password'])){

                    //CREATE USER SESSION
                    $sessionData = [
                        'userID' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'isLoggedIn' => true
                    ];

                    session()->set($sessionData);

                    //FLASH MESSAGE AND REDIRECT
                    session()->setFlashdata('success','Welcome back, '. $user['name'] . '!');
                    return redirect()->to('/dashboard');
                } else {
                    $data['error'] = 'Invalid email or password.';
                }
            } else {
                $data['validation'] = $this->validator;
            }
                        
         }
        return view('auth/login', $data);
       
    }

    public function logout(){
        session()->destroy();

        return redirect()->to('/login');
    }

    public function dashboard(){
        if (!session()->get('isLoggedIn')){
            return redirect()->to('/login');
        }

        return view('auth/dashboard');
    }

}
