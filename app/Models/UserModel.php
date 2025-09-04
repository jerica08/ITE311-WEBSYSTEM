<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
	protected $table = 'users';
	protected $primaryKey = 'id';
	protected $allowedFields = ['username', 'email', 'password', 'first_name', 'last_name', 'role'];
	protected $useTimestamps = true;
	
	// Validation rules - simplified for testing
	protected $validationRules = [
		'username' => 'required|min_length[3]|max_length[100]',
		'email' => 'required|valid_email',
		'password' => 'required|min_length[6]',
		'first_name' => 'required|min_length[2]|max_length[100]',
		'last_name' => 'required|min_length[2]|max_length[100]',
		'role' => 'required|in_list[admin,instructor,student]'
	];
	
	protected $validationMessages = [
		'username' => [
			'required' => 'Username is required',
			'min_length' => 'Username must be at least 3 characters long',
			'max_length' => 'Username cannot exceed 100 characters',
			'is_unique' => 'This username is already taken'
		],
		'email' => [
			'required' => 'Email is required',
			'valid_email' => 'Please enter a valid email address',
			'is_unique' => 'This email is already registered'
		],
		'password' => [
			'required' => 'Password is required',
			'min_length' => 'Password must be at least 6 characters long'
		],
		'first_name' => [
			'required' => 'First name is required',
			'min_length' => 'First name must be at least 2 characters long',
			'max_length' => 'First name cannot exceed 100 characters'
		],
		'last_name' => [
			'required' => 'Last name is required',
			'min_length' => 'Last name must be at least 2 characters long',
			'max_length' => 'Last name cannot exceed 100 characters'
		],
		'role' => [
			'required' => 'Role is required',
			'in_list' => 'Please select a valid role'
		]
	];
	
	/**
	 * Authenticate user with email and password
	 */
	public function authenticate($email, $password)
	{
		$user = $this->where('email', $email)->first();
		
		if ($user && password_verify($password, $user['password'])) {
			return $user;
		}
		
		return false;
	}
	
	/**
	 * Hash password before saving
	 */
	protected function beforeInsert(array $data)
	{
		if (isset($data['password'])) {
			$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
		}
		return $data;
	}
	
	/**
	 * Hash password before updating
	 */
	protected function beforeUpdate(array $data)
	{
		if (isset($data['password'])) {
			$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
		}
		return $data;
	}
}


