<?php

namespace FfHausen\SocialiteProvider;

use GuzzleHttp\RequestOptions;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    public static function additionalConfigKeys(): array
    {
        return [
            'host',
        ];
    }

    protected function getAuthUrl($state): string
    {
        return $this->getConfig('host').'/oauth/authorize';
    }

    protected function getTokenUrl(): string
    {
        return $this->getConfig('host').'/oauth/token';
    }

    protected function getUserByToken($token): array
    {
        $userUrl = $this->getConfig('host').'/api/user';

        $response = $this->getHttpClient()->get($userUrl, [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return (array) json_decode($response->getBody()->getContents(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User)->map([
            'id' => $user['id'],
            'nickname' => null,
            'name' => $user['first_name'].' '.$user['last_name'],
            'email' => $user['email'],
            'avatar' => $user['image_url'],
        ]);

    }
}
