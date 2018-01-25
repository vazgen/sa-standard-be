<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\Column(type="string", length=32, unique=true, nullable=true)
     */
    private $apiKey;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Set apiKey.
     *
     * @param string|null $apiKey
     *
     * @return User
     */
    public function setApiKey($apiKey = null)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey.
     *
     * @return string|null
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function generateApiKey()
    {
        $this->apiKey = md5(random_bytes(128));

        return $this->apiKey;
    }

    public function setEmail($email){
        parent::setEmail($email);

        $this->username = $this->email;

        return $this;
    }
}
