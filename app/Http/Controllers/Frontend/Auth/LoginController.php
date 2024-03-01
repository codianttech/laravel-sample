<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\LoginRequest;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $userRepositoryInterface;

    /**
     * Constructor
     *
     * @param UserRepositoryInterface $userRepositoryInterface Interface for user repository
     *
     * @return void
     */
    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

    /**
     * Display login form
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('frontend.auth.login');
    }

    /**
     * Handle user login
     *
     * @param LoginRequest $request Request parameter for login
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(LoginRequest $request)
    {
        // Check if login attempts exceed the limit
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            $this->sendLockoutResponse($request);
        }

        $credentials = $request->only(['email', 'password']);

        // Throw error if the email and password are not correct
        throw_if(
            !Auth::validate($credentials),
            CustomException::class,
            __('message.error.invalid_login_details'),
            400
        );

        // Check login and retrieve user
        Auth::validate($credentials);
        $user = $this->userRepositoryInterface->checkLogin($request->all());

        $this->incrementLoginAttempts($request);

        if (!empty($user)) {
            if (!Hash::check($request->password, $user->password)) {
                return $this->sendFailedLoginResponse($request);
            }
            if (User::STATUS_INACTIVE == $user->status) {
                // Send response as inactive if the user is not activated
                return response()->json(
                    [
                        'success' => false,
                        'data' => [],
                        'message' => trans('auth.inactive_account'),
                    ],
                    401
                );
            }
            // Send the login response as the user is valid
            return $this->sendLoginResponse($user, $request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        if (Auth::guard('web')->check()) {
            $this->guard('web')->logout();
        }
        session()->flash('success', trans('auth.log_out'));

        return redirect()->route('login');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(User $user, Request $request)
    {
        $this->guard('web')->login($user, $request->get('remember'));

        $request->session()->regenerate();
        $this->clearLoginAttempts($request);
        if ($request->expectsJson()) {
            $redirectionUrl = route('dashboard');
            $data = [
                'success' => true,
                'message' => trans('auth.log_in'),
                'redirectionUrl' => $redirectionUrl,
            ];

            return $this->responseSend($data);
        }
    }

    /**
     * Send a JSON response.
     *
     * @param  array  $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseSend(array $data)
    {
        return response()->json($data);
    }
}
