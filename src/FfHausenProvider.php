<?php

namespace FfHausen\SocialiteProvider;

use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\App;
use SocialiteProviders\Manager\Contracts\OAuth2\ProviderInterface;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class FfHausenProvider extends AbstractProvider implements ProviderInterface
{
    protected $scopeSeparator = ' ';

    public static function additionalConfigKeys(): array
    {
        return [
            'base_uri',
        ];
    }

    protected function getAuthUrl($state): string
    {
        $base_uri = $this->getConfig('base_uri').'/oauth/authorize';

        return $this->buildAuthUrlFromBase($base_uri, $state);
    }

    protected function getTokenUrl(): string
    {
        return $this->getConfig('base_uri').'/oauth/token';
    }

    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            RequestOptions::HEADERS => $this->getTokenHeaders($code),
            RequestOptions::FORM_PARAMS => $this->getTokenFields($code),
            RequestOptions::VERIFY => ! App::environment('local'),
        ]);

        return json_decode($response->getBody(), true);
    }

    protected function getUserByToken($token): array
    {
        $userUrl = $this->getConfig('base_uri').'/api/user';

        $response = $this->getHttpClient()->get(
            $userUrl, $this->getRequestOptions($token)
        );

        return json_decode($response->getBody(), true);
    }

    public function getRequestOptions(string $token): array
    {
        return [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer '.$token,
            ],
            RequestOptions::VERIFY => ! App::environment('local'),
        ];
    }

    protected function mapUserToObject(array $user)
    {
        $data = $user['data'];

        return (new User)->setRaw($data)->map([
            'id' => $data['id'],
            'name' => $data['first_name'].' '.$data['last_name'],
            'email' => $data['email'],
            'avatar' => $data['image_url'],
        ]);

    }
}
