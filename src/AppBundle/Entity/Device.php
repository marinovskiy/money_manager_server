<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Device
 *
 * @ORM\Table(name="device")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DeviceRepository")
 */
class Device
{
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
     *
     * @ORM\Column(name="udid", type="string", length=255, unique=true)
     */
    private $udid;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="apiKey", type="string", length=255, nullable=true)
     */
    private $apiKey;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

//   ORM\Column(name="user", type="object")
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User" inversedBy="devices")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $user;


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
     * Set udid
     *
     * @param string $udid
     *
     * @return Device
     */
    public function setUdid($udid)
    {
        $this->udid = $udid;

        return $this;
    }

    /**
     * Get udid
     *
     * @return string
     */
    public function getUdid()
    {
        return $this->udid;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Device
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set apiKey
     *
     * @param string $apiKey
     *
     * @return Device
     */
    public function setApiKey($apiKey = null)
    {
        if ($apiKey) {
            $this->apiKey = $apiKey;
        } else {
            $this->apiKey = bin2hex(random_bytes(16));
        }

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Device
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

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
     * Set user
     *
     * @param \stdClass $user
     *
     * @return Device
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
        if ($user) {
            $user->addDevice($this);
        }

        return $this;
    }
    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}