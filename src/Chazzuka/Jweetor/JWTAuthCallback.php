<?php namespace Chazzuka\Jweetor;

interface JWTAuthCallback {

    /**
     * @param object $claim
     *
     * @return void
     * @throws \Chazzuka\Jweetor\JWTException
     */
    public function authenticate($claim);

}