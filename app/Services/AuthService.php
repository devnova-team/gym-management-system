<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\RefreshTokenRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private UserRepository $userRepository,
        private RefreshTokenRepository $refreshTokenRepository

    ) {}

    public function login(array $credentials): ?array
    {
        $field = !empty($credentials['email']) ? 'email' : 'phone';

        $user = $field === 'email'
            ? $this->userRepository->findByEmail($credentials['email'])
            : $this->userRepository->findByPhone($credentials['phone']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        $token = Auth::guard('api')->login($user);
        $refreshToken = $this->createRefreshToken($user->id);

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'gym_id' => $user->gym_id,
            'role' => $user->role,
            'refresh_token' => $refreshToken,
        ];
    }

private function createRefreshToken(int $userId): string
{
    $token = bin2hex(random_bytes(64));

    $this->refreshTokenRepository->create([
        'user_id' => $userId,
        'token_hash' => hash('sha256', $token),
        'expires_at' => now()->addDays(14),
    ]);

    return $token;
}



   

public function logout(?string $refreshToken): void
{
    Auth::guard('api')->logout();

    if ($refreshToken) {
        $tokenHash = hash('sha256', $refreshToken);

        $storedToken = $this->refreshTokenRepository
            ->findByTokenHash($tokenHash);

        if ($storedToken) {
            $this->refreshTokenRepository->delete($storedToken);
        }
    }
}

  public function refresh(?string $refreshToken): array
{
    if (!$refreshToken) {
        throw new \RuntimeException('Refresh token is missing.');
    }

    $tokenHash = hash('sha256', $refreshToken);

    $storedToken = $this->refreshTokenRepository->findByTokenHash($tokenHash);

if (!$storedToken || $storedToken->expires_at->isPast()) {
    throw new \RuntimeException('Invalid or expired refresh token.');
}

     $user = $storedToken->user;
  $token = Auth::guard('api')->login($user);

  $this->refreshTokenRepository->delete($storedToken);

$newRefreshToken = $this->createRefreshToken($user->id);

    return [
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
         'refresh_token' => $newRefreshToken,

    ];
}
}