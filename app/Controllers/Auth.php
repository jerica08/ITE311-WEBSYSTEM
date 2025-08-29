<?php

namespace App\Controllers;


use CodeIgniter\Controller;

class Auth extends Controller
{
    public function register()
    {
        helper(['form']);
        $db =\Config\Database::connect();
        $data = [];

         if ($this->request->getMethod() === 'post'){

            $rules = [
               'name'     => 'required|min_length[3]|max_length[20]',
                'email'    => 'required|valid_email|is_unique[users.email]',
                'role'     => 'required|in_list[student,teacher,admin]',
                'password' => 'required|min_length[6]|max_length[200]',
                'password_confirm'=> 'matches[password]'
                 
            ];
            if ($this->validate($rules)) {
                
                //HASH PASSWORD
                $hashedPassword = password_hash($this->request->getVar('password'), PASSWORD_DEFAULT);

                //SAVE USERDATA
                $builder = $db->table('users');
                $builder->insert([
                    'name'     => $this->request->getVar('name'),
                    'email'    => $this->request->getVar('email'),
                    'password' => $hashedPassword,
                    'role'     => $this->request->getVar('role')
                ]);

                session()->setFlashdata('success','Registration successful! Please log in.');
                return redirect()->to('/auth/login');
            } else {
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
                        'role'     => 'required|in_list[student,teacher,admin]',
                        'isLoggedIn' => true
                    ];

                    session()->set($sessionData);

                    //FLASH MESSAGE AND REDIRECT
                    session()->setFlashdata('success','Welcome back, '. $user['name'] . '!');
                    return redirect()->to('/auth/dashboard');
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

        return redirect()->to('/auth/login');
    }

    public function dashboard(){
        if (!session()->get('isLoggedIn')){
            return redirect()->to('auth/login');
        }

        return view('auth/dashboard');
    }

}
