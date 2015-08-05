<?php

namespace Inck\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Inck\UserBundle\Entity\User;

/**
 * Transaction
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Inck\PaymentBundle\Entity\TransactionRepository")
 */
class Transaction
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
     * @var string
     *
     * @ORM\Column(name="transaction_id", type="string", length=255)
     */
    private $transactionId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\Inck\UserBundle\Entity\User", cascade={"persist"})
     */
    private $buyer;

    /**
     * @var float
     *
     * @ORM\Column(name="amount_net_of_tax", type="float")
     */
    private $amountNetOfTax;

    /**
     * @var integer
     *
     * @ORM\Column(name="value", type="integer")
     */
    private $value;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
    * @ORM\Column(name="subject", type="string", columnDefinition="enum('credits', 'membership')")
    */
    private $subject;

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
     * Set transactionId
     *
     * @param string $transactionId
     *
     * @return Transaction
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * Get transactionId
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * Set amountNetOfTax
     *
     * @param float $amountNetOfTax
     *
     * @return Transaction
     */
    public function setAmountNetOfTax($amountNetOfTax)
    {
        $this->amountNetOfTax = $amountNetOfTax;

        return $this;
    }

    /**
     * Get amountNetOfTax
     *
     * @return float
     */
    public function getAmountNetOfTax()
    {
        return $this->amountNetOfTax;
    }

    /**
     * Set value
     *
     * @param integer $value
     *
     * @return Transaction
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Transaction
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
     * Set subject
     *
     * @param string $subject
     *
     * @return Transaction
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set buyer
     *
     * @param \Inck\UserBundle\Entity\User $buyer
     *
     * @return Transaction
     */
    public function setBuyer(\Inck\UserBundle\Entity\User $buyer = null)
    {
        $this->buyer = $buyer;

        return $this;
    }

    /**
     * Get buyer
     *
     * @return \Inck\UserBundle\Entity\User
     */
    public function getBuyer()
    {
        return $this->buyer;
    }
}
