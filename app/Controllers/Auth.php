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
                    'role'     => 'user'
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
        $data = [];
    }

    public function logout(){
        
    }

    public function dashboard(){
        
    }
}
