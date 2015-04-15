<?php

namespace Phramework\API\models;

use Phramework\API\exceptions\missing_paramenters;
use Phramework\API\exceptions\incorrect_paramenters;

/**
 * Provides various methods for validating data
 * 
 * @author Spafaridis Xenophon <nohponex@gmail.com>
 * @since 0
 * @package Phramework
 * @subpackage API
 * @category models
 */
class validate {

    /**
     * Text type
     */
    const TYPE_TEXT = 'text';

    /**
     * Multiline text type
     */
    const TYPE_TEXTAREA = 'textarea';

    /**
     * Signed integer type
     */
    const TYPE_INT = 'int';

    /**
     * Unsigned integer type
     */
    const TYPE_UINT = 'uint';

    /**
     * Floating point number type
     */
    const TYPE_FLOAT = 'float';
    
    /**
     * Double presision floating point number type
     */
    const TYPE_DOUBLE = 'double';
    
    /**
     * boolean type
     */
    const TYPE_BOOLEAN = 'boolean';
    
    
    const TYPE_COLOR = 'color';
    const TYPE_USERNAME = 'username';
    const TYPE_EMAIL = 'email';
    const TYPE_PASSWORD = 'password';
    const TYPE_TOKEN = 'token';
    const TYPE_URL = 'url';
    const TYPE_PERMALINK = 'permalink';
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_REGEXP = 'regexp';
    
    /**
     * Unix timestamp (unsigned integer)
     */
    const TYPE_UNIX_TIMESTAMP = 'unix_timestamp';
    
    const TYPE_ENUM = 'enum';
    const TYPE_JSON = 'json';
    const TYPE_JSON_ARRAY = 'json_array';
    const TYPE_ARRAY = 'array';
    
    /**
     * Comma separated array
     * 
     * When used in a validate::model it splits the values ,
     * validates the subtype and returns as array
     * @example aaa,bb,cc,1,2 Example parameter data
     * @property string subtype [Optional] Defines the subtype, default text
     */
    const TYPE_ARRAY_CSV = 'array_csv';
    
    const REGEXP_RESOURCE_ID = '/^d+$/';  //'/^[A-Za-z0-9_]{3,128}$/' );
    const REGEXP_USERNAME = '/^[A-Za-z0-9_\.]{3,64}$/';
    const REGEXP_TOKEN = '/^[A-Za-z0-9_]{3,48}$/';
    const REGEXP_PERMALINK = '/^[A-Za-z0-9_]{3,32}$/';
    
    /**
     * Custom data types validators
     * @var type 
     */
    private static $custom_types = [];
    
    /**
     * Register a custom data type
     * 
     * It can be used to validate models
     * @param string $type
     * @param function $callback
     * @throws \Exception
     */
    public static function register_custom_type($type, $callback) {
        if(!is_callable($callback)) {
            throw new \Exception(__('callback_is_not_function_exception'));
        }
        
        self::$custom_types[$type]= ['callback' => $callback];
    }
    
    /**
     * Validate a custom data type
     * @param string $type Custom type's name
     * @param mixed $value Value to test
     * @param string $field_name [Optional] field's name
     * @param array $model [optional]
     * @throws \Exception type_not_found
     * @throws incorrect_paramenters if validation fails
     */
    public static function validate_custom_type($type, $value, $field_name, $model=[]) {
        if(!isset(self::$custom_types[$type])) {
            throw new \Exception('type_not_found');
        }
        $callback = self::$custom_types[$type]['callback'];
                            
        $output;

        if($callback($value, $model, $output) === FALSE) {
            //Incorrect
            throw new incorrect_paramenters([$field_name]);
        }else{
            //update output
            return $output;
        }        
    }
    
    /**
     * Define available operators
     */
    /*public static $operators = [ OPERATOR_EMPTY, OPERATOR_EQUAL, OPERATOR_GREATER, OPERATOR_GREATER_EQUAL,
        OPERATOR_ISSET, OPERATOR_LESS, OPERATOR_LESS_EQUAL, OPERATOR_NOT_EMPTY,
        OPERATOR_NOT_EQUAL, OPERATOR_NOT_ISSET, OPERATOR_ISNULL, OPERATOR_NOT_ISNULL,
        OPERATOR_IN, OPERATOR_NOT_IN, OPERATOR_LIKE, OPERATOR_NOT_LIKE];*/

