<?php

declare(strict_types=1);

namespace App\Entity\JWTRefreshToken;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

#[ORM\Entity]
#[ORM\Table(name: 'app_refresh_tokens')]
class RefreshToken extends BaseRefreshToken
{
}
