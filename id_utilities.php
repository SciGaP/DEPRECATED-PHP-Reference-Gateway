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
     * Create new user
     *
     * @param $username
     * @param $password
     * @param $first_name
     * @param $last_name
     * @param $email
     * @param $organization
     * @param $address
     * @param $country
     * @param $telephone
     * @param $mobile
     * @param $im
     * @param $url
     * @return mixed
     */
    public function add_user($username, $password, $first_name, $last_name, $email, $organization,
            $address, $country,$telephone, $mobile, $im, $url);

    /**
     * Get the user profile
     * @param $username
     * @return mixed|void
     */
    public function get_user_profile($username);

    /**
     * Update the user profile
     *
     * @param $username
     * @param $first_name
     * @param $last_name
     * @param $email
     * @param $organization
     * @param $address
     * @param $country
     * @param $telephone
     * @param $mobile
     * @param $im
     * @param $url
     * @return mixed
     */
    public function update_user_profile($username, $first_name, $last_name, $email, $organization, $address,
        $country, $telephone, $mobile, $im, $url);

    /**
     * Function to update user password
     *
     * @param $username
     * @param $current_password
     * @param $new_password
     * @return mixed
     */
    public function change_password($username, $current_password, $new_password);
} 