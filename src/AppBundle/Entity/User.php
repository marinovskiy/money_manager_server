<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface, \JsonSerializable
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER = 'ROLE_USER';

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    const GENDER_TITLES = ['Чоловік' => self::GENDER_MALE, 'Жінка' => self::GENDER_FEMALE];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank(message="Email can not be empty")
     * @ORM\Column(name="email", type="string", length=100, unique=true)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="firstName", type="string", length=50)
     */
    private $firstName;

    /**
     * @var string
     * @ORM\Column(name="lastName", type="string", length=50)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=64)
     */
    private $password;

    /**
     * @var string
     * @Assert\NotBlank(message="Password can not be empty", groups={"Registration"})
     * @Assert\Regex(pattern="/^[a-zA-Z0-9_@%+’!#$^?:,(){}[\]~_-]{6,}$/", groups={"Registration"})
     */
    private $plainPassword;

    /**
     * @var Device[]
     *
     * @ORM\OneToMany(targetEntity="Device", mappedBy="user", cascade={"remove","persist"})
     */
    private $devices;

    private $apiKey;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=50)
     */
    private $role;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var string
     * @ORM\Column(name="gender", type="string", length=255)
     */
    private $gender;

    /**
     * @var ArrayCollection|$accounts[]
     *
     * @ORM\OneToMany(targetEntity="Account", mappedBy="user", cascade={"remove","persist"})
     */
    private $accounts;

    /**
     * @var ArrayCollection|$createdOrganizations[]
     *
     * @ORM\OneToMany(targetEntity="Organization", mappedBy="creator")
     */
    private $createdOrganizations;

    /**
     * @var Organization[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Organization", mappedBy="members")
     */
    private $organizations;

    /**
     * @var News[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="News", mappedBy="author")
     * @ORM\JoinColumn(name="news_id", referencedColumnName="id")
     */
    private $newses;

    public function __construct()
    {
        $this->devices = new ArrayCollection();
        $this->accounts = new ArrayCollection();
        $this->organizations = new ArrayCollection();
        $this->newses = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * Add devices
     *
     * @param Device $devices
     *
     * @return User
     */
    public function addDevice(Device $devices)
    {
        $this->devices[] = $devices;

        return $this;
    }

    /**
     * Remove devices
     *
     * @param Device $devices
     */
    public function removeDevice(Device $devices)
    {
        $this->devices->removeElement($devices);
    }

    /**
     * Get devices
     *
     * @return Device[]
     */
    public function getDevices()
    {
        return $this->devices;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return User
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Add account
     *
     * @param Account $account
     *
     * @return User
     */
    public function addAccount(Account $account)
    {
        $this->accounts->add($account);

        return $this;
    }

    /**
     * Remove account
     *
     * @param Account $account
     */
    public function removeAccount(Account $account)
    {
        $this->accounts->removeElement($account);
    }

    /**
     * Get accounts
     *
     * @return ArrayCollection|$accounts[]
     */
    public function getAccounts()
    {
        return $this->accounts;
    }

    /**
     * @param Account[] $accounts
     */
    public function setAccounts($accounts)
    {
        $this->accounts = $accounts;
    }

    /**
     * Add createdOrganization
     *
     * @param Organization $createdOrganization
     *
     * @return User
     */
    public function addCreatedOrganization(Organization $createdOrganization)
    {
        $this->createdOrganizations->add($createdOrganization);

        return $this;
    }

    /**
     * Remove createdOrganization
     *
     * @param Organization $createdOrganization
     */
    public function removeCreatedOrganization(Organization $createdOrganization)
    {
        $this->createdOrganizations->removeElement($createdOrganization);
    }

    /**
     * Get createdOrganizations
     *
     * @return Organization[]|ArrayCollection
     */
    public function getCreatedOrganizations()
    {
        return $this->createdOrganizations;
    }

    /**
     * @param Organization[] $createdOrganizations
     */
    public function setCreatedOrganizations($createdOrganizations)
    {
        $this->createdOrganizations = $createdOrganizations;
    }

    /**
     * Add organization
     *
     * @param Organization $organization
     *
     * @return User
     */
    public function addOrganization(Organization $organization)
    {
        $this->organizations->add($organization);

        return $this;
    }

    /**
     * Remove organization
     *
     * @param Organization $organization
     */
    public function removeOrganization(Organization $organization)
    {
        $this->organizations->removeElement($organization);
    }

    /**
     * Get organizations
     *
     * @return ArrayCollection|$organizations[]
     */
    public function getOrganizations()
    {
        return $this->organizations;
    }

    /**
     * @param Organization[] $organizations
     */
    public function setOrganizations($organizations)
    {
        $this->organizations = $organizations;
    }

    /**
     * Add news
     *
     * @param News $news
     *
     * @return User
     */
    public function addNews(News $news)
    {
        $this->newses->add($news);

        return $this;
    }

    /**
     * Remove news
     *
     * @param News $news
     */
    public function removeNews(News $news)
    {
        $this->newses->removeElement($news);
    }

    /**
     * Get newses
     *
     * @return News[]|ArrayCollection
     */
    public function getNewses()
    {
        return $this->newses;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
    }

    function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'apiKey' => $this->getApiKey(),
            'email' => $this->getEmail(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'gender' => $this->getGender(),
        ];
    }
}