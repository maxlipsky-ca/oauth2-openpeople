<?php

namespace MaxLipsky\OAuth2\Client\Provider;

use League\OAuth2\Client\Grant\AbstractGrant;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use MaxLipsky\OAuth2\Client\OptionProvider\OpenPeopleOptionProvider;
use MaxLipsky\OAuth2\Client\Provider\Exception\OpenPeopleIdentityProviderException;
use MaxLipsky\OAuth2\Client\Provider\Exception\OpenPeopleProviderException;
use MaxLipsky\OAuth2\Client\Token\OpenPeopleAccessToken;
use Psr\Http\Message\ResponseInterface;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;

class OpenPeople extends AbstractProvider
{
    use BearerAuthorizationTrait;

    const OPTION_NAME_USERNAME = 'username';
    const OPTION_NAME_PASSWORD = 'password';

    protected string $username;
    protected string $password;

    /**
     * @throws OpenPeopleProviderException
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        if (empty($options[self::OPTION_NAME_USERNAME])) {
            throw OpenPeopleProviderException::requiredOption(self::OPTION_NAME_USERNAME);
        }

        if (empty($options[self::OPTION_NAME_PASSWORD])) {
            throw OpenPeopleProviderException::requiredOption(self::OPTION_NAME_PASSWORD);
        }

        $this->username = $options[self::OPTION_NAME_USERNAME];
        $this->password = $options[self::OPTION_NAME_PASSWORD];
        $collaborators = ['optionProvider' => new OpenPeopleOptionProvider()];

        parent::__construct($options, $collaborators);
    }

    /**
     * @var string Key used in a token response to identify the resource owner.
     */
    const ACCESS_TOKEN_RESOURCE_OWNER_ID = null;

    /**
     * Default host
     *
     * @var string
     */
    protected $host = 'https://api.openpeoplesearch.com';

    /**
     * @return string[]
     */
    protected function getDefaultHeaders(): array
    {
        return [
            'Accept' => '*/*',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->host.'/api/v1/User/authenticate';
    }

    /**
     * @param ResponseInterface $response
     * @param array|string $data
     * @throws IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 400) {
            throw new OpenPeopleIdentityProviderException(
                isset($data['errorMessages']) ? implode(' ', $data['errorMessages']) : $response->getReasonPhrase(),
                $statusCode,
                $response
            );
        }
    }

    public function getToken()
    {
        return $this->getAccessToken('password', [
            'username' => $this->username,
            'password' => $this->password
        ]);
    }

    /**
     * @throws OpenPeopleProviderException
     */
    protected function createAccessToken(array $response, AbstractGrant $grant): OpenPeopleAccessToken
    {
        return new OpenPeopleAccessToken($response);
    }

    /**
     * @return array
     */
    protected function getDefaultScopes(): array
    {
        return [];
    }

    /**
     * @throws OpenPeopleProviderException
     */
    public function getBaseAuthorizationUrl()
    {
        throw OpenPeopleProviderException::clientCredentialsOnly();
    }

    /**
     * @throws OpenPeopleProviderException
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        throw OpenPeopleProviderException::clientCredentialsOnly();
    }

    /**
     * @throws OpenPeopleProviderException
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        throw OpenPeopleProviderException::clientCredentialsOnly();
    }
}