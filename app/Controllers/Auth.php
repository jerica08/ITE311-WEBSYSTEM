<?php

namespace App\Controllers;


use CodeIgniter\Controller;

class Auth extends Controller
{
    public function register()
    {
        helper(['form']);
        $data = [];

        if ($this->request->getMethod() === 'post'){
            $rules = [
               'name'     => 'required|min_length[3]|max_length[20]',
                'email'    => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]|max_length[200]',
            ];
            if ($this->validate($rules)) {
                $model = new UserModel();

                $model->save([
                    'name'     => $this->request->getVar('name'),
                    'email'    => $this->request->getVar('email'),
                    'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
                ]);

                return redirect()->to('/auth/login');
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('auth/register', $data);
        }
    }

    public function login(){

    }

    public function logout(){
        
    }

    public function dashboard(){
        
    }
}
