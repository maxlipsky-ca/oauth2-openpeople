<?php

namespace MaxLipsky\OAuth2\Client\Token;

use League\OAuth2\Client\Token\AccessToken;
use InvalidArgumentException;
use MaxLipsky\OAuth2\Client\Provider\Exception\OpenPeopleIdentityProviderException;
use MaxLipsky\OAuth2\Client\Provider\Exception\OpenPeopleProviderException;

class OpenPeopleAccessToken extends AccessToken
{
    const OPTION_NAME_TOKEN = 'token';
    const OPTION_NAME_TOKEN_EXPIRY = 'token_expiry_utc';

    /**
     * @throws OpenPeopleProviderException
     */
    public function __construct(array $options = [])
    {
        if (empty($options[self::OPTION_NAME_TOKEN])) {
            throw OpenPeopleProviderException::requiredOption(self::OPTION_NAME_TOKEN);
        }

        if (empty($options[self::OPTION_NAME_TOKEN_EXPIRY])) {
            throw OpenPeopleProviderException::requiredOption(self::OPTION_NAME_TOKEN_EXPIRY);
        }

        $expires = new \DateTime($options[self::OPTION_NAME_TOKEN_EXPIRY]);
        $options['access_token'] = $options[self::OPTION_NAME_TOKEN];
        $options['expires_in'] = $expires->getTimestamp();

        parent::__construct($options);
    }
}
