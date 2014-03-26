<?php
/**
 * RoleFixture
 *
 */
class RoleFixture extends CakeTestFixture {

    public $useDbConfig = 'test';
/**
 * Fields
 *
 * @var array
 */
    public $fields = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'key' => 'primary'),
        'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 16, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
        'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
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
    public $records = array(
        array(
            'id' => 1,
            'name' => 'Admin',
            'created' => '2014-03-01 09:13:11',
            'modified' => '2014-03-01 09:13:11'
        ),
        array(
            'id' => 2,
            'name' => 'Manager',
            'created' => '2014-03-01 09:13:11',
            'modified' => '2014-03-01 09:13:11'
        ),
        array(
            'id' => 3,
            'name' => 'User',
            'created' => '2014-03-01 09:13:11',
            'modified' => '2014-03-01 09:13:11'
        ),
    );

}
