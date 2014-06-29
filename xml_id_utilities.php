<?php
/**
 * Utilities for ID management with an XML file
 */

include 'id_utilities.php';

class XmlIdUtilities implements IdUtilities
{
    const DB_PATH = 'users.xml';

    /**
     * Connect to the user database.
     * @return mixed|void
     */
    public function connect()
    {
        global $db;


        try
        {
            if (file_exists(self::DB_PATH))
            {
                $db = simplexml_load_file(self::DB_PATH);
            }
            else
            {
                throw new Exception("Error: Cannot connect to database!");
            }


            if (!$db)
            {
                throw new Exception('Error: Cannot open database!');
            }
        }
        catch (Exception $e)
        {
            echo '<div>' . $e->getMessage() . '</div>';
        }
    }

    /**
     * Return true if the given username exists in the database.
     * @param $username
     * @return bool
     */
    public function username_exists($username)
    {
        global $db;

        foreach($db->xpath('//username') as $db_username)
        {
            if ($db_username == $username)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Authenticate the user given username and password.
     * @param $username
     * @return int|mixed
     */
    public function authenticate($username, $password)
    {
        global $db;

        $hashed_password = md5($password);
        
        $user = $db->xpath('//user[username="' . $username . '"]');

        if (sizeof($user) == 1)
        {
            return $user[0]->password_hash == $hashed_password;
        }
        elseif(sizeof($user) == 0)
        {
            return -1;
        }
        else // duplicate users in database
        {
            return -2;
        }
    }

    /**
     * Add a new user to the database.
     * @param $username
     * @param $password
     * @return mixed|void
     */
    public function add_user($username, $password, $first_name, $last_name, $email, $organization,
            $address, $country,$telephone, $mobile, $im, $url)
    {
        global $db;

        $users = $db->xpath('//users');

        $user = $users[0]->addChild('user');

        $user->addChild('username', $username);
        $user->addChild('password_hash', md5($password));

        //Format XML to save indented tree rather than one line
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($db->asXML());
        $dom->save('users.xml');
    }

    /**
     * Get the user profile
     * @param $username
     * @return mixed|void
     */
    public function get_user_profile($username)
    {
        // TODO: Implement get_user_profile() method.
    }

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
                                        $country, $telephone, $mobile, $im, $url)
    {
        // TODO: Implement update_user_profile() method.
    }

    /**
     * Function to update user password
     *
     * @param $username
     * @param $current_password
     * @param $new_password
     * @return mixed
     */
    public function change_password($username, $current_password, $new_password)
    {
        // TODO: Implement change_password() method.
    }
}