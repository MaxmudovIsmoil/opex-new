<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Http\Requests\LoginRequest;
use App\Ldap\Ldap;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $service
    ) {}


    /**
     * @param LoginRequest $request
     * @throws UnauthorizedException
     * @throws NotFoundException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            
            $result = $this->service->login($request->validated());

            return response()->success($result, 200);
        }
        catch (UnauthorizedException $e) {
            // MailController::sendErrorAdmin($e->getMessage());
            return response()->fail($e->getMessage(), $e->getCode());
        }
        catch (NotFoundException $e) {
            return response()->fail($e->getMessage(), $e->getCode());
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return response()->success('You are logged out.', 200);
    }


//    public function profile(UserProfileRequest $request): JsonResponse
//    {
//        try {
//            $result = $this->service->profile($request->validated());
//            return response()->success($result);
//        }
//        catch(\Exception $e) {
//            return response()->fail($e->getMessage());
//        }
//    }


    public function loginConfluence(Request $request, Ldap $ldap)
    {
        $request->validate(['login' => 'required']);
        $login = strtolower($request->login);

        $check_data = $this->checkDataConfluence($login);


        if(!$check_data['status'])
            return redirect()->back()->withErrors('No permission to use the system');

        $login = $check_data['login'];

        $user = User::where('login', $login)
            ->where('status', 1)
            ->where('is_deleted', 0)
            ->where('ldap', 1)
            ->first();

        if ($user) {
            $handle = $ldap->handle_confluence($login);
            if ($handle['status'] === true && Auth::attempt(['login' => $login]))
                return redirect()->intended('home');
        }
        return redirect()->back()->withErrors($handle['message']);
    }



    public function checkDataConfluence(string $login): array
    {
        $login_project = base64_decode($login);
        $login_project_array = explode(':', $login_project);


        $login = $login_project_array[0] ?? null;
        $project_name = $login_project_array[1] ?? null;
        $time = isset($login_project_array[2]) ? (int)$login_project_array[2] : null;
        $login = base64_decode($login);


        if($project_name != 'weekly')
            return ['status' => false];

        $start_date = strtotime("-3 minutes");
        $end_date   = strtotime("+1 hour");


        if(!(($start_date <= $time) && ($time < $end_date)))
            return ['status' => false];


        return ['status' => true, 'login' => $login];
    }
}
