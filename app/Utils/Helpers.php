<?php 

namespace App\Utils;

class Helpers {
    
    public static function createRandomCode($length = 8, $IN_Params = [])
    {
        $IN_Params['Upper_Case']        = isset($IN_Params['Upper_Case']) ? $IN_Params['Upper_Case'] : true;
        $IN_Params['Lower_Case']        = isset($IN_Params['Lower_Case']) ? $IN_Params['Lower_Case'] : true;
        $IN_Params['Number']            = isset($IN_Params['Number']) ? $IN_Params['Number'] : true;
        $IN_Params['Special_Character'] = isset($IN_Params['Special_Character']) ? $IN_Params['Special_Character'] : false;
    
        $chars = '';
        if ($IN_Params['Lower_Case']) {
            $chars .= "abcdefghijklmnopqrstuvwxyz";
        }
    
        if ($IN_Params['Upper_Case']) {
            $chars .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
    
        if ($IN_Params['Number']) {
            $chars .= "0123456789";
        }
    
        if ($IN_Params['Special_Character']) {
            $chars .= "!@#$%^&*()_-=+;:,.";
        }
    
        return substr(str_shuffle($chars), 0, $length);
    }
}