<?php

return [
    // jwt issuer
    'iss' => 'http://api.melajah.com',

    // signature algorithm
    'alg' => 'HS256',

    // token expiration in minutes
    'ttl' => 1440,

    // secret key
    'secrets' => app('audiences')->secrets(),

    // authenticator callback
    'authenticator' => function ($claim)
    {
        $audience = Auth::audience()->loginUsingId($claim->aud);
        if ( ! $audience) throw new \Chazzuka\Jweetor\JWTException(403, 'Invalid audience credentials');

        if (starts_with($claim->sub, 'user:'))
        {
            $id = (int) str_replace('user:', '', $claim->sub);
            $user = Auth::loginUsingId($id);
            if ( ! $user) throw new \Chazzuka\Jweetor\JWTException(403, 'Invalid user credentials');
        }
    },
];