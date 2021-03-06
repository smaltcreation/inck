<?php

namespace Inck\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Inck\ArticleBundle\Entity\Article;
use Proxies\__CG__\Inck\ArticleBundle\Entity\Bookshelf;
use Serializable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="Inck\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser implements Serializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="first_login", type="datetime", nullable=true)
     */
    private $firstLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="occupation", type="string", length=255, nullable=true)
     */
    private $occupation;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="zipcode", type="string", length=255, nullable=true)
     */
    private $zipcode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="school", type="string", length=255, nullable=true)
     */
    private $school;

    /**
     * @var string
     *
     * @Assert\Length(max="500")
     * @ORM\Column(name="biography", type="text", nullable=true, length=500)
     */
    private $biography;

    /**
     * @var string
     *
     * @Assert\Length(max="255")
     * @Assert\Url()
     * @Assert\Regex(
     *     pattern="/https?:\/\/(www\.)?(facebook|fb)\.com\/.+/",
     *     match=true,
     *     message="Le nom de domaine doit être celui de Facebook"
     * )
     * @ORM\Column(name="facebook", type="text", nullable=true)
     */
    private $facebook;

    /**
     * @var string
     *
     * @Assert\Length(max="255")
     * @Assert\Regex(
     *     pattern="/https?:\/\/(www\.)?twitter\.com\/.+/",
     *     match=true,
     *     message="Le nom de domaine doit être celui de Twitter"
     * )
     * @Assert\Url()
     * @ORM\Column(name="twitter", type="text", nullable=true)
     */
    private $twitter;

    /**
     * @var string
     *
     * @Assert\Length(max="255")
     * @Assert\Url()
     * @Assert\Regex(
     *     pattern="/https?:\/\/(www\.)?plus\.google\.com\/.+/",
     *     match=true,
     *     message="Le nom de domaine doit être celui de Google Plus"
     * )
     * @ORM\Column(name="googlePlus", type="text", nullable=true)
     */
    private $googlePlus;

    /**
     * @var string
     *
     * @Assert\Length(max="255")
     * @Assert\Url()
     * @Assert\Regex(
     *     pattern="/https?:\/\/(www\.)?([a-z]{2}\.)?linkedin\.com\/.+/",
     *     match=true,
     *     message="Le nom de domaine doit être celui de Linkedin"
     * )
     * @ORM\Column(name="linkedIn", type="text", nullable=true)
     */
    private $linkedIn;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Inck\UserBundle\Entity\Group")
     * @ORM\JoinTable(
     *      name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * @ORM\OneToMany(targetEntity="\Inck\ArticleBundle\Entity\Bookshelf", mappedBy="user")
     */
    private $bookshelfs;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Inck\ArticleBundle\Entity\Article", mappedBy="author")
     */
    protected $articles;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Inck\ArticleBundle\Entity\Article")
     * @ORM\JoinTable(
     *      name="user_articleWatchLater",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")}
     * )
     */
    private $articlesWatchLater;

    /**
     * @ORM\Column(name="facebook_id", type="string", length=255, nullable=true)
     */
    private $facebook_id;

    /**
     * @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true)
     */
    private $facebook_access_token;

    /**
     * @ORM\Column(name="google_id", type="string", length=255, nullable=true)
     */
    private $google_id;

    /**
     * @ORM\Column(name="google_access_token", type="string", length=255, nullable=true)
     */
    private $google_access_token;

	/**
	 * @var ArrayCollection
	 *
	 * @ORM\OneToMany(targetEntity="Inck\SubscriptionBundle\Entity\Subscription", mappedBy="subscriber")
	 */
	private $subscriptions;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Inck\UserBundle\Entity\Activity", mappedBy="user")
     */
    protected $activities;

    /**
     * @ORM\ManyToMany(targetEntity="Inck\UserBundle\Entity\Badge", mappedBy="users")
     */
    private $badges;


    public function __construct()
    {
        parent::__construct();

        $this->country          = 'FR';
        $this->firstLogin       = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $this->subscriptions    = new ArrayCollection();
        $this->subscribers      = new ArrayCollection();
        $this->groups           = new ArrayCollection();
        $this->badges           = new ArrayCollection();
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id,
            $this->email,
            $this->firstname,
            $this->lastname,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id,
            $this->email,
            $this->firstname,
            $this->lastname,
        ) = array_merge(unserialize($serialized), array_fill(0, 2, null));
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
     * Set firstLogin
     *
     * @param \DateTime $firstLogin
     * @return User
     */
    public function setFirstLogin($firstLogin)
    {
        $this->firstLogin = $firstLogin;

        return $this;
    }

    /**
     * Get firstLogin
     *
     * @return \DateTime 
     */
    public function getFirstLogin()
    {
        return $this->firstLogin;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
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
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
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
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->firstname && $this->lastname ? $this->firstname.' '.$this->lastname : $this->username;
    }

    /**
     * Set birthday
     *
     * @param \DateTime birthday
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set occupation
     *
     * @param string $occupation
     * @return User
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;

        return $this;
    }

    /**
     * Get occupation
     *
     * @return string 
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * Set school
     *
     * @param string $school
     * @return User
     */
    public function setSchool($school)
    {
        $this->school = $school;

        return $this;
    }

    /**
     * Get school
     *
     * @return string 
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * Set biography
     *
     * @param string $biography
     * @return User
     */
    public function setBiography($biography)
    {
        $this->biography = $biography;

        return $this;
    }

    /**
     * Get biography
     *
     * @return string 
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * Set facebook
     *
     * @param string $facebook
     * @return User
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return string 
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set twitter
     *
     * @param string $twitter
     * @return User
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
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

    /**
     * Set googlePlus
     *
     * @param string $googlePlus
     * @return User
     */
    public function setGooglePlus($googlePlus)
    {
        $this->googlePlus = $googlePlus;

        return $this;
    }

    /**
     * Get googlePlus
     *
     * @return string 
     */
    public function getGooglePlus()
    {
        return $this->googlePlus;
    }

    /**
     * Set linkedIn
     *
     * @param string $linkedIn
     * @return User
     */
    public function setLinkedIn($linkedIn)
    {
        $this->linkedIn = $linkedIn;

        return $this;
    }

    /**
     * Get linkedIn
     *
     * @return string 
     */
    public function getLinkedIn()
    {
        return $this->linkedIn;
    }

    /**
     * @param mixed $groups
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
    }

    /**
     * @return mixed
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param mixed $articles
     */
    public function setArticles($articles)
    {
        $this->articles = $articles;
    }

    /**
     * @return mixed
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Add articles
     *
     * @param Article $articles
     * @return User
     */
    public function addArticle(Article $articles)
    {
        $this->articles[] = $articles;

        return $this;
    }

    /**
     * Remove article
     *
     * @param Article $article
     */
    public function removeArticle(Article $article)
    {
        $this->articles->removeElement($article);
    }

    /**
     * Add articleWatchLater
     *
     * @param Article $articleWatchLater
     * @return User
     */
    public function addArticlesWatchLater(Article $articleWatchLater)
    {
        $this->articlesWatchLater[] = $articleWatchLater;

        return $this;
    }

    /**
     * Remove articleWatchLater
     *
     * @param Article $articleWatchLater
     */
    public function removeArticlesWatchLater(Article $articleWatchLater)
    {
        $this->articlesWatchLater->removeElement($articleWatchLater);
    }

    /**
     * Get articlesWatchLater
     *
     * @return ArrayCollection
     */
    public function getArticlesWatchLater()
    {
        return $this->articlesWatchLater;
    }

    /**
     * Add subscriptions
     *
     * @param User $subscription
     * @return User
     */
    public function addSubscription(User $subscription)
    {
        $this->subscriptions[] = $subscription;

        return $this;
    }

    /**
     * Remove subscriptions
     *
     * @param User $subscription
     */
    public function removeSubscription(User $subscription)
    {
        $this->subscriptions->removeElement($subscription);
    }

    /**
     * Get subscriptions
     *
     * @return ArrayCollection
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * @return ArrayCollection
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * @param ArrayCollection $subscribers
     */
    public function setSubscribers($subscribers)
    {
        $this->subscribers = $subscribers;
    }

    /**
     * Set facebook_id
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebook_id = $facebookId;

        return $this;
    }

    /**
     * Get facebook_id
     *
     * @return string 
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set facebook_access_token
     *
     * @param string $facebookAccessToken
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebook_access_token = $facebookAccessToken;

        return $this;
    }

    /**
     * Get facebook_access_token
     *
     * @return string 
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * Set google_id
     *
     * @param string $googleId
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->google_id = $googleId;

        return $this;
    }

    /**
     * Get google_id
     *
     * @return string 
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }

    /**
     * Set google_access_token
     *
     * @param string $googleAccessToken
     * @return User
     */
    public function setGoogleAccessToken($googleAccessToken)
    {
        $this->google_access_token = $googleAccessToken;

        return $this;
    }

    /**
     * Get google_access_token
     *
     * @return string 
     */
    public function getGoogleAccessToken()
    {
        return $this->google_access_token;
    }

    /**
     * Add activity
     *
     * @param \Inck\UserBundle\Entity\Activity $activity
     *
     * @return User
     */
    public function addActivity(\Inck\UserBundle\Entity\Activity $activity)
    {
        $this->activities[] = $activity;

        return $this;
    }

    /**
     * Remove activity
     *
     * @param \Inck\UserBundle\Entity\Activity $activity
     */
    public function removeActivity(\Inck\UserBundle\Entity\Activity $activity)
    {
        $this->activities->removeElement($activity);
    }

    /**
     * Get activities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * Add badge
     *
     * @param \Inck\UserBundle\Entity\Badge $badge
     *
     * @return User
     */
    public function addBadge(\Inck\UserBundle\Entity\Badge $badge)
    {
        $this->badges[] = $badge;

        return $this;
    }

    /**
     * Remove badge
     *
     * @param \Inck\UserBundle\Entity\Badge $badge
     */
    public function removeBadge(\Inck\UserBundle\Entity\Badge $badge)
    {
        $this->badges->removeElement($badge);
    }

    /**
     * Get badges
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBadges()
    {
        return $this->badges;
    }

    /**
     * @return mixed
     */
    public function getBookshelfs()
    {
        return $this->bookshelfs;
    }

    /**
     * @param mixed $bookshelfs
     */
    public function setBookshelfs($bookshelfs)
    {
        $this->bookshelfs = $bookshelfs;
    }

    /**
     * @return ArrayCollection
     */
    public function getPublicBookshelfs(){
        $collection = new ArrayCollection();

        foreach($this->bookshelfs as $bookshelf){
            if($bookshelf->getShare()){
                $collection->add($bookshelf);
            }
        }
        return $collection;
    }
}
