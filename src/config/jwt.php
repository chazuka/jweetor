<?php

return [
    // jwt issuer
    'iss' => 'http://api.melajah.com',

    // signature algorithm
    'alg' => 'HS256',

    // token expiration in minutes
    'ttl' => 1440,

    // secret key
    'secrets' => 'Th3M4st3r0FUn1V3rSe',

    // authenticator callback example
    'authenticator' => function ($claim)
    {
        $id = (int) str_replace('user:', '', $claim->sub);
        $user = Auth::loginUsingId($id);
        if ( ! $user) throw new \Chazzuka\Jweetor\JWTException(403, 'Invalid user credentials');
    },
];