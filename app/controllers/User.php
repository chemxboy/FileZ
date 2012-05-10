<?php

/**
 * @file
 * This file define the User controller that is used for user administration. 
 * 
 * Long description.
 * 
 * @package FileZ
 */

/**
 * Controller used for user administration
 */
class App_Controller_User extends Fz_Controller {

    public function init () {
        layout ('layout'.DIRECTORY_SEPARATOR.'admin.html.php');
    }

    /**
     * Action list users
     * List users.
     */
    public function indexAction () {
        $this->secure ('admin');
        set ('EditUserRight', (fz_config_get ('app', 'user_factory_class') === "Fz_User_Factory_Database"));
        set ('users', Fz_Db::getTable ('User')->findAll ()); // TODO paginate
        $diskUsage=array();
        foreach (Fz_Db::getTable ('User')->findAll () as $user_item) {
            $diskUsage[$user_item->id] = Fz_Db::getTable ('File')->getReadableTotalDiskSpaceByUser ($user_item);
        }
        set ('diskUsage', $diskUsage);
        return html ('user/index.php');
    }

    /**
     * Action called to display user details
     */
    public function showAction () {
        $this->secure ('admin');
        set ('EditUserRight', (fz_config_get ('app', 'user_factory_class') === "Fz_User_Factory_Database"));
        set ('user', Fz_Db::getTable ('User')->findById (params ('id')));
        return html ('user/show.php');
    }

    /**
     * Action called to post values of a new user.
     */
    public function postnewAction () {
        // TODO prevent CSRF

        $this->secure ('admin');
        $user = new App_Model_User ();
        $user->setUsername  ($_POST ['username']);
        $user->setPassword  ($_POST ['password']);
        $user->setFirstname ($_POST ['firstname']);
        $user->setLastname  ($_POST ['lastname']);
        $user->setIsAdmin   ($_POST ['is_admin'] == 'on');
        $user->setEmail     ($_POST ['email']);
        if( 0 === count( $user->isValid() ) ) {
            $user->save ();
            return redirect_to ('/admin/users');
        }
        else {
            $errors = '';
            foreach ($user->isValid() as $error) {
               $errors .= $error."<br />";
            }
               flash_now ('error', $errors);
            return $this->createAction ();
        }
    }

    /**
     * Action called to update values of an existing user.
     */
    public function updateAction () {
        // TODO prevent CSRF

        $this->secure ('admin');
        $user = Fz_Db::getTable ('User')->findById (params ('id'));
        $user->setUsername  ($_POST ['username']);
        if ( 0 < strlen($_POST['password']) ) {
          $user->setPassword  ($_POST ['password']);
        }
        $user->setFirstname ($_POST ['firstname']);
        $user->setLastname  ($_POST ['lastname']);
        $user->setIsAdmin   ($_POST ['is_admin'] == 'on');
        $user->setEmail     ($_POST ['email']);
        if( 0 === count( $user->isValid('update') ) ) {
            $user->save ();
            return redirect_to ('/admin/users');
        }
        else {
            $errors = '';
            foreach ($user->isValid('update') as $error) {
               $errors .= $error."<br />";
            }
               flash_now ('error', $errors);
            return $this->editAction ();
        }
    }

    /**
     * Action called to create a new user
     */
    public function createAction () {
        $this->secure ('admin');
        return html ('user/create.php');
    }

    /**
     * Action called to edit a user
     */
    public function editAction () {
        $this->secure ('admin');
        set ('user', Fz_Db::getTable ('User')->findById (params ('id')));
        return html ('user/edit.php');
    }

    /**
     * Action called to delete a user
     */
    public function deleteAction () {
        // TODO prevent CSRF

        $this->secure ('admin');
        $user = Fz_Db::getTable ('User')->findById (params ('id'));
        if($user) 
            $user->delete();

        return redirect_to ('/admin/users');
    }

    public function editQuotaAction() {

        $this->secure ('admin');
        $user = Fz_Db::getTable ('User')->findById ($_POST['idUser']);
        $error = false;
        $success = false;

        error_log('dans la fonction editQuota');
      if($this->shorthandSizeToBytes($_POST['quota'].'G') > $this->shorthandSizeToBytes($user->getDiskUsage()))
      {
         if(count($user) > 0)
       {
          $user->setQuota($_POST['quota'].'G');
          $user->save();
          $success = true;
       }
      }
      else
        $error = true;

     if ($this->isXhrRequest())
       return json (array ('success' => $success, 'error' => $error));
  }

    /**
     * Transform a size in the shorthand format ('K', 'M', 'G') to bytes
     *
     * @param string $size
     * @return integer
     */
    public function shorthandSizeToBytes ($size) {
        $size = str_replace (' ', '', $size);
        switch(strtolower($size[strlen($size)-1])) {
            case 'g': $size *= 1024;
            case 'm': $size *= 1024;
            case 'k': $size *= 1024;
        }
        return floatval ($size);
    }
}
