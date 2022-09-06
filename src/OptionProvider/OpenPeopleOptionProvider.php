<?php

namespace MaxLipsky\OAuth2\Client\OptionProvider;

use League\OAuth2\Client\OptionProvider\OptionProviderInterface;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Tool\QueryBuilderTrait;

class OpenPeopleOptionProvider implements OptionProviderInterface
{
    use QueryBuilderTrait;

    /**
     * @inheritdoc
     */
    public function getAccessTokenOptions($method, array $params): array
    {
        $options['body'] = json_encode($params);

        return $options;
    }
}