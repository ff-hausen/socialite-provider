<?php

namespace FfHausen\SocialiteProvider;

use GuzzleHttp\RequestOptions;
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
        ];
    }

    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'name' => $user['first_name'].' '.$user['last_name'],
            'email' => $user['email'],
            'avatar' => $user['image_url'],
        ]);

    }
}
