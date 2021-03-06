# CakePHP ProAcl Plugin
The ProAcl Plugin is an admin interface for managing the CakePHP Access Control Lists system and a helper for displaying links based on ACL permissions. The Admin iThis plugin is an an updated version of the [Alaxos ACL Plugin](http://www.alaxos.net/blaxos/pages/view/plugin_acl) by Nicolas Rod. The plugin has been refactored and updated to work with CakePHP 2.3+. The UI now uses Twitter Bootstrap.

## Requirements
CakePHP 2.2+

PHP 5.3+

A CakePHP application with User object, a Role/Group object and an Actions based Auth setup.

## ACL Setup
Before you can use the ProAcl Plugin, you will need to set up and configure your application to use CakePHP ACLs. Complete instructions for setting up ACL can be found here:

http://book.cakephp.org/2.0/en/core-libraries/components/access-control-lists.html

In short, You will need to initialize the database with the ACL tables, add the Acl Component to your AppController, and setup your bindNode methods for your ACL Requesters.

If you haven’t initialized your database, you can use the following Cake Shell command:

```
cake schema create DbAcl;
```

In order for the plugin to properly create and update ARO records, the ACL Behavior will need to be added to each model that will be requesting permissions. This is typically done on your User and Role/Group models. Instructions for configuring the ACL Behavior can be found here:

http://book.cakephp.org/2.0/en/core-libraries/behaviors/acl.html

## Plugin Installation
The ProAcl Plugin can be installed as a git submodule, using Composer, or downloaded.

### Submodule
Clone/Copy the files in this directory into {APP}/Plugin/Acl

```
git submodule add https://github.com/abalonepaul/cakephp_plugin_proacl.git app/Plugin/Acl
```

### Using Composer
```javascript
{
    "require": {
        "cakephp/pro_acl”: “1.*”
    }
}
```

### Download
Download the plugin and copy the files to {APP}/Plugin/Acl.

## Configuration
Ensure the plugin is loaded in {APP}/Config/bootstrap.php by calling

```php
CakePlugin::load(‘Acl’, array(‘bootstrap’ => true, ‘routes’ => true));
```
Include the components and helper in your {APP}/Controller/AppController.php:
```php
class AppController extends Controller {
         public $components = array(’Acl.Manager’,’Acl.Reflector’);
         public $helpers = array(’Acl.AclHtml’);
}
```
Edit the {APP}/Plugin/Acl/Config/bootstrap.php for your application. The configuration settings are documented in that file.

If you are using user-based permissions add the following to your User model. 
```php
public $actsAs = array('Acl' => array('type' => 'requester'));

```

If methods in your User controller also need to be controlled add this instead:

```php
public $actsAs = array('Acl' => array('type' => ‘both’));

```

Now add the following callback method to your User model:
```php
    public function parentNode() {
        if (!$this->id && empty($this->data)) {
            return null;
        }
        $data = $this->data;
        if (empty($this->data)) {
            $data = $this->read();
        }
        if (empty($data['User’][‘group_id'])) {
            return null;
        } else {
            return array(‘Group' => array('id' => $data['User’][‘group_id']));
        }
    }

```

If want to use group-based permissions additionally add this to your User model:
```php
public function bindNode($user) {
return array('model' => 'Group', 'foreign_key' => $user['User']['group_id']);
}
```
and add this to your Group model:
```php
    public $actsAs = array('Acl' => array('type' => 'requester'));

    public function parentNode() {
        return null;
    }
```

The ProAcl plugin uses an ‘admin’ prefix. You may need to configure your application and routes to use properly access interface.


## Session Stored Permissions
The Session Stored Permissions feature will store a user’s permissions in their session. This provides performance improvements when checking permissions manually. To use this feature, add the following to your AppController in the beforeFilter method:

```php
    $this->AclManager->setSessionPermissions();
```

You can read permissions using:
```php
        $permissions = $this->Session->read('ProAcl.permissions');
```
## Usage
To begin using the Plugin navigate to {your domain}/admin/acl/acl/acos.

### ACOs
The ACOs are the controllers and actions your users will need permissions to access.

#### Build Actions ACOs
This function will build the ACO table from scratch.

#### Clear Actions ACOs
This function will remove all of the ACOs from the database.

#### Prune Actions ACOs
This function will remove ACOs for actions you have removed.

#### Synchronize Actions ACOs
This function will check for obsolete and new actions. New actions are added and obsolete actions are removed.

### AROs
The AROs are the Users that are requesting permissions to access an ACO.

#### Build Missing AROs
This function will check for AROs that are not in the system. If Missing AROs are found, they are added to the ARO table.

#### Users Roles
This function will let you change the Role or Group that your users belong to. You can search for users and then click the appropriate Role or Group for that User.

#### Roles Permissions
This function allows you to view the actions and the Roles assigned to them. You can click a role to grant or deny permissions for each action. At the top of the page, you can also grant all of the permissions to a particular Role.

#### Users Permissions
This function allows you to view individual permissions assigned to a user. User based permissions will override Role-based permissions.

### AclHtml Helper
The AclHtml Helper contains a method that will conditionally output a link to a controller action only if the User has permissions to access that action. You can output a link in your views like this:

```php
echo $this->AclHtml->link(‘Edit User’, ‘/users/edit/1’);
```

The link would only by rendered if the logged in user has permissions to access the edit method in the UsersController.

## Changelog

### 1.0 Initial Commit
Refactored the plugin. Added support for the ‘both’ ACL Behavior requester key. Added Unit Tests.

## What’s Coming
In the near future, we plan to add the following features:

* Add a database setup function that will create your ACL tables for you with either incremental ids or uuids.

* Add support for Cascading Permissions with a role map.

* Add support for Custom Role ordering.

* Add an Ownable Behavior to limit access to a User’s own records.

* Add support for CRUD based permissions.
