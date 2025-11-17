<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Account;
use Symfony\Component\HttpFoundation\Response;
class ApiTokenTest
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if(!$token){
            return response('Authentication token not provided', 401);
        }
        $account = Account::where('token_value', $token)->first();
        if (!$account) {
            return response('Проблема с аккаунтом', 401);
        }

        $request->attributes->add(['auth_account' => $account]);

        return $next($request);
    }
}
