<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function _construct()
    {
         $this->userModel = new UserModel();  
    }

    public function register()
    {
        if ($this->request->getMethod() === 'post'){
            //set validation
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]'
            ];
            //if validation passes
            if ($this->validate($rules)){
                $hashedPassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

                $userData = [
                     'name' => $this->request->getPost('name'),
                    'email' => $this->request->getPost('email'),
                    'password' => $hashedPassword,
                    'role' => 'student'
                ];

                //save user to database
                if ($this->userModel->insert($userData)) {
                    session()->setFlashdata('success', 'Registration successful! Please login.');
                    return redirect()->to('/login');
                } else {
                    session()->setFlashdata('error', 'Registration failed. Please try again.');
                }
            } else {
                // validation failed
                session()->setFlashdata('errors', $this->validator->getErrors());
            
            }
        }
         return view ('auth/register');
    }

     public function login()
    {
        if ($this->request->getMethod() === 'post'){
            //set validation
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required',
            ];
            //if validation passes
            if ($this->validate($rules)){
                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');

                //find user email
                $user = $this->userModel->where('email', $email)->first();

                if ($user && password_verify($password,$user['password'])){
                    //create user session
                     $sessionData = [
                        'user_id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'logged_in' => true
                    ];
                    session()->set($sessionData);

                    session()->setFlashdata('success', 'Welcome back, ' . $user['name'] . '!');
                    return redirect()->to('/dashboard');
                }else{
                    session()->setFlashdata('error', 'Invalid email or password.');                 
                }
            } else {
                ///Validation failed
                session()->setFlashData('errors',$this->validator->getErrors());                  
            }
        }
         return view ('auth/login');
    }

    public function logout(){
        session()->destroy();
        session()->setFlashdata('succes','You have been logged out successfully.');
        return redirect()->to('/login');
    }

public function dashboard(){
    // Check if user is logged in using helper function
        if (!is_logged_in()) {
            session()->setFlashdata('error', 'Please login to access the dashboard.');
            return redirect()->to('/login');
        }

        $data = [
            'user' => current_user()
        ];

        return view('auth/dashboard', $data);
    }
}






}