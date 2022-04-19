<?php

declare(strict_types=1);

namespace App\Email;

use App\Entity\SupplierInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Webmozart\Assert\Assert;

final class SendSupplierApprovedEmail implements SendSupplierApprovedEmailInterface
{
    public function __construct(
        private ChannelContextInterface $channelContext,
        private SenderInterface $emailSender,
        private string $emailCode
    ) {
    }

    public function send(SupplierInterface $supplier): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $locale = $channel->getDefaultLocale();
        Assert::notNull($locale);
        $localeCode = $locale->getCode();
        Assert::notNull($localeCode);

        $this->emailSender->send(
            $this->emailCode,
            [
                $supplier->getEmail(),
            ],
            [
                'supplier' => $supplier,
                'channel' => $channel,
                'localeCode' => $localeCode,
            ]
        );
    }
}
