<?php
/**
 * Interface for ID management
 */

interface IdUtilities {

    /**
     * Connect to the user database.
     * @return mixed
     */
    public function connect();

    /**
     * Return true if the given username exists in the database.
     * @param $username
     * @return bool
     */
    public function username_exists($username);

    /**
     * Get the password for the given username.
     * @param $username
     * @return mixed
     */
    public function get_password($username);

    /**
     * Add a new user to the database.
     * @param $username
     * @param $password
     * @return mixed
     */
    public function add_user($username, $password);
} 