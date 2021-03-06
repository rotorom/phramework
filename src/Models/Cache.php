<?php
/**
 * Copyright 2015-2016 Xenofon Spafaridis
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Phramework\Models;

/**
 * Default cache engine
 *
 * WARNING, function not completed yet
 *
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @author Xenofon Spafaridis <nohponex@gmail.com>
 * @since 0
 * @todo Use prefix from the settings file
 * @deprecated since 1.0.0
 */
class Cache
{
    private static $instance = null;
    private static $prefix   = 'phramework_';

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return cache The *Singleton* instance.
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
        try {
            if (!self::$instance && class_exists('Memcached')) {
                self::$instance = new \Memcached();
                self::$instance->addServer('localhost', 11211);

                if (($prefix = \Phramework\Phramework::getSetting('cache_prefix'))) {
                    self::$prefix = $prefix;
                }
            }
        } catch (\Exception $e) {
            self::$instance = null;
        }
    }

    /*
     * Access an memcached object using key
     * if object is not available returns the data using the callback provided by $class, $function, $parameters
     *
     * @todo Rename
     * @todo Use anonymous functions
     */

    public static function memcached($key, $class, $function, $parameters = array(), $time = MEMCACHED_TIME_DEFAULT)
    {
        $data = false;

        $memcached = self::getInstance();
        if ($memcached) {
            $key = self::$prefix . $key;


            $data = $memcached->get($key);
            if ($data) {
                return $data;
            }
            /* if( $memcached->getResultCode() != Memcached::RES_NOTSTORED ){
              return $data;
              } */
        }
        $data = call_user_func_array(array($class, $function), $parameters);

        if ($data && $memcached) {
            $memcached->set($key, $data, $time); // or die ("Failed to save data at the server");
        }
        return $data;
    }

    /**
     *
     * @param string $keys
     * @return boolean
     *
     * @todo rename
     */
    public static function memcachedDelete($keys)
    {
        $memcached = self::getInstance();
        if (!$memcached) {
            return false;
        }
        if (is_array($keys)) {
            foreach ($keys as $k => $v) {
                $keys[$k] = self::$prefix . $v;

                $memcached->delete($keys[$k]);
            }
        } else {
            $key = self::$prefix . $keys;
            $memcached->delete($key);
        }
        //return $memcached->deleteMulti( $keys );
    }

    /**
     * todo rename
     * @return boolean
     */
    public static function memcachedDeleteAll()
    {
        $memcached = self::getInstance();
        if (!$memcached) {
            return false;
        }
        return $memcached->flush(1);
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }
}
