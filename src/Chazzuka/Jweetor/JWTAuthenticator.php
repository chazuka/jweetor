<?php namespace Chazzuka\Jweetor;

use Auth;
use Illuminate\Http\Request;

class JWTAuthenticator {

    /**
     * @var string
     */
    protected $iss;

    /**
     * @var int
     */
    protected $ttl = 1440;

    /**
     * @var string
     */
    protected $alg = 'HS256';

    /**
     * @var string|array
     */
    protected $secrets;

    /**
     * @var mixed
     */
    protected $authenticator;

    /**
     * @var string
     */
    protected $headerMethod = 'bearer';

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        foreach (['iss', 'alg', 'ttl', 'secrets', 'authenticator'] as $key)
        {
            if ( ! empty($options[$key])) $this->{$key} = $options[$key];
        }
    }

    /**
     * @param array|string $secrets
     *
     * @return $this
     */
    public function setSecrets($secrets)
    {
        $this->secrets = $secrets;

        return $this;
    }

    /**
     * @param mixed $authenticator
     *
     * @return $this
     */
    public function setAuthenticator($authenticator)
    {
        $this->authenticator = $authenticator;

        return $this;
    }

    /**
     * @param string $alg
     *
     * @return $this
     */
    public function setAlg($alg)
    {
        $this->alg = $alg;

        return $this;
    }

    /**
     * @param int $ttl
     *
     * @return $this
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param  mixed                   $authenticator
     * @param  mixed                   $secrets
     */
    public function authenticateFromRequest(Request $request, $authenticator = null, $secrets = null)
    {
        $token = $this->retrieveToken($request);
        if ( ! $token) throw new JWTException(401, 'Invalid authorization parameters');

        $this->authenticate($token, $authenticator, $secrets);
    }

    /**
     * Retrieve token from request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    public function retrieveToken(Request $request)
    {
        $authorizationToken = $request->header('authorization');
        if ($authorizationToken && starts_with(strtolower($authorizationToken), $this->headerMethod))
        {
            return trim(str_ireplace($this->headerMethod, '', $authorizationToken));
        }
    }

    /**
     * Authenticate from token
     *
     * @param string $token
     * @param mixed  $authenticator
     * @param mixed  $secrets
     */
    public function authenticate($token, $authenticator = null, $secrets = null)
    {
        $secrets = $secrets ?: $this->secrets;
        $claim = $this->decode($token, $secrets);

        $authenticator = $authenticator ?: $this->authenticator;
        if (is_callable($authenticator))
        {
            call_user_func($authenticator, $claim);

            return;
        }

        if (is_string($authenticator)) $authenticator = app($authenticator);

        if ($authenticator instanceof JWTAuthCallback)
        {
            $authenticator->authenticate($claim);

            return;
        }

        throw new \InvalidArgumentException('3rd argument must be a callable iOC bounded or instance of JWTAuthCallback');
    }

    /**
     * Decode string token
     *
     * @param string       $token
     * @param string|array $secrets
     *
     * @return object
     */
    public function decode($token, $secrets = null)
    {
        $secrets = $secrets ?: $this->secrets;

        try {
            return \JWT::decode($token, $secrets);
        } catch (\Exception $e) {
            throw new JWTException(403, $e->getMessage());
        }
    }

    /**
     * Generate token
     *
     * @param string $aud
     * @param string $sub
     * @param string $secret
     * @param string $kid
     *
     * @return string
     */
    public function encode($aud, $sub, $secret, $kid = null)
    {
        return $this->encodeWith(compact('aud', 'sub'), $secret, $kid);
    }

    /**
     * Generate token with extra claims
     *
     * @param array  $claims
     * @param string $secret
     * @param null   $kid
     *
     * @return string
     * @throws \DomainException
     */
    public function encodeWith(array $claims, $secret, $kid = null)
    {

        $time = time();
        $claims = array_merge($claims, [
            'iss' => $this->iss,
            'exp' => $time + ($this->ttl * 60),
            'nbf' => $time,
            'iat' => $time,
        ]);

        if (count(array_diff(['aud', 'sub', 'iss'], array_keys($claims))) !== 0)
        {
            throw new \DomainException('Insufficient claim parameters');
        }

        var_export($claims);

        return \JWT::encode($claims, $secret, $this->alg, $kid);
    }
}