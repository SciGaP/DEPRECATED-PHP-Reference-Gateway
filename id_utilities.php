<?php
/**
 * Interface for ID management
 */

interface IdUtilities
{
    /**
     * Connect to the user database.
     * @return mixed|void
     */
    public function connect();

    /**
     * Return true if the given username exists in the database.
     * @param $username
     * @return bool
     */
    public function username_exists($username);

    /**
     * Authenticate user given username and password.
     * @param $username
     * @param $password
     * @return int|mixed
     */
    public function authenticate($username, $password);

    /**
     * Add a new user to the database.
     * @param $username
     * @param $password
     * @return mixed|void
     */
    public function add_user($username, $password);
} 