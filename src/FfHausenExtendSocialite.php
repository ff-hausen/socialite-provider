<?php

namespace FfHausen\SocialiteProvider;

use SocialiteProviders\Manager\SocialiteWasCalled;

class FfHausenExtendSocialite
{
    public function handle(SocialiteWasCalled $socialiteWasCalled): void
    {
        $socialiteWasCalled->extendSocialite('ffhausen', Provider::class);
    }
}
