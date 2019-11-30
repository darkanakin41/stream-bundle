<?php
namespace Darkanakin41\StreamBundle\Tool;

use Darkanakin41\StreamBundle\Nomenclature\ProviderNomenclature;

class StreamTool {

    public static function getIdentifiant($url){
        if(strpos ($url, "twitch" )){
            $url = str_replace("/profile", "", $url);
            $step1 = implode('', array_slice(explode('/', $url), -1));
            $step2_explode = explode('?', $step1);
            return $step2_explode[0];
        }
        if(strpos ($url, "youtube" )){
            $step1 = implode('', array_slice(explode('/', $url), -1));
            $step2_explode = explode('?v=', $step1);
            $step3_explode = explode('&', $step2_explode[1]);
            return $step3_explode[0];
        }
        return $url;
    }

    public static function getProvider($url){
        if(strpos($url, "twitch" )){
            return ProviderNomenclature::TWITCH;
        }
        if(strpos($url, "youtube" )){
            return ProviderNomenclature::YOUTUBE;
        }
        return "OTHER";
    }
}
