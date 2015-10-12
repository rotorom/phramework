<?php

namespace Phramework\Validate;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-10-05 at 22:11:07.
 */
class BaseValidatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Boolean
     */
    protected $bool;
    protected $int;
    protected $str;
    protected $uint;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->bool = new Boolean;
        $this->int = new Integer;
        $this->str = new String;
        //$this->uint = new UnsignedInteger;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers Phramework\Validate\BaseValidator::createFromJSON
     */
    public function testCreateFromJSON()
    {
        $json = '{
            "type": "integer",
            "minimum" : -1000,
            "maximum" : 1000
        }';

        $validationObject = Integer::createFromJSON($json);

        $this->assertInstanceOf(BaseValidator::class, $validationObject);

        $this->assertSame(
            -1000,
            $validationObject->minimum
        );
    }

    /**
     * @covers Phramework\Validate\BaseValidator::createFromJSON
     */
    public function testCreateFromJSON2()
    {
        $json = '{
            "type": "unsignedinteger",
            "minimum" : -1000,
            "maximum" : 1000
        }';

        $validationObject = BaseValidator::createFromJSON($json);

        $this->assertInstanceOf(BaseValidator::class, $validationObject);

        $this->assertSame(
            -1000,
            $validationObject->minimum
        );
    }

    /**
     * @covers Phramework\Validate\BaseValidator::createFromJSON
     * @expectedException Exception
     */
    public function testCreateFromJSONFailure()
    {
        $json = '{
            "type": "xyz",
            "minimum" : -1000,
            "maximum" : 1000
        }';

        $validationObject = Integer::createFromJSON($json);
    }

    /**
     * @covers Phramework\Validate\BaseValidator::createFromJSON
     */
    public function testCreateFromJSON3()
    {
        $json = '
        {
          "type": "object",
          "properties": {
            "data": {
              "type": "object",
              "properties": {
                "type": {
                  "type": "enum",
                  "values": ["user"]
                },
                "order": {
                  "type": "unsignedinteger",
                  "default" : 0
                }
              },
              "required": ["type"]
            }
          }
        }';

        $validationObject = Object::createFromJSON($json);

        $this->assertInstanceOf(Object::class, $validationObject);
        $this->assertInternalType(
            'object',
            $validationObject->properties
        );
        $this->assertInstanceOf(Object::class, $validationObject->properties->data);

        $data = $validationObject->properties->data;

        $this->assertInstanceOf(
            Enum::class,
            $data->properties->type
        );
        $this->assertInstanceOf(
            UnsignedInteger::class,
            $data->properties->order
        );
        $this->assertInternalType(
            'array',
            $data->properties->type->values
        );

        $this->assertSame(
            0,
            $data->properties->order->default
        );
    }

    /**
     * @covers Phramework\Validate\BaseValidator::parse
     */
    public function testParseSuccess()
    {
        $input = [
            'weight' => '5',
            'obj' => [
                'valid' => 'true',
                'number' => 10.2,
            ]
        ];

        $validationObject = new Object(
            [ //properties
                'weight' => new Integer(-10,10, true),
                'obj' => new Object(
                    [ //properties
                        'valid' => new Boolean(),
                        'number' => new Number(0,100),
                        'not_required' => (new Number(0,100))->setDefault(5.5),
                    ],
                    ['valid'] //required
                )
            ],
            ['weight'] //required
        );

        $record = $validationObject->parse($input);

        $this->assertInternalType('object', $record);
        $this->assertInternalType('object', $record->obj);
        $this->assertInternalType('float', $record->obj->not_required);
        $this->assertEquals(5, $record->weight);
        $this->assertTrue( $record->obj->valid);
        $this->assertEquals(5.5, $record->obj->not_required);
    }

    /**
     * @covers Phramework\Validate\BaseValidator::parse
     */
    public function testParseSuccess2()
    {
        $input = '5';

        $validationModel = new Integer(0,6);

        $cleanInput = $validationModel->parse($input);

        $this->assertInternalType('integer', $cleanInput);
        $this->assertEquals(5, $cleanInput);
    }

    /**
     * @covers Phramework\Validate\BaseValidator::parse
     * @expectedException Exception
     * @todo \Phramework\Exceptions\MissingParametersException
     */
    public function testParseFailure()
    {
        $input = [
            'weight' => '5',
            'obj' => [
                //'valid' => 'true',
                'number' => 10.2,
            ]
        ];

        $validationObject = new Object(
            [ //properties
                'weight' => new Integer(-10,10, true),
                'obj' => new Object(
                    [ //properties
                        'valid' => new Boolean(),
                        'number' => new Number(0,100),
                        'not_required' => (new Number(0,100))->setDefault(5.5),
                    ],
                    ['valid'] //required
                )
            ],
            ['weight'] //required
        );

        $record = $validationObject->parse($input);
    }

    /**
     * @covers Phramework\Validate\BaseValidator::parse
     * @expectedException Exception
     * @todo \Phramework\Exceptions\IncorrectParametersException
     */
    public function testParseFailure2()
    {
        $input = [
            'weight' => '555', //out of range
            'obj' => [
                'valid' => 'ΝΟΤ_VALID',
                'number' => 10.2
            ]
        ];

        $validationObject = new Object(
            [ //properties
                'weight' => new Integer(-10,10, true),
                'obj' => new Object(
                    [ //properties
                        'valid' => new Boolean(),
                        'number' => new Number(0,100),
                        'not_required' => (new Number(0,100))->setDefault(5),
                    ],
                    ['valid'] //required
                )
            ],
            ['weight'] //required
        );

        $record = $validationObject->parse($input);

    }

    /**
     * @covers Phramework\Validate\BaseValidator::parse
     * @expectedException Exception
     */
    public function testParseFailure3()
    {
        $input = '87';

        $validationModel = new Integer(0,6);

        $cleanInput = $validationModel->parse($input);
    }
}
