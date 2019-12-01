<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Nomenclature;

use PHPUnit\Framework\TestCase;

class StatusNomenclatureTest extends TestCase
{
    public function testGetAllConstants()
    {
        $this->assertSame([
            'ONLINE' => 'online',
            'OFFLINE' => 'offline'
        ], StatusNomenclature::getAllConstants());
    }
}
