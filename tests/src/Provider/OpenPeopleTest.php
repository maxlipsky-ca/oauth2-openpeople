<?php

namespace MaxLipsky\OAuth2\Client\Test\Provider;

use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\QueryBuilderTrait;
use MaxLipsky\OAuth2\Client\Provider\Exception\OpenPeopleIdentityProviderException;
use MaxLipsky\OAuth2\Client\Provider\Exception\OpenPeopleProviderException;
use MaxLipsky\OAuth2\Client\Token\OpenPeopleAccessToken;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class OpenPeopleTest extends TestCase
{
    use QueryBuilderTrait;

    protected $provider;

    /**
     * @throws OpenPeopleProviderException
     */
    protected function setUp(): void
    {
        $this->provider = new \MaxLipsky\OAuth2\Client\Provider\OpenPeople([
            'username' => 'mock_username',
            'password' => 'mock_password',
        ]);
    }

    protected function getJsonFile($file, $encode = false)
    {
        $json = file_get_contents(dirname(dirname(dirname(__FILE__))).'/'.$file);
        $data = json_decode($json, true);

        if ($encode && json_last_error() == JSON_ERROR_NONE) {
            return $data;
        }

        return $json;
    }

    public function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }

    /**
     * @throws OpenPeopleProviderException
     */
    public function testBaseAuthorizationUrl(): void
    {
        $this->expectException(OpenPeopleProviderException::class);
        $url = $this->provider->getBaseAuthorizationUrl();
    }

    /**
     * @throws OpenPeopleProviderException
     */
    public function testGetResourceOwnerDetailsUrl(): void
    {
        $token = new OpenPeopleAccessToken(['token' => 'mock_access_token', 'token_expiry_utc' => '2022-09-12T19:48:41.6838702Z']);
        $this->expectException(OpenPeopleProviderException::class);
        $url = $this->provider->getResourceOwnerDetailsUrl($token);
    }

    public function testGetBaseAccessTokenUrl(): void
    {
        $params = [];

        $url = $this->provider->getBaseAccessTokenUrl($params);
        $uri = parse_url($url);

        $this->assertEquals('api.openpeoplesearch.com', $uri['host']);
        $this->assertEquals('/api/v1/User/authenticate', $uri['path']);
    }

    public function testGetAccessToken(): void
    {
        $accessToken = $this->getJsonFile('token_response.json');
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->andReturn($accessToken);
        $response->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $response->shouldReceive('getStatusCode')->andReturn(200);

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($response);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getToken();

        $this->assertEquals('mock_token', $token->getToken());
        $this->assertNotNull($token->getExpires());
    }

    public function testExceptionThrownWhenErrorObjectReceived(): void
    {
        $status = rand(401,599);
        $error = $this->getJsonFile('error_response.json');
        $postResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $postResponse->shouldReceive('getBody')->andReturn($error);
        $postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $postResponse->shouldReceive('getStatusCode')->andReturn($status);
        $postResponse->shouldReceive('getStatusCode')->andReturn($status);
        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')
            ->times(1)
            ->andReturn($postResponse);
        $this->provider->setHttpClient($client);

        $this->expectException(OpenPeopleIdentityProviderException::class);
        $token = $this->provider->getToken();
    }
}