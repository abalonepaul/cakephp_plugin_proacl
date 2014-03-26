<?php
/**
 * UserFixture
 *
 */
class UserFixture extends CakeTestFixture {

    public $useDbConfig = 'test';
/**
 * Fields
 *
 * @var array
 */
    public $fields = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
        'role_id' => array('type' => 'integer', 'null' => false, 'default' => '3', 'length' => 4, 'key' => 'index'),
        'first_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 32, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
        'last_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 32, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
        'email' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
        'password' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 64, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
        'deleted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
                'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'role_id' => array('column' => 'role_id', 'unique' => 0)
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
            'role_id' => 1,
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@test.loc',
            'password' => '4d15903d0e36110fda32bf027b33c76c1bfc2d90',
            'deleted' => 0,
            'created' => '2014-03-01 09:13:11',
                    ),
        array(
            'id' => 2,
            'role_id' => 2,
            'first_name' => 'Manager',
            'last_name' => 'Manager',
            'email' => 'manager@test.loc',
            'password' => '4d15903d0e36110fda32bf027b33c76c1bfc2d90',
            'deleted' => 0,
            'created' => '2014-03-01 09:13:11',
                    ),
        array(
            'id' => 3,
            'role_id' => 3,
            'first_name' => 'User',
            'last_name' => 'User',
            'email' => 'user@test.loc',
            'password' => '4d15903d0e36110fda32bf027b33c76c1bfc2d90',
            'deleted' => 0,
            'created' => '2014-03-01 09:13:11',
                    ),
    );

}
