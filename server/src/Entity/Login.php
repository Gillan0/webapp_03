<?php

namespace App\Entity;

interface Login 
{

    /**
     * Creates a new user
     * 
     * @param string $username
     * @param string $password
     * @param string $email
     * @return User 
     */
    public function createUser(string $username,
                                string $password,
                                string $email) : User;

    /**
     * Handles login
     * 
     * @param string $username
     * @param string $password
     * @return User
     */
    public function login(string $username, string $password) : User;

    /**
     * Checks that username and password are valid (ie length, type of characters)
     */
    public function isValidCredentials(string $username,
                                string $password) : bool;

}

?>