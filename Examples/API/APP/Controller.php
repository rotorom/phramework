<?php

namespace Examples\API\APP;

use Examples\API\APP\Models\Request;

/**
 * Base controller
 * contains shortcut helper methods.
 * @author Xenophon Spafaridis <nohponex@gmail.com>
 */
class Controller
{
    /**
     * Shortcut to \Phramework\Phramework::view.
     *
     * @param array $params
     *
     * @uses \Phramework\Phramework::view
     */
    protected static function view($params = [])
    {
        \Phramework\Phramework::view($params);
    }

    /**
     * If !assert then a NotFoundException exception is thrown.
     *
     * @param mixed  $assert
     * @param string $exceptionMessage [Optional] Default is 'resource_not_found'
     *
     * @throws \Phramework\Exceptions\NotFoundException
     */
    protected static function exists($assert, $exceptionMessage = 'resource_not_found')
    {
        if (!$assert) {
            throw new \Phramework\Exceptions\NotFoundException($exceptionMessage);
        }
    }

    /**
     * If !assert then a Exception exception is thrown.
     *
     * @param mixed  $assert
     * @param string $exceptionMessage [Optional] Default is 'unknown_error'
     *
     * @throws Exception
     */
    protected static function testUnknownError($assert, $exceptionMessage = 'unknown_error')
    {
        if (!$assert) {
            throw new \Exception($exceptionMessage);
        }
    }

    /**
     * Checks if the user is a system user.
     *
     * @return array user
     */
    protected static function checkPermissionSystem()
    {
        return Request::checkPermission(Request::USERGROUP_SYSTEM);
    }
}
