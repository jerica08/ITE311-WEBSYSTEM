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

        $session = session();
        helper(['form']);
       
         if ($this->request->getMethod() === 'post'){

            $rules = [            
                'email'    => 'required|valid_email',
                'password' => 'required|min_length[6]|max_length[200]',                              
            ];

           if (!$this->validate($rules)){
            return view('auth/login',["validation" => $this->validator]);
           }else{
                $db = Database:: connect();
                $builder = $db->table('users');
                $user = $builder->where('email', $this->request->getVar('email'))->get()->getRowArray();

                if ($user) {
                    $pass = $this->request->getVar('password');
                    if (password_verify($pass, $user['password'])) {
                        $sessionData = [
                            'user_id' => $user['id'],
                            'name' => $user['name'],
                            'email' => $user['email'],
                            'role' => $user['role'],
                            'isLoggedIn' => true
                        ];
                        $session->set($sessionData);
                        $session->setFlashdata('success', 'Welcome back, ' . $user['name'] . '!');
                        return redirect()->to('/dashboard');
                    } else {
                        $session->setFlashdata('error', 'Wrong password.');
                        return redirect()->to('/login');
                    }
                } else {
                    $session->setFlashdata('error', 'Email not found.');
                    return redirect()->to('/login');
                }
           }
                        
         }
        return view('auth/login');
       
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
