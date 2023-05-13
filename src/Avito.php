<?php

namespace Avito\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Avito extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * @var string
     */
    public $baseDomain = 'avito.ru';

    /**
     * @var string
     */
    public $protocol = 'https://';

    /**
     * @var array
     */
    public $scopes = [];

    /**
     * @var string
     */
    public $authorizationHeader = 'Bearer';

    /**
     * @var array
     */
    public $headers = [
        'User-Agent' => 'Avito/oAuth Client 1.0',
    ];

    /**
     * Avito constructor.
     * @param array $options
     * @param array $collaborators
     */
    public function __construct($options = [], $collaborators = [])
    {
        parent::__construct($options, $collaborators);

        if (isset($options['baseDomain'])) {
            $this->baseDomain = $options['baseDomain'];
        }
    }

    /**
     * @param string $domain
     */
    public function setBaseDomain($domain)
    {
        $this->baseDomain = $domain;
    }

    /**
     * @return string
     */
    public function getBaseDomain()
    {
        return $this->baseDomain;
    }

    /**
     * @param string $protocol
     */
    public function setProtocol($protocol)
    {
        if (!in_array($protocol, ['https://', 'http://'], true)) {
            return;
        }

        $this->protocol = $protocol;
    }

    /**
     * @param string|null $redirectUri
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->urlAccount() . 'oauth/';
    }

    /**
     * Get access token url to retrieve token
     *
     * @param array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->urlAccount() . 'oauth2/access_token';
    }

    /**
     * Get the default scopes used by this provider.
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return [];
    }


    public function urlAccount()
    {
        return $this->getProtocol() . $this->getBaseDomain() . '/';
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     * @return AvitoResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new AvitoResourceOwner($response);
    }

    /**
     * @inheritDoc
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw AvitoException::errorResponse($response, $data);
        }
    }

    /**
     * Get provider url to fetch user details
     *
     * @param AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->urlAccount() . 'v3/user';
    }
}
