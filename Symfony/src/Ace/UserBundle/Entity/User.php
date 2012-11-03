<?php
// src/Ace/UserBundle/Entity/User.php

namespace Ace\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
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
	 * @ORM\Column(type="string", length="255")
	 *
	 * @Assert\NotBlank(message="Please enter your first name.", groups={"Registration", "Profile"})
	 * @Assert\MinLength(limit="3", message="The first name is too short.", groups={"Registration", "Profile"})
	 * @Assert\MaxLength(limit="255", message="The first name is too long.", groups={"Registration", "Profile"})
	 */
    private $firstname;

    /**
	 * @ORM\Column(type="string", length="255")
	 *
	 * @Assert\NotBlank(message="Please enter your last name.", groups={"Registration", "Profile"})
	 * @Assert\MinLength(limit="3", message="The  last name is too short.", groups={"Registration", "Profile"})
	 * @Assert\MaxLength(limit="255", message="The last name is too long.", groups={"Registration", "Profile"})
	 */
    private $lastname;

    /**
	 * @ORM\Column(type="string", length="255", nullable=true)
     */
    private $twitter;

    /**
     * Set firstname
     *
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }
	
    /**
     * Set twitter
     *
     * @param string $twitter
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;
    }

    /**
     * Get twitter
     *
     * @return string 
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