    /**
     * Validate a signed integer
     * 
     * @param string|int $int Input value
     * @param int|NULL $min Minimum value. Optional default is NULL, if NULL then the minum value is skipped
     * @param int|NULL $max Maximum value. Optional default is NULL, if NULL then the maximum value is skipped
     * @param string $field_name [Optional] Field's name, used in IncorrectParamentersException. Optional default is int
     * @throws incorrect_paramenters if value is not correct
     * @return intigerReturns the value of the input value as int
     */
    public static function int($int, $min = NULL, $max = NULL, $field_name = 'int') {
        $options = [];
        if ($min !== NULL) {
            $options['min_range'] = $min;
        }
        if ($max !== NULL) {
            $options['max_range'] = $max;
        }
        if (filter_var($int, FILTER_VALIDATE_INT, ['options' => $options]) === FALSE) {
            throw new incorrect_paramenters([ $field_name]);
        }
        return intval($int);
    }

    /**
     * Validate an unsigned integer
     * @param string|int $int Input value
     * @param int $min Minimum value. Optional default is 0.
     * @param int|NULL $max Maximum value. Optional default is NULL, if NULL then the maximum value is skipped
     * @param String $field_name Field's name, used in IncorrectParamentersException. Optional default is uint
     * @throws incorrect_paramenters if value is not correct
     * @return intigerReturns the value of the input value as int
     */
    public static function uint($int, $min = 0, $max = NULL, $field_name = 'uint') {
        $options = [ 'min_range' => $min];
        if ($max !== NULL) {
            $options['max_range'] = $max;
        }
        if (filter_var($int, FILTER_VALIDATE_INT, ['options' => $options]) === FALSE) {
            throw new incorrect_paramenters([ $field_name]);
        }
        return intval($int);
    }

    /**
     * Validate a floating point number
     * @param string|float|int $number
     * @param float|NULL $min Minimum value. Optional default is NULL, if NULL then the minum value is skipped
     * @param float|NULL Maximum value. Optional default is NULL, if NULL then the maximum value is skipped
     * @param string $field_name [Optional] Field's name, used in IncorrectParamentersException. Optional default is number
     * @return float Returns the input value as float
     * @throws incorrect_paramenters
     */
    public static function float($number, $min = NULL, $max = NULL, $field_name = 'float') {
        $options = [];
        if ($min != NULL) {
            $options['min_range'] = $min;
        }
        if ($max != NULL) {
            $options['max_range'] = $max;
        }
        if (filter_var($number, FILTER_VALIDATE_FLOAT, ['options' => $options]) === FALSE) {
            throw new incorrect_paramenters([ $field_name]);
        }

        return floatval($number);
    }
    
     /**
     * Validate a double presision floating point number
     * @param string|double|float|int $number
     * @param float|NULL $min Minimum value. Optional default is NULL, if NULL then the minum value is skipped
     * @param float|NULL Maximum value. Optional default is NULL, if NULL then the maximum value is skipped
     * @param string $field_name [Optional] Field's name, used in IncorrectParamentersException. Optional default is number
     * @return float Returns the input value as double
     * @throws incorrect_paramenters
     */
    public static function double($number, $min = NULL, $max = NULL, $field_name = 'double') {
        $options = [];
        if ($min != NULL) {
            $options['min_range'] = $min;
        }
        if ($max != NULL) {
            $options['max_range'] = $max;
        }
        if (filter_var($number, FILTER_VALIDATE_FLOAT, ['options' => $options]) === FALSE) {
            throw new incorrect_paramenters([ $field_name]);
        }

        return doubleval($number);
    }

