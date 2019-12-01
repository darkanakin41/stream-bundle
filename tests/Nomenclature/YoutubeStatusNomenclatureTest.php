<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Nomenclature;

use PHPUnit\Framework\TestCase;

class YoutubeStatusNomenclatureTest extends TestCase
{
    const ACTIVE = 'active';
    const ALL = 'all';
    const COMPLETED = 'completed';
    const UPCOMING = 'upcoming';
    public function testGetAllConstants()
    {
        $this->assertSame([
            'ACTIVE' => 'active',
            'ALL' => 'all',
            'COMPLETED' => 'completed',
            'UPCOMING' => 'upcoming',
        ], YoutubeStatusNomenclature::getAllConstants());
    }
}
