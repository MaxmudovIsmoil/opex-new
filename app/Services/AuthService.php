<?php

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Http\Resources\UserResource;
use App\Ldap\Ldap;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{

    public function __construct(
        protected User $model,
    ) {}

    public function login(array $data): array
    {
        $p = Hash::make(123);
        log::info($p);

        $user = $this->model
            ->where('username', $data['username'])
            ->where('status', 1)
            ->first();

        if ($user === null) {
            throw new NotFoundException(message:'<i class="fa-solid fa-magnifying-glass-minus"></i> User not found', code:404);
        }

        if ($user->ldap == 1) {
            // use ldap
            // $this->authenticateUserWithLdap($user, $data['password']);

            // not use ldap for developer test branch
           $this->authenticateAdminUser($user, $data['password']);
        }
        else {
            $this->authenticateAdminUser($user, $data['password']);
        }

        return [
            'user' => new UserResource($user),
            'token' => $user->createToken($data['username'])->plainTextToken
        ];
    }


   
    protected function authenticateUserWithLdap(object $user, string $password): void
    {
        // dd($user, $password);
        $ldap = new Ldap();
        $ldapResponse = $ldap->handle($user->username, $password);

        if ($ldapResponse['status'] === true) {
            $passwordData = ['password' => Hash::make($password)];
            $this->model->updateOrCreate(['id' => $user->id], $passwordData);
        } else {
            throw new UnauthorizedException(message: '<i class="fa-solid fa-triangle-exclamation"></i> The password is incorrect.', code: 401);
        }
    }

    protected function authenticateAdminUser(object $user, string $password): void
    {
        if (!Hash::check($password, $user->getAuthPassword())) {
            throw new UnauthorizedException(message: '<i class="fa-solid fa-triangle-exclamation"></i> The password is incorrect.', code: 401);
        }
    }

    public function profile(array $data)
    {
        $userId = Auth::id();
        $user = User::findOrfail($userId);
        if (isset($data['password'])) {
            $user->fill(['password' => Hash::make($data['password'])]);
        }
        $user->fill([
            'full_name' => $data['name'],
            'username' => $data['username']
        ]);
        $user->save();
        return $userId;
    }
}
