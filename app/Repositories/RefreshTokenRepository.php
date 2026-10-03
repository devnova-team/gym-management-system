<?php

namespace App\Repositories;

use App\Models\RefreshToken;

class RefreshTokenRepository
{
    public function create(array $data): RefreshToken
    {
        return RefreshToken::create($data);
    }

    public function findByTokenHash(string $tokenHash): ?RefreshToken
    {
        return RefreshToken::where('token_hash', $tokenHash)->first();
    }

    public function delete(RefreshToken $refreshToken): void
    {
        $refreshToken->delete();
    }
}