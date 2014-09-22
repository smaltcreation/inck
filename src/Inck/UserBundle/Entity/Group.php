<?php

namespace Inck\UserBundle\Entity;

use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_group")
 * @ORM\Entity(repositoryClass="Inck\UserBundle\Entity\GroupRepository")
 */
class Group extends BaseGroup
{
    const TYPE_DEFAULT = 0;
    const TYPE_COMPANY = 1;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\Email()
     * @ORM\Column(name="email", type="string")
     */
    protected $email;

    /**
     * @var int
     *
     * @ORM\Column(name="credit", type="integer")
     */
    protected $credit;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer")
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    protected $address;

    /**
     * @var string
     *
     * @ORM\Column(name="zipcode", type="string", length=255, nullable=true)
     */
    protected $zipcode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    protected $country;

    /**
     * @var string
     *
     * @Assert\Length(max="255")
     * @Assert\Url()
     * @ORM\Column(name="website", type="text", nullable=true)
     */
    protected $website;

    /**
     * @var int
     *
     * @ORM\Column(name="siret", type="integer", nullable=true)
     */
    protected $siret;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="private", type="boolean")
     */
    protected $private;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;

    /**
     * @ORM\OneToMany(targetEntity="Group", mappedBy="parent")
     **/
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    protected $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Inck\BidBundle\Entity\Bid", mappedBy="group")
     */
    protected $bids;


    public function __construct()
    {
        parent::__construct();

        $this->type             = TYPE_DEFAULT;
        $this->credit           = 0;
        $this->country          = 'FR';
        $this->private          = true;
        $this->enabled          = false;
        $this->firstLogin       = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

        $this->children         = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Set name
     *
     * @param string $name
     * @return Group
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Group
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
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
     * Set credit
     *
     * @param integer $credit
     * @return Group
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return integer 
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Group
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Group
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set zipcode
     *
     * @param string $zipcode
     * @return Group
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode
     *
     * @return string 
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Group
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Group
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return Group
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set siret
     *
     * @param integer $siret
     * @return Group
     */
    public function setSiret($siret)
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * Get siret
     *
     * @return integer 
     */
    public function getSiret()
    {
        return $this->siret;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Group
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set private
     *
     * @param boolean $private
     * @return Group
     */
    public function setPrivate($private)
    {
        $this->private = $private;

        return $this;
    }

    /**
     * Get private
     *
     * @return boolean 
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Group
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Add children
     *
     * @param \Inck\UserBundle\Entity\Group $children
     * @return Group
     */
    public function addChild(\Inck\UserBundle\Entity\Group $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Inck\UserBundle\Entity\Group $children
     */
    public function removeChild(\Inck\UserBundle\Entity\Group $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \Inck\UserBundle\Entity\Group $parent
     * @return Group
     */
    public function setParent(\Inck\UserBundle\Entity\Group $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Inck\UserBundle\Entity\Group 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add bids
     *
     * @param \Inck\BidBundle\Entity\Bid $bids
     * @return Group
     */
    public function addBid(\Inck\BidBundle\Entity\Bid $bids)
    {
        $this->bids[] = $bids;

        return $this;
    }

    /**
     * Remove bids
     *
     * @param \Inck\BidBundle\Entity\Bid $bids
     */
    public function removeBid(\Inck\BidBundle\Entity\Bid $bids)
    {
        $this->bids->removeElement($bids);
    }

    /**
     * Get bids
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBids()
    {
        return $this->bids;
    }
}
