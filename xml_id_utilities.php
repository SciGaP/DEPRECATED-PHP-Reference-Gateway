<?php
/**
 * Utilities for ID management with an XML file
 */

include 'id_utilities.php';

class XmlIdUtilities implements IdUtilities
{
    /**
     * @return mixed
     */
    public function connect()
    {
        global $db;

        $db = simplexml_load_file('users.xml') or die('Error: Cannot open database');
    }

    /**
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
     * @param $username
     * @return mixed
     */
    public function get_password($username)
    {
        global $db;

        $user = $db->xpath('//user[username="' . $username . '"]');

        if (sizeof($user) == 1)
        {
            return $user[0]->password_hash;
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
     * @param $username
     * @param $password
     * @return mixed
     */
    public function add_user($username, $password)
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
} 