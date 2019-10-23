<?php
namespace ant\attribute\helpers;

class ParsableAttributeHelper {
    public static function lookup($params) {
        list ($find, $data) = array_pad($params, 2, null);

        $find = trim($find);

        if (!isset($data[$find])) {
            if (isset($params[2])) {
                return $params[2];
            }
            throw new \Exception('"'.$find.'" is not exist in '.print_r($data,1));
        }
        
        return $data[$find];
    }
}