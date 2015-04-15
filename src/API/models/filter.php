<?php

namespace Phramework\API\models;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Provides various methods for filtering data
 *
 * @author Spafaridis Xenophon <nohponex@gmail.com>
 * @since 0
 * @package Phramework
 * @subpackage API
 * @category models
 */
class filter {

    /**
     * Allow only keys of $whitelist in every row of $data array
     * 
     * @param type $data
     * @param type $whitelist
     * @return type
     */
    public static function filter_in($data, $whitelist) {
        if ($data) {
            if (is_object($data)) {
                $data = get_object_vars($data);
            }
            $whitelist = array_flip($whitelist);

            //Filter
            foreach ($data as $key => $value) {
                if (is_object($value)) {
                    $value = get_object_vars($value);
                }
                $data[$key] = array_intersect_key($value, $whitelist);
            }
        }
        return $data;
    }

    /**
     * Allow only keys of $whitelist in object
     * 
     * @param type $data
     * @param type $whitelist
     * @return type
     */
    public static function filter_in_entry($data, $whitelist) {
        if ($data) {
            $whitelist = array_flip($whitelist);

            if (is_object($data)) {
                $data = get_object_vars($data);
            }

            $data = array_intersect_key($data, $whitelist);
        }
        return $data;
    }

    /**
     * Exclude blackisted keys of $blacklist in every row of $data array
     * 
     * @param type $data
     * @param type $blacklist
     * @return type
     */
    public static function out($data, $blacklist) {
        if ($data) {
            $blacklist = array_flip($blacklist);

            //Filter
            foreach ($data as $key => $value) {
                $data[$key] = array_diff_key($value, $blacklist);
            }
        }
        return $data;
    }

    /**
     * Exclude blackisted keys of $blacklist in $data object 
     * 
     * @param type $data
     * @param type $blacklist
     * @return type
     */
    public static function out_entry($data, $blacklist) {
        if ($data) {
            $blacklist = array_flip($blacklist);

            $data = array_diff_key($data, $blacklist);
        }
        return $data;
    }

    /**
     * Filter string, applies FILTER_SANITIZE_STRING
     * @param string $value Input string
     * @param int|NULL $max_length Max length of the string, optional. Default value is NULL (no limit)
     * @return string Returns the filtered string
     */
    public static function string($value, $max_length = NULL) {
        if(!is_string($value)) {
            throw new \Exception('not_a_string');
        }
        $value = filter_var(trim($value), FILTER_SANITIZE_STRING);

        if ($max_length && mb_strlen($value) > $max_length) {
            $value = mb_substr($value, 0, $max_length);
        }
        return $value;
    }

    /**
     * Filter email
     * @param type $value
     * @return string Returns the filtered email
     */
    public static function email($value) {
        return filter_var(trim($value), FILTER_VALIDATE_EMAIL);
    }

    /**
     * Parse input value as boolean
     *
     * @param string|boolean $value Input value
     * @return boolean Return the input value as boolean
     */
    public static function boolean($value) {
        if ($value && strtolower($value) != 'false') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Typecast a value
     * @param mixed $value
     * @param string $type
     * @return mixed The typecasted value
     */
    public static function typecast(&$value, $type) {
        switch ($type) {
            case validate::TYPE_INT:
            case validate::TYPE_UINT:
                $value = intval($value);
                break;
            case validate::TYPE_FLOAT:
                $value = floatval($value);
                break;
            case validate::TYPE_DOUBLE:
                $value = doubleval($value);
                break;
            case validate::TYPE_BOOLEAN:
                $value = boolval($value);
                break;
            case validate::TYPE_UNIX_TIMESTAMP:
                //Add the timezone offset (in minutes)
                $value = intval($value) + 
                    (\Phramework\API\API::get_timezone_offset()*60);
                break;
        }
    }
    
    /**
     * Type cast entry's attributs based on the provided model
     * @param array $entry
     * @param array $model
     * @return array Returns the typecasted entry
     */
    public static function cast_entry($entry, $model) {
        if(!$entry) {
            return $entry;
        }
        foreach($model as $k => $v) {
            if(!isset($entry[$k])) {
                continue;
            }
            //Typecast
            filter::typecast($entry[$k], $v);
        }
        
        return $entry;
    }
    
    /**
     * Type cast each entry of list based on the provided model
     * @param array $list
     * @param array $model
     * @return array Returns the typecasted list
     */
    public static function cast($list, $model) {
        if(!$list) {
            return $list;
        }
        
        //Apply cast entry to each entry
        foreach($list as $k => &$v) {
            $v= self::cast_entry($v, $model);
        }
        
        return $list;
    }
}