    /**
     * Validate an email address
     * @param type $email
     * @param string $field_name [Optional] Field's name, used in IncorrectParamentersException. Optional default is email
     * @return string Return the email address
     * @throws incorrect_paramenters
     */
    public static function email($email, $field_name = 'email') {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
            throw new incorrect_paramenters([ $field_name]);
        }
        return $email;
    }

    /**
     * Validate a url
     * @param type $url
     * @param string $field_name [Optional] Field's name, used in IncorrectParamentersException. Optional default is url
     * @return string Return the url
     * @throws incorrect_paramenters
     */
    public static function url($url, $field_name = 'url') {
        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            throw new incorrect_paramenters([ $field_name]);
        }
        return $url;
    }

    /**
     * Validate a permalink id
     * @param type $permalink
     * @param string $field_name [Optional] Field's name, used in IncorrectParamentersException. Optional default is permalink
     * @return string Return the permalink
     * @throws incorrect_paramenters
     */
    public static function permalink($permalink, $field_name = 'permalink') {
        if (!preg_match(self::REGEXP_PERMALINK, $permalink)) {
            throw new incorrect_paramenters([ $field_name]);
        }
        return $permalink;
    }

    /**
     * 
     * @param string $token
     * @param string $field_name [Optional] Field's name, used in IncorrectParamentersException. Optional default is token
     * @return string Return the token
     * @throws incorrect_paramenters
     */
    public static function token($token, $field_name = 'token') {
        if (!preg_match(self::REGEXP_TOKEN, $token)) {
            throw new incorrect_paramenters([ $field_name]);
        }
        return $token;
    }

    /**
     * Check if input value is in allowed values
     * @param string|int $input Input array to check
     * @param array $allowed Array of strings or number, defines the allowed input values
     * @param string $field_name [Optional] Field's name, used in IncorrectParamentersException. Optional default is enum
     * @throws incorrect_paramenters if value is not correct
     * @return returns the value of the input value
     */
    public static function enum($input, $allowed, $field_name = 'enum') {

        //Check if array was given for $values value
        if (!is_array($allowed)) {
            throw new Exception('Array is expencted as value');
        }
        //Check if input doesen't exist in $values array
        if (!in_array($input, $allowed)) {
            throw new incorrect_paramenters([ $field_name]);
        }

        return $input;
    }

    /**
     * Validate SQL date, datetime
     * @param string $date Input date
     * @param string $field_name [Optional] Field's name, used in IncorrectParamentersException. Optional default is date
     * @return type
     * @throws incorrect_paramenters
     */
    public static function sql_date($date, $field_name = 'date') {
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $date, $matches)) {
            if (checkdate($matches[2], $matches[3], $matches[1])) {
                return $date;
            }
        }
        throw new incorrect_paramenters([ $field_name]);
    }

    /**
     * Validate color
     * @param type $color Input color
     * @param string $field_name [Optional] [Optional] Field's name, used in IncorrectParamentersException. Optional default is color
     * @param string $type Color value type. Optional, default is hex
     * @return type
     * @throws incorrect_paramenters
     * @todo Implement additional types
     */
    public static function color($color, $field_name = 'color', $type = 'hex') {
        if (!preg_match('/^#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{8}$/', $color)) {
            throw new incorrect_paramenters([ $field_name]);
        }
        return $color;
    }

    /**
     * Validate an operator
     * @param type $operator
     * @param string $field_name [Optional] [Optional]
     * @return type
     * @throws incorrect_paramenters
     */
    public static function operator($operator, $field_name = 'operator') {
        if (!in_array($operator, self::$operators)) {
            throw new incorrect_paramenters([ $field_name]);
        }
        return $operator;
    }

    /**
     * Validate a regexp
     * @param string input
     * @param string regexp
     * @param string $field_name [Optional]
     * @return type
     * @throws incorrect_paramenters
     */
    public static function regexp($input, $regexp, $field_name = 'regexp') {
        if (!preg_match($regexp, $input)) {
            throw new incorrect_paramenters([ $field_name]);
        }
        return $input;
    }
    
    /**
     * Validate a regexp
     * @param string input
     * @param string $field_name [Optional]
     * @return type
     * @throws incorrect_paramenters
     */
    public static function boolean($input, $field_name) {
        return \Phramework\API\models\filter::boolean($input);
    }
    /**
     * Validate a model
     * @param array $parameters Request parameters
     * @param array $model Model
     * @return boolean
     * @throws Exception
     * @throws incorrect_paramenters
     * @throws missing_paramenters
     */
    public static function model(&$parameters, $model) {
        $incorrect = [];
        $missing = [];
        foreach ($model as $key => $value) {

            if (!isset($parameters[$key])) {
                if (is_array($value) && (
                    ( isset($value['required']) && $value['required']) ||
                    in_array('required', $value, TRUE) === TRUE) ) {
                    array_push($missing, $key);
                } else if (is_array($value) && isset($value['default'])) {
                    $parameters[$key] = $value['default'];
                }
            } else {
                if (!is_array($value)) {
                    $parameters[$key] = strip_tags(self::filter_STRING($parameters[$key]));
                    continue;
                }

                switch ($value['type']) {
                    case self::TYPE_INT :
                        $options = [];
                        if (isset($value['max'])) {
                            $options['max_range'] = $value['max'];
                        }
                        if (isset($value['min'])) {
                            $options['min_range'] = $value['min'];
                        }
                        if (filter_var($parameters[$key], FILTER_VALIDATE_INT, [ 'options' => $options]) === FALSE) {
                            array_push($incorrect, $key);
                        } else {
                            $parameters[$key] = intval($parameters[$key]);
                        }
                        break;
                    case self::TYPE_UINT :
                    case self::TYPE_UNIX_TIMESTAMP :
                        $options = ['min_range' => 0];
                        if (isset($value['max'])) {
                            $options['max_range'] = $value['max'];
                        }
                        if (isset($value['min'])) {
                            $options['min_range'] = $value['min'];
                        }
                        if (filter_var($parameters[$key], FILTER_VALIDATE_INT, [ 'options' => $options]) === FALSE) {
                            array_push($incorrect, $key);
                        } else {
                            $parameters[$key] = intval($parameters[$key]);
                        }
                        break;
                    case self::TYPE_DOUBLE :
                        //Replace comma with dot
                        $parameters[$key] = str_replace(',', '.', $parameters[$key]);
                        if (!filter_var($parameters[$key], FILTER_VALIDATE_FLOAT)) {
                            array_push($incorrect, $key);
                        } else {
                            $parameters[$key] = doubleval($parameters[$key]);

                            if (isset($value['max']) && $parameters[$key] > $value['max']) {
                                array_push($incorrect, $key);
                            }
                            if (isset($value['min']) && $parameters[$key] < $value['min']) {
                                array_push($incorrect, $key);
                            }
                        }
                        break;
                    case self::TYPE_FLOAT :
                        //Replace comma with dot
                        $parameters[$key] = str_replace(',', '.', $parameters[$key]);
                        if (!filter_var($parameters[$key], FILTER_VALIDATE_FLOAT)) {
                            array_push($incorrect, $key);
                        } else {
                            $parameters[$key] = floatval($parameters[$key]);

                            if (isset($value['max']) && $parameters[$key] > $value['max']) {
                                array_push($incorrect, $key);
                            }
                            if (isset($value['min']) && $parameters[$key] < $value['min']) {
                                array_push($incorrect, $key);
                            }
                        }
                        break;
                    case self::TYPE_USERNAME :
                        if (!preg_match(self::REGEXP_USERNAME, $parameters[$key])) {
                            array_push($incorrect, $key);
                        }
                        break;
                    case self::TYPE_PERMALINK :
                        if (!preg_match(self::REGEXP_PERMALINK, $parameters[$key])) {
                            array_push($incorrect, $key);
                        }
                        break;
                    case self::TYPE_TOKEN :
                        if (!preg_match(self::REGEXP_TOKEN, $parameters[$key])) {
                            array_push($incorrect, $key);
                        }
                        break;
                    case self::TYPE_COLOR :
                        if (!preg_match('/^#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{8}$/', $parameters[$key])) {
                            array_push($incorrect, $key);
                        }
                        break;
                    case self::TYPE_EMAIL :
                        if (!empty($parameters[$key]) && filter_var($parameters[$key], FILTER_VALIDATE_EMAIL) === FALSE) {
                            array_push($incorrect, $key);
                        }
                        break;
                    case self::TYPE_URL :
                        if (!filter_var($parameters[$key], FILTER_VALIDATE_URL)) {
                            array_push($incorrect, $key);
                        }
                        break;
                    case self::TYPE_DATE :
                    case self::TYPE_DATETIME :
                        if (!self::validate_sql_date($parameters[$key])) {
                            array_push($incorrect, $key);
                        }
                        break;
                    case self::TYPE_REGEXP :
                        if (!isset($value['regexp'])) {
                            throw new \Exception(__('regexp_not_set_exception'));
                        }
                        if (!preg_match($value['regexp'], $parameters[$key])) {
                            array_push($incorrect, $key);
                        }
                        break;
                    case self::TYPE_PASSWORD :
                        break;
                    case self::TYPE_ENUM :
                        if (!isset($value['values'])) {
                            //Internal error ! //TODO @security
                            throw new \Exception('Values not set');
                        }
                        if (!in_array($parameters[$key], $value['values'])) {
                            array_push($incorrect, $key);
                        }
                        break;
                    case self::TYPE_JSON_ARRAY :
                        $temp = [];

                        //Force to array when is not []
                        if (!$parameters[$key]) {
                            $parameters[$key] = [];
                        }
                        foreach ($parameters[$key] as $t) {
                            $ob = json_decode($t, FALSE);
                            if ($ob === null) {
                                array_push($incorrect, $key);
                            } else {
                                //Overwrite json
                                $temp[] = $ob;
                            }
                        }
                        $parameters[$key] = $temp;
                        break;
                    case self::TYPE_JSON :
                        $ob = json_decode($parameters[$key], FALSE);
                        if ($ob === null) {
                            array_push($incorrect, $key);
                        } else {
                            //Overwrite json
                            $parameters[$key] = $ob;
                        }
                        break;
                    case self::TYPE_ARRAY:
                        //Get single value
                        if (!is_array($parameters[$key])) {
                            $parameters[$key] = [ $parameters[$key]];
                        }

                        if (isset($value['max']) && count($parameters[$key]) > $value['max']) {
                            array_push($incorrect, $key);
                        }

                        if (isset($value['min']) && count($parameters[$key]) < $value['min']) {
                            array_push($incorrect, $key);
                        }
                        break;
                    case self::TYPE_ARRAY_CSV:
                        if(!is_string($parameters[$key])) {
                            array_push($incorrect, $key);
                        }else{
                            $values = mbsplit(',', $parameters[$key]);
                            
                            $subtype = (
                                isset($value['subtype'])
                                ? $value['subtype']
                                : validate::TYPE_TEXT
                            );
                            
                            //Validate every record of this subtype
                            foreach($values as &$v) {
                                //Create temporary model
                                $m = [ $key => $v];
                                
                                //Validate this model
                                validate::model($m, [
                                    $key => ['type' => $subtype]
                                    ]
                                );
                                
                                //Overwrite $v
                                $v = $m[$key];
                            }
                            $parameters[$key] = $values;
                        }
                        break;
                    case self::TYPE_TEXT :
                    case self::TYPE_TEXTAREA :
                    default :
                        //Check if is custom_type
                        if(isset(self::$custom_types[$value['type']])) {
                            $callback = self::$custom_types[$value['type']]['callback'];
                            
                            $output;
                                                                                    
                            if($callback($parameters[$key], $value, $output) === FALSE) {
                                //Incorrect
                                array_push($incorrect, $key);
                            }else{
                                //update output
                                $parameters[$key]=$output;
                            }                            
                        }else{
                            if (isset($value['max'])) {
                                if (mb_strlen($parameters[$key]) > $value['max']) {
                                    array_push($incorrect, $key);
                                }
                            }
                            if (isset($value['min'])) {
                                if (mb_strlen($parameters[$key]) < $value['min']) {
                                    array_push($incorrect, $key);
                                }
                            }
                            if (!in_array('raw', $value)) {
                                $parameters[$key] = strip_tags(filter::string($parameters[$key]));
                            }
                        }
                }
            }
        }
        if ($incorrect) {
            throw new incorrect_paramenters($incorrect);
        } elseif ($missing) {
            throw new missing_paramenters($missing);
        }
        return TRUE;
    }
    
    /**
     * Check if callback is valid
     * @link http://www.geekality.net/2010/06/27/php-how-to-easily-provide-json-and-jsonp/ source
     * @param type $subject
     * @return type
     */
    public function is_valid_callback($subject) {
        
        $identifier_syntax
          = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

        $reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case',
          'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue',
          'for', 'switch', 'while', 'debugger', 'function', 'this', 'with',
          'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum',
          'extends', 'super', 'const', 'export', 'import', 'implements', 'let',
          'private', 'public', 'yield', 'interface', 'package', 'protected',
          'static', 'null', 'true', 'false');

        return preg_match($identifier_syntax, $subject)
            && ! in_array(mb_strtolower($subject, 'UTF-8'), $reserved_words);
    }
}