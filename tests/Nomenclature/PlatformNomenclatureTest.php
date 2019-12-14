<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Tests\Nomenclature;

use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;
use PHPUnit\Framework\TestCase;

class PlatformNomenclatureTest extends TestCase
{
    public function testGetAllConstants()
    {
        $this->assertSame([
            'OTHER' => 'other',
            'TWITCH' => 'twitch',
            'YOUTUBE' => 'youtube'
        ], PlatformNomenclature::getAllConstants());
    }
}
