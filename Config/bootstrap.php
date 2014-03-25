<?php
App::uses('AclRouter', 'Acl.Lib');

/* ------------------------------------------------------------------- The
 * settings below have to be loaded to make the acl plugin work.
 * ------------------------------------------------------------------- See how
 * to include these settings in the README file */

/*
 * The model name used in the database for the user role (typically 'Roles' or 'Groups')
 */
Configure::write('acl.aro.role.model', 'Role');

/* The primary key of the role model (can be left empty if your primary key's
 * name follows CakePHP conventions)('id') */
Configure::write('acl.aro.role.primary_key', '');

/* The foreign key's name for the roles (can be left empty if your foreign key's
 * name follows CakePHP conventions)(e.g. 'role_id') */
Configure::write('acl.aro.role.foreign_key', '');

/* The model name used for the user (typically 'User') */
Configure::write('acl.aro.user.model', 'User');

/* The primary key of the user model (can be left empty if your primary key's
 * name follows CakePHP conventions)('id') */
Configure::write('acl.aro.user.primary_key', '');

/* The name of the database field that can be used to display the role name */
Configure::write('acl.aro.role.display_field', 'name' );

/* You can add here role id(s) that are always allowed to access the ACL plugin
 * (by bypassing the ACL check) (This may prevent a user from being rejected
 * from the ACL plugin after a ACL permission update) */
Configure::write('acl.role.access_plugin_role_ids', array(1));

/* You can add here users id(s) that are always allowed to access the ACL plugin
 * (by bypassing the ACL check) (This may prevent a user from being rejected
 * from the ACL plugin after a ACL permission update) */
Configure::write('acl.role.access_plugin_user_ids', array(
    1
));

/* The users table field used as username in the views It may be a table field
 * or a SQL expression such as "CONCAT(User.lastname, ' ', User.firstname)" for
 * MySQL or "User.lastname||' '||User.firstname" for PostgreSQL */
Configure::write('acl.user.display_name', "email");

/* Indicates whether the presence of the Acl behavior in the user and role
 * models must be verified when the ACL plugin is accessed */
Configure::write('acl.check_act_as_requester', true);

/* Add the ACL plugin 'locale' folder to your application locales' folders */
App::build(array(
    'locales' => App::pluginPath('Acl') . DS . 'locale'
));

/* Indicates whether the roles permissions page must load through Ajax */
Configure::write('acl.gui.roles_permissions.ajax', false);

/* Indicates whether the users permissions page must load through Ajax */
Configure::write('acl.gui.users_permissions.ajax', false);

/**
 * Cascading Permission
 * You can use cascading permissions if each role in your role tier inherits all
 * of the permissions from the role beneath it.
 * Example
 * 1. Users have a set of permissions,
 * 2. Managers have all of the permissions of Users, plus additional permissions
 * 3. Admins have all of the permissions.
 *
 * The advantage of cascading permissions is that you can grant a permission
 * setting to Users and it will also add that permission for Managers and
 * Admins.
 *
 * If you need distinct permissions for your roles, do not use cascading permissions.
 */
Configure::write('acl.cascading_permissions', false);


?>