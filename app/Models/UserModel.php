<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
	protected $table = 'users';
	protected $primaryKey = 'id';
	protected $allowedFields = ['name', 'email', 'password', 'role'];
	protected $useTimestamps = true;
	
	// Validation rules - simplified for testing
	protected $validationRules = [
		'name' => 'required|min_length[2]|max_length[100]',
		'email' => 'required|valid_email|is_unique[users.email]',
		'password' => 'required|min_length[6]',
		'role' => 'required|in_list[admin,user]'
	];
	
	protected $validationMessages = [
		'name' => [
			'required' => 'Name is required',
			'min_length' => 'Name must be at least 2 characters long',
			'max_length' => 'Name cannot exceed 100 characters'
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


