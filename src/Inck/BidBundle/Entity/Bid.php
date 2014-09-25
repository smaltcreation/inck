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
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false)
     */
    private $group;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="bids")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
     */
    private $product;


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

    /**
     * Set product
     *
     * @param \Inck\BidBundle\Entity\Product $product
     * @return Bid
     */
    public function setProduct(\Inck\BidBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Inck\BidBundle\Entity\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }
}
