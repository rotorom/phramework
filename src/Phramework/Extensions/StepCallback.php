<?php

namespace Phramework\Extensions;

use Phramework\API;

class StepCallback
{
    /**
     * Step callbacks
     */
    const STEP_AFTER_AUTHENTICATION_CHECK = 'STEP_AFTER_AUTHENTICATION_CHECK';
    const STEP_BEFORE_REQUIRE_CONTROLLER = 'STEP_BEFORE_REQUIRE_CONTROLLER';
    const STEP_BEFORE_CALL_METHOD = 'STEP_BEFORE_CALL_METHOD';
    const STEP_BEFORE_CLOSE = 'STEP_BEFORE_CLOSE';

    /**
     * Hold all step callbacks
     * @var array Array of arrays
     */
    protected $stepCallback;

    /**
     * Add a step callback
     *
     * Step callbacks, are callbacks that executed when the API reaches
     * a certain step, multiple callbacks can be set for the same step.
     * @param string $step
     * @param function $callback
     * @since 0.1.1
     * @throws \Exception callback_is_not_function_exception
     */
    public function add($step, $callback)
    {

        //Check if step is allowed
        \Phramework\Models\Validate::enum($step, [
            self::STEP_BEFORE_REQUIRE_CONTROLLER,
            self::STEP_BEFORE_CALL_METHOD,
            self::STEP_BEFORE_CLOSE,
        ]);

        if (!is_callable($callback)) {
            throw new \Exception(
                API::getTranslated('callback_is_not_function_exception')
            );
        }

        //If stepCallback list is empty
        if (!isset($this->stepCallback[$step])) {
            //Initialize list
            $this->stepCallback[$step] = [];
        }

        //Push
        $this->stepCallback[$step][] = $callback;
    }

    /**
     * Execute all callbacks set for this step
     * @param string $step
     */
    public function call($step)
    {
        if (!isset($this->stepCallback[$step])) {
            return null;
        }
        foreach ($this->stepCallback[$step] as $s) {
            if (!is_callable($s)) {
                throw new \Exception(
                    API::getTranslated('callback_is_not_function_exception')
                );
            }
            return $s();
        }
    }

    public function __contstruct()
    {
        $this->stepCallback = [];
    }
}