<?php
namespace App\Security;

use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

final class User implements JWTUserInterface
{
    private $username;
    private $roles;
    private $email; 
    
    public function __construct($username, array $roles, $email)
    {
        $this->username = $username;
        $this->roles = $roles;
        $this->email = $email;
    }
    
    public static function createFromPayload($username, array $payload)
    {   
        $email = isset($payload['email']) ? $payload['email'] : null;
        return new self(
            $username,
            $payload['roles'], 
            $email
        );
    }

     /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return string[] The user roles
     */
    public function getRoles(){
        return $this->roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string|null The encoded password if any
     */
    public function getPassword(){
        throw new Exception ("User.getPassword not implemented");
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt(){
        throw new Exception ("User.getSalt not implemented");
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername(){
        return $this->username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(){}

   /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getEmail(){
        return $this->email;
    }
}