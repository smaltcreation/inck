<?php

namespace Inck\BidBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Inck\BidBundle\Entity\ProductRepository")
 */
class Product
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
     * @var \DateTime
     *
     * @ORM\Column(name="start_at", type="datetime")
     */
    private $startAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_at", type="datetime")
     */
    private $endAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="min_price", type="integer")
     */
    private $minPrice;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Bid", mappedBy="product")
     */
    protected $bids;


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
     * Set startAt
     *
     * @param \DateTime $startAt
     * @return Product
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime 
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     * @return Product
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime 
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Product
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set minPrice
     *
     * @param integer $minPrice
     * @return Product
     */
    public function setMinPrice($minPrice)
    {
        $this->minPrice = $minPrice;

        return $this;
    }

    /**
     * Get minPrice
     *
     * @return integer 
     */
    public function getMinPrice()
    {
        return $this->minPrice;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bids = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add bids
     *
     * @param \Inck\BidBundle\Entity\Bid $bids
     * @return Product
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
