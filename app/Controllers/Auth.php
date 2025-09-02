<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
	public function register()
	{
		$session = session();
		$request = $this->request;

		if ($request->getMethod() === 'post') {
			$validationRules = [
				'name' => 'required|min_length[3]',
				'email' => 'required|valid_email|is_unique[users.email]',
				'password' => 'required|min_length[6]',
				'password_confirm' => 'required|matches[password]'
			];

			if ($this->validate($validationRules)) {
				$userModel = new UserModel();
				$passwordHash = password_hash((string) $request->getPost('password'), PASSWORD_DEFAULT);

				$userData = [
					'name' => (string) $request->getPost('name'),
					'email' => (string) $request->getPost('email'),
					'password_hash' => $passwordHash,
					'role' => 'user'
				];

				if ($userModel->save($userData)) {
					$session->setFlashdata('success', 'Registration successful. Please log in.');
					return redirect()->to('/login');
				}

				$session->setFlashdata('error', 'Unable to register user. Please try again.');
			} else {
				$session->setFlashdata('error', implode("\n", $this->validator->getErrors()));
			}
		}

		return view('auth/register');
	}

	public function login()
	{
		$session = session();
		$request = $this->request;

		if ($request->getMethod() === 'post') {
			$validationRules = [
				'email' => 'required|valid_email',
				'password' => 'required'
			];

			if ($this->validate($validationRules)) {
				$userModel = new UserModel();
				$user = $userModel->where('email', (string) $request->getPost('email'))->first();

				if ($user && password_verify((string) $request->getPost('password'), (string) $user['password_hash'])) {
					$session->set([
						'isLoggedIn' => true,
						'userId' => $user['id'] ?? null,
						'name' => $user['name'] ?? '',
						'email' => $user['email'] ?? '',
						'role' => $user['role'] ?? 'user',
					]);

					$session->setFlashdata('success', 'Welcome back, ' . ($user['name'] ?? 'User') . '!');
					return redirect()->to('/dashboard');
				}

				$session->setFlashdata('error', 'Invalid email or password.');
			} else {
				$session->setFlashdata('error', implode("\n", $this->validator->getErrors()));
			}
		}

		return view('auth/login');
	}

	public function logout()
	{
		session()->destroy();
		return redirect()->to('/login');
	}

	public function dashboard()
	{
		$session = session();
		if (!$session->get('isLoggedIn')) {
			return redirect()->to('/login');
		}

		return view('welcome_message');
	}
}


