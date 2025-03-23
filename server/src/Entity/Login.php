<?php

namespace App\Entity;

/**
 * Interface which contains all methods required to handle login and account creation
 * 
 * @author Antonino Gillard <antonino.gillard@imt-atlantique.net>
 * @author Lucien Duhamel <lucien.duhamel@imt-atlantique.net>
 * 
 */
interface Login 
{

    /**
     * Creates a new user and returns the newly created {@link User} account
     * 
     * @param string $username
     * @param string $password
     * @param string $email
     * @throws \Exception invalid parameters
     * @return User Newly created account
     */
    public function createUser(string $username,
                                string $password,
                                string $email) : User;

    /**
     * Handles login and returns the {@link User} account if credentials are correct
     * 
     * @param string $username
     * @param string $password
     * @throws \Exception Wrong credentials or Illegal arguments
     * @return \App\Entity\User
     */
    public function login(string $username, string $password) : User;

    /**
     * Checks that username and password are valid (ie length, type of characters)
     * 
     * @param string $username
     * @param string $password
     * @param string $email
     * @return string error message, if correct returns empty character list
     */
    public function isValidCredentials(string $username,
                                        string $password,
                                        string $email) : ?string;

}

?>