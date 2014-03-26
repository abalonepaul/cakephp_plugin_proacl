<?php
/**
 * AcoFixture
 *
 */
class AcoFixture extends CakeTestFixture {
    public $useDbConfig = 'test';
/**
 * Fields
 *
 * @var array
 */
    public $fields = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
        'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
        'model' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
        'alias' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'lft' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
        'rght' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

/**
 * Records
 *
 * @var array
 */
    public $records = false;


}
