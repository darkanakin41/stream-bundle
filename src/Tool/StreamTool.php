<?php

/*
 * This file is part of the Darkanakin41StreamBundle package.
 */

namespace Darkanakin41\StreamBundle\Tool;

use Darkanakin41\StreamBundle\Nomenclature\PlatformNomenclature;

class StreamTool
{
    public static function getIdentifiant($url)
    {
        if (strpos($url, 'twitch')) {
            $url = str_replace(array('/profile', '/videos'), '', $url);
            $step1 = implode('', array_slice(explode('/', $url), -1));
            $step2_explode = explode('?', $step1);

            return $step2_explode[0];
        }
        if (strpos($url, 'youtube')) {
            $step1 = implode('', array_slice(explode('/', $url), -1));
            $step2_explode = explode('?v=', $step1);
            $step3_explode = explode('&', $step2_explode[1]);

            return $step3_explode[0];
        }

        return $url;
    }

    public static function getProvider($url)
    {
        if (strpos($url, 'twitch')) {
            return PlatformNomenclature::TWITCH;
        }
        if (strpos($url, 'youtube')) {
            return PlatformNomenclature::YOUTUBE;
        }

        return 'OTHER';
    }
}
