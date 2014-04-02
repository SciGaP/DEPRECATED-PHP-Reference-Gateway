<?php

/**
 * Connect to the user database.
 * @return SimpleXMLElement
 */
function connect_to_db()
{
    $db = simplexml_load_file("users.xml") or die("Error: Cannot open database");
    return $db;
}

/**
 * Return true if the given username exists in the database.
 * @param $username
 * @return bool
 */
function username_in_db($username)
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
 * Get the password for the given username.
 * @param $username
 * @return SimpleXMLElement[]|string
 */
function db_get_password($username)
{
    global $db;

    $user = $db->xpath("//user[username=\"" . $username . "\"]");

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
 * Add a new user to the database.
 * @param $username
 * @param $password
 */
function db_add_user($username,$password)
{
    global $db;

    $users = $db->xpath("//users");

    $user = $users[0]->addChild("user");

    $user->addChild("username", $username);
    $user->addChild("password_hash", md5($password));

    //Format XML to save indented tree rather than one line
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($db->asXML());
    $dom->save('users.xml');
}