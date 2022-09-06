<?php

namespace MaxLipsky\OAuth2\Client\Provider\Exception;

class OpenPeopleProviderException extends \Exception
{
    public static function clientCredentialsOnly(): OpenPeopleProviderException
    {
        return new self('This oauth2 client only supports client credentials grant.');
    }

    public static function requiredOption($name): OpenPeopleProviderException
    {
        return new self(sprintf('Required option not passed: "%s"', $name));
    }
}