<?php

namespace App\Http\Middleware;

use App\Repositories\Contracts\Users as UsersContract;
use Closure;
use Illuminate\Http\Request;

class SuperSimpleAuthenticator
{
    private $users_contract;
    public function __construct(UsersContract $users_contract)
    {
        $this->users_contract = $users_contract;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $api_key = $request->header('api_key', '');
        $user_id = 0;

        if ($api_key != '') {
            $user = $this->users_contract->getUserWithApiKey($api_key);
            if ($user) {
                $user_id = $user->id;
            }
        }

        if ($user_id == 0) {
            return response(['message' => 'Unauthorised'], 401);
        }

        $request->merge(['authenticated_user' => $user_id]);

        return $next($request);
    }
}
