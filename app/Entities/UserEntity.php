<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class UserEntity extends Entity
{
    protected $attributes = [
        'id'            => null,
        'name'          => null,
        'email'         => null,
        'password'      => null,
        'role'          => 'user',
        'avatar'        => null,
        'google_avatar' => null,
        'firebase_uid'  => null,
        'created_at'    => null,
        'updated_at'    => null,
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function setPassword(string $password): static
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }
}
