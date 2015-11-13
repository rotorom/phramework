<?php

namespace Examples\JSONAPI\APP\Models;

use \Phramework\Models\Database;
use \Phramework\Validate\Validate;
use \Phramework\JSONAPI\Relationship;

class Test extends \Phramework\JSONAPI\Model
{
    protected static $type = 'test';
    protected static $endpoint = 'test';
    protected static $table = 'test';

    public static function getValidationModel()
    {
        return new \Phramework\Validate\Object(
            [
                'created_user_id' => (new \Phramework\Validate\UnsignedInteger())
                    ->setDefault(1),
                'title' => new \Phramework\Validate\String(2, 32)
            ],
            ['title']
        );
    }

    public static function getMutable()
    {
        return [
            'title'
        ];
    }

    public static function getRelationships()
    {
        return [
            'created' => new Relationship(
                'created_user_id',
                'user',
                Relationship::TYPE_TO_ONE,
                User::class,
                'id'
            ),
            'comment' => new Relationship(
                'test_comment_id',
                'test_comment',
                Relationship::TYPE_TO_MANY,
                TestComment::class,
                'id'
            )
        ];
    }

    /**
     * Get collection of resources
     *
     * @return \stdClass[]
     */
    public static function get()
    {
        $records = Database::executeAndFetchAll(
            'SELECT * FROM `test`',
            [],
            self::getCast()
        );

        return self::collection($records);
    }

    /**
     * Get a single entry by id
     * @param int $id Resource's id
     * @return \stdClass|null
     */
    public static function getById($id)
    {
        $record = Database::executeAndFetch(
            'SELECT * FROM `test`
            WHERE `id` = ?
            LIMIT 1',
            [$id],
            self::getCast()
        );

        return self::resource($record);
    }
}
