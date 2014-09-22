<?php

namespace Inck\BidBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bid
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Inck\BidBundle\Entity\BidRepository")
 */
class Bid
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="integer")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="\Inck\UserBundle\Entity\Group", inversedBy="bids")
     */
    private $group;


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
     * Set price
     *
     * @param integer $price
     * @return Bid
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set group
     *
     * @param \Inck\UserBundle\Entity\Group $group
     * @return Bid
     */
    public function setGroup(\Inck\UserBundle\Entity\Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \Inck\UserBundle\Entity\Group 
     */
    public function getGroup()
    {
        return $this->group;
    }
}
