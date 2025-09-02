<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
	protected $table = 'users';
	protected $primaryKey = 'id';
	protected $allowedFields = ['name', 'email', 'password', 'role'];
	protected $useTimestamps = true;
	protected $createdField = 'created_at';
	protected $updatedField = 'updated_at';
	protected $dateFormat = 'datetime';
	
	protected $validationRules = [
		'name' => 'required|min_length[3]|max_length[100]',
		'email' => 'required|valid_email|max_length[100]|is_unique[users.email]',
		'password' => 'required|min_length[6]|max_length[255]',
		'role' => 'required|in_list[student,instructor,admin]'
	];
	
	protected $validationMessages = [
		'email' => [
			'is_unique' => 'This email is already registered.'
		]
	];
}


