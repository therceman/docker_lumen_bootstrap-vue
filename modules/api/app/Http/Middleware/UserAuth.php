<?php

namespace App\Http\Middleware;

use App\DTO\ErrorDTO;
use App\Repository\UserRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAuth
{
    protected UserRepository $userRepository;

    /**
     * Create a new middleware instance.
     *
     * @param UserRepository $userRepository
     * @return void
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $auth_header = $request->header('Authorization');

        // check auth header

        if (is_array($auth_header) || empty($auth_header))
            return response()->json(new ErrorDTO('Authorization header not found'), Response::HTTP_UNAUTHORIZED);

        // process auth key

        $auth_key = str_replace('Bearer ', '', $auth_header);

        // find user in DB

        $user = $this->userRepository->findByAuthKey($auth_key);
        if ($user === null)
            return response()->json(new ErrorDTO('User not found', 2), Response::HTTP_UNAUTHORIZED);

        // bind user to request

        $request->merge(['user' => $user]);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
