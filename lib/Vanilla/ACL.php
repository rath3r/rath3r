<?php
/**
 * Access Control List for Vanilla framework
 * The class controlls access for both pages and file routes using users_type_id property assigned to them.
 * If user has users_type_id greater than the one set for page or route, they won't be able to access the page,
 * and will be automatically redirected to the login page
 * Otherwise the function returns true, and the controller can be called
 *
 * @name     Vanilla_ACL
 * @category ACL
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

/**
 * Class comments
 * 
 * @name     Vanilla_ACL
 * @category ACL
 * @package  Vanilla
 * @author   Kasia Gogolek <kasia.gogolek@living-group.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://192.168.50.14/vanilla-doc/
 */

class Vanilla_ACL
{
    /**
     *
     * User Model returned regardless of user being logged in or not
     * @var Users_Model_User
     */
    public $user;

    /**
     * Recognisable code for 202 response (202 Accepted).
     * This will be then checked on login
     * to check if the request returned 202 to show a update page password
     * The request has been accepted for processing, 
     * but the processing has not been completed.
     */
    const RESPONSE_202     = '202';  

    /**
     * Recognisable code for 401 response (auth required).
     * This will be then checked throughout the application,
     * to check if the request returned 401
     */
    const RESPONSE_401     = '401'; // needs authorisation

    /**
     * Recognisable code for 404 response (page doesn't exist).
     * This will be then checked throughout the application,
     * to check if the request returned 404
     */
    const RESPONSE_404     = '404'; // page doesn't exist

    /**
     * Check if user can access page
     * 
     * @param Pages_Model_Page $page Page that we're checking the access for
     * 
     * @return boolean
     */
    public static function hasPageAccess($page)
    {
        $acl        = new self();
        $permission = $acl->hasPermission($page);
        if ($permission === true) {
            return true;
        }
        return false;
    }

    /**
     * Checking if user has got permission to view this page
     * If the pages or routes $users_type_id is lower than logged in users
     * users_type_id, return 401 response, and ask the users to log in
     * This will throw an exception in Router and redirect them to
     * the correct Action / Controller
     * 
     * @param Pages_Model_Page $page   Page passed
     * @param string             $module Module name
     * 
     * @return mixed
     */

    public function hasPermission($page, $module = null)
    {
        if ($this->user == null) {
            $this->getLoggedUser();
        }
        // If the page is private and the user is not live
       
        if($this->user instanceof Model_User && ($this->user->password_change_required == true))
        {
            return self::RESPONSE_202;
        }

        // Start off false
        $has_access = false;
            
        // If the user can view the page hasAccess becomes true
        $has_access = $has_access || self::hasEntityPermission($this->user, $page);
        if($page && Vanilla_Module::isInstalled("Dashboard"))
        {
            $page->getRelated();
            if (isset($page->relations)) 
            {
                foreach($page->relations as $relation)
                {
                    // If the user can view any of the related entities - the user will be able to view the page.
                    $result = self::hasEntityPermissionForIds($this->user, $relation->id, $relation->entity_id);
		    $has_access = $has_access || $result;
                }
            }
        }
        return $has_access ? true : self::RESPONSE_401;
        /** } 

        return true;*/
    }
    
    /**
     * Check any entitie's permission here
     * 
     * @param Users_Model_User  $user   User Object we're checking against
     * @param Vanilla_Model_Row $entity Entity Object
     * 
     * @todo  Implement a better way to get the user permissions when there is no database attached to the site
     *         Here I have used a check for the user's module but it looks nasty to me.
     *         I assume affectively there are no permissions now. Thomas
     * 
     * @static
     * 
     * @return boolean
     */
    public static function hasEntityPermission($user, $entity, $navigation = false)
    {
        if($user instanceof Users_Model_User)
        {
            if($user->isAdmin() && $navigation === false)
            {
                return true;
            }
            else 
            {
                $user->_getUserGroupIdsArray();
                $groups = $user->users_group_ids;
            }
        }
        else 
        {
            $groups = array(3);
        }
        
        if(Vanilla_Module::isInstalled("Users"))
        {
            $entity->getUserPermissionsGroups();
            
            $permissions_merge = array_intersect($entity->users_permissions, $groups);
            // (!empty($groups) && !empty($entity->users_permissions)) || 
            if(empty($permissions_merge))
            {
                return false;
            }
            
        }
        
        return true;
        
    }
    
    /**
     * Checking Entity permissions when can't pass an object
     * 
     * @param Users_Model_User $user           User Object
     * @param int              $entity_id      Entity ID
     * @param int              $entity_type_id Entity Type ID
     * 
     * @static
     * 
     * @return boolean
     */
    public static function hasEntityPermissionForIds($user, $entity_id, $entity_type_id)
    {
        if($user->isAdmin())
        {
            return true;
        }
        
        $permission = new Users_Model_Permissions();
        return $permission->hasEntityPermission($user, $entity_id, $entity_type_id);
    }


    /**
     * Getting the logged user from session
     * or logging them in from Data Source
     * This is run in every Controller
     * 
     * @return Users_Model_User
     */

    public function getLoggedUser()
    {
        if(!class_exists("Model_User"))
        {
            return null;
        }
        $user_object = new Model_User();
        $user        = $user_object->getSession();
        if ((empty($user) || $user->id == 0) && !empty($_POST))
        {
            $user = $this->_loginUser();
        }

        if ($user === null) {
            $user = $user_object;
        }
        $user->getGroupPermissions();

        $this->user = $user;
        return $user;
    }
    
    /**
     * This is a general login functionality if needs to be used
     * This should be redundant, as every controller should run
     * getLoggedUser automatically
     * 
     * @return boolean
     */
    
    public function login()
    {
        $this->getLoggedUser();
        //login success
        if (null !== $this->user && $this->user->isLoggedIn()) {
            return true;
        }

        //login fail
        return false;

    }

    /**
     * Functionality behind login in
     * Please note the Labels that are checked in POST request,
     * if they changed in your views, please make sure your Model
     * reflects those changes. Otherwise the user won't be able to log in.
     * 
     * @return mixed
     */

    private function _loginUser()
    {
        if(!empty($_POST))
        {
            if(!empty($_POST[Model_User::PASSWORD_LABEL]) && !empty($_POST[Model_User::USERNAME_LABEL]))
            {
                $user = new Model_User();
                $user->login($_POST[Model_User::USERNAME_LABEL], $_POST[Model_User::PASSWORD_LABEL]);
                if($user->isLoggedIn())
                {
                    return $user;
                }
                return null;
            }
        }
        return null;
    }

    /**
     * Logout the user.
     * This uses Users_Model_User logout function
     * Check it for the actual functionality.
     *
     * @return boolean
     */

    public function logout()
    {
        $this->getLoggedUser();
        if ($this->user->isLoggedIn()) {
            $this->user->logout();
            return true;
        }
        return false;
    }

}
