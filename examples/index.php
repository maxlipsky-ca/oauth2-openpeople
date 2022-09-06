<?php

require __DIR__ . '/../vendor/autoload.php';

use MaxLipsky\OAuth2\Client\Provider\OpenPeople;
use MaxLipsky\OAuth2\Client\Provider\Exception\OpenPeopleProviderException;
use MaxLipsky\OAuth2\Client\Provider\Exception\OpenPeopleIdentityProviderException;

// Replace these with your token settings
$username = 'your_username';
$password = 'your_password';

try {
    $provider = new OpenPeople([
        'username' => $username,
        'password' => $password,
    ]);

    $token = $provider->getToken();
    echo $token->getToken();

    //    Uncomment if you want to make this request. Fees may be charged!
//    $request = $provider->getAuthenticatedRequest('POST', 'https://api.openpeoplesearch.com/api/v1/Consumer/PhoneSearch', $token,
//        [
//            'body' => json_encode(['phoneNumber' => '+14845219743'])
//        ]
//    );
//    $response = $provider->getResponse($request);
//    print_r($response->getBody()->getContents());

} catch (OpenPeopleProviderException|OpenPeopleIdentityProviderException $e) {
    echo $e->getMessage();
}