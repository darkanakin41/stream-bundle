<?php
namespace Darkanakin41\StreamBundle\Tool;

use Darkanakin41\StreamBundle\Nomenclature\ProviderNomenclature;

class StreamTool {
    public static function getIdentifiant($url){
        if(strpos ($url, "dailymotion" )){
            $step1 = implode('', array_slice(explode('/', $url), -1));
            $step2_explode = explode('_', $step1);
            return $step2_explode[0];
        }
        if(strpos ($url, "twitch" )){
            $url = str_replace("/profile", "", $url);
            $step1 = implode('', array_slice(explode('/', $url), -1));
            $step2_explode = explode('?', $step1);
            return $step2_explode[0];
        }
        if(strpos ($url, "hitbox" )){
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
        if(strpos ($url, "azubu" )){
            $step1 = array_slice(explode('/', $url), -1);
            return $step1[0];
        }
        if(strpos ($url, "beam" )){
            $step1 = array_slice(explode('/', $url), -1);
            return $step1[0];
        }
        return $url;
    }

    public static function getProvider($url){
        if(strpos($url, "dailymotion" )){
            return ProviderNomenclature::DAILYMOTION;
        }
        if(strpos($url, "twitch" )){
            return ProviderNomenclature::TWITCH;
        }
        if(strpos($url, "hitbox" )){
            return ProviderNomenclature::HITBOX;
        }
        if(strpos($url, "youtube" )){
            return ProviderNomenclature::YOUTUBE;
        }
        if(strpos($url, "azubu" )){
            return ProviderNomenclature::AZUBU;
        }
        if(strpos($url, "beam" )){
            return ProviderNomenclature::BEAM;
        }
        return "OTHER";
    }
}
