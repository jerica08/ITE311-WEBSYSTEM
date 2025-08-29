<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Database;

class Auth extends BaseController
{
    public function register()
    {   
        $session = session();
        helper(['form']);

        if ($this->request->getMethod() === 'post') {
            
            $rules = [
                'name'     => 'required|min_length[3]|max_length[100]',
                'email'    => 'required|valid_email|is_unique[users.email]',
                'role'     => 'required|in_list[student,teacher,admin]',
                'password' => 'required|min_length[6]|max_length[255]',
                'password_confirm' => 'matches[password]'
            ];

            if (!$this->validate($rules)){
                return view('auth/register',["validation" => $this->validator]);
            }else{
                $db = Database::connect();
                $builder = $db->table('users');

                $data =[
                     
                    'name' => $this->request->getVar('name'),
                    'email' => $this->request->getVar('email'),
                    'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
                    'role' => 'user'
                ];

                $builder->insert($data);

                $session->setFlashdata('success', 'Registration successful. You can now login.');
                return redirect()->to('/login');
            }
            
           
        }

        return view('auth/register');
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
