<?php
namespace AppBundle\Entity;
use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
/**
 * Device
 *
 * @ORM\Table(name="device")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DeviceRepository")
 */
class Device
{
    const PLATFORM_ANDROID = 'android';
    const PLATFORM_WEB = 'web';
    const PLATFORM_TYPES = [
        self::PLATFORM_ANDROID,
        self::PLATFORM_WEB
    ];
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
     * @ORM\Column(name="platformType", type="string", length=255)
     */
    private $platformType;
    /**
     * @var string
     *
     * @ORM\Column(name="udid", type="string", length=255, unique=true)
     */
    private $udid;
    /**
     * @var string
     *
     * @ORM\Column(name="apiKey", type="string", length=255)
     */
    private $apiKey;
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="devices")
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
     * Set platformType
     *
     * @param string $platformType
     *
     * @return Device
     */
    public function setPlatformType($platformType)
    {
        $this->platformType = $platformType;
        return $this;
    }
    /**
     * Get platformType
     *
     * @return string
     */
    public function getPlatformType()
    {
        return $this->platformType;
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
//    public function setApiKey($apiKey)
//    {
//        $this->apiKey = $apiKey;
//
//        return $this;
//    }
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
     * Set user
     *
     * @param User $user
     * @return Device
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
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