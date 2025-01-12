<?php

declare(strict_types=1);

namespace App\Api\Command\Account;

use Sylius\Bundle\ApiBundle\Attribute\ChannelCodeAware;
use Sylius\Bundle\ApiBundle\Attribute\LocaleCodeAware;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Serializer\Attribute\Groups;

#[ChannelCodeAware]
#[LocaleCodeAware]
#[Exclude]
class RegisterShopUser
{
    public function __construct(
        public readonly string $channelCode,
        public readonly string $localeCode,
        #[Groups('sylius:shop:customer:create')]
        public readonly string $email,
        #[Groups('sylius:shop:customer:create')]
        public readonly string $password,
        public readonly bool $subscribedToNewsletter = false,
    ) {
    }
}
