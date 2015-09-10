<?php

namespace Inck\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Badge
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Badge
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=7, nullable=true)
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column(name="colorText", type="string", length=7, nullable=true)
     */
    private $colorText;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */

    private $createdAt;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="badges")
     * @ORM\JoinTable(name="badges_users")
     **/
    private $users;

    /**
     * @var string
     *
     * @ORM\Column(name="icone", type="text", nullable=false)
     */
    private $icone;

    /**
     * @var integer
     *
     * @ORM\Column(name="lvl", type="integer", options={"default" = 0})
     */
    private $lvl;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Badge
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return Badge
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Badge
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Badge
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
     * Add user
     *
     * @param \Inck\UserBundle\Entity\User $user
     *
     * @return Badge
     */
    public function addUser(\Inck\UserBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \Inck\UserBundle\Entity\User $user
     */
    public function removeUser(\Inck\UserBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set icone
     *
     * @param string $icone
     *
     * @return Badge
     */
    public function setIcone($icone)
    {
        $this->icone = $icone;

        return $this;
    }

    /**
     * Get icone
     *
     * @return string
     */
    public function getIcone()
    {
        return $this->icone;
    }

    /**
     * Set colorText
     *
     * @param string $colorText
     *
     * @return Badge
     */
    public function setColorText($colorText)
    {
        $this->colorText = $colorText;

        return $this;
    }

    /**
     * Get colorText
     *
     * @return string
     */
    public function getColorText()
    {
        return $this->colorText;
    }

    /**
     * Set lvl
     *
     * @param integer $lvl
     *
     * @return Badge
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }
}
