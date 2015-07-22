<?php

namespace Inck\ArticleBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Inck\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Article
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Inck\ArticleBundle\Entity\ArticleRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class Article
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
     * @Assert\NotBlank()
     * @Assert\Length(max="160")
     * @ORM\Column(name="title", type="string", length=160)
     */
    private $title;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     * @ORM\Column(name="summary", type="text", length=255)
     */
    private $summary;

    /**
     * @var string
     *
     * @Assert\Language()
     * @ORM\Column(name="language", type="string", length=30)
     */
    private $language;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime()
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime $updatedAt
     */
    protected $updatedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="official", type="boolean")
     */
    private $official;

    /**
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime()
     * @ORM\Column(name="publishedAt", type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean", nullable=true)
     */
    private $locked = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="asDraft", type="boolean")
     */
    private $asDraft;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $slug;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\Inck\UserBundle\Entity\User", inversedBy="articles")
     */
    private $author;

    /**
     * @var File
     *
     * @Assert\Image(
     *     maxSize = "10M",
     * )
     * @Vich\UploadableField(mapping="article_image", fileNameProperty="imageName")
     */
    protected $imageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $imageName;

    /**
     * @var string
     */
    protected $previousImageName;

    /**
     * @var Category[]
     *
     * @ORM\ManyToMany(targetEntity="Inck\ArticleBundle\Entity\Category", inversedBy="articles", cascade={"persist"})
     * @Assert\Count(min="1", max="3")
     */
    private $categories;

    /**
     * @var Tag[]
     *
     * @ORM\ManyToMany(targetEntity="Inck\ArticleBundle\Entity\Tag", inversedBy="articles", cascade={"persist"})
     * @Assert\Count(min="1", max="10")
     */
    private $tags;

    /**
     * @var Vote[]
     *
     * @ORM\OneToMany(targetEntity="Inck\ArticleBundle\Entity\Vote", mappedBy="article", cascade={"remove"})
     * @ORM\JoinColumn(nullable=true)
     *
     * @ORM\OrderBy({"submittedOn" = "ASC"})
     */
    private $votes;

    /**
     * @var Report[]
     *
     * @ORM\OneToMany(targetEntity="Inck\ArticleBundle\Entity\Report", mappedBy="article", cascade={"remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $reports;

    /**
     * @var boolean anonymous
     * @ORM\Column(type="boolean")
     */
    private $anonymous = false;


    public function __construct()
    {
        $this->official     = false;
        $this->categories   = new ArrayCollection();
        $this->tags         = new ArrayCollection();
        $this->votes        = new ArrayCollection();
        $this->reports      = new ArrayCollection();
        $this->anonymous;
    }

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersist()
    {
        $this->createdAt = new DateTime();
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
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;
        $this->slug = $this->__toSlug();

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
     * Set summary
     *
     * @param string $summary
     * @return Article
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Article
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Article
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        $this->setUpdatedAt($createdAt);

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
     * Set official
     *
     * @param boolean $official
     * @return Article
     */
    public function setOfficial($official)
    {
        $this->official = $official;

        return $this;
    }

    /**
     * Get official
     *
     * @return boolean
     */
    public function getOfficial()
    {
        return $this->official;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Article
     */
    public function setPublished($published)
    {
        $this->published = $published;

        if($published) {
            $this->setPublishedAt(new \DateTime());
            $this->setAsDraft(false);
        } else {
            $this->setAsDraft(true);
        }

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     * @return Article
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;
        $this->setUpdatedAt($publishedAt);

        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return Article
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
        if($locked) {
            $this->setPublished(false);
        }

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set asDraft
     *
     * @param boolean $asDraft
     * @return Article
     */
    public function setAsDraft($asDraft)
    {
        $this->asDraft = $asDraft;

        return $this;
    }

    /**
     * Get asDraft
     *
     * @return boolean
     */
    public function getAsDraft()
    {
        return $this->asDraft;
    }

    /**
     * Set author
     *
     * @param User $author
     * @return Article
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Add categories
     *
     * @param Category $categories
     * @return Article
     */
    public function addCategory(Category $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param Category $categories
     */
    public function removeCategory(Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add tag
     *
     * @param Tag $tag
     * @return Article
     */
    public function addTag(Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Set tags
     *
     * @param ArrayCollection $tags
     * @return Article
     */
    public function setTags(ArrayCollection $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add votes
     *
     * @param Vote $votes
     * @return Article
     */
    public function addVote(Vote $votes)
    {
        $this->votes[] = $votes;

        return $this;
    }

    /**
     * Remove votes
     *
     * @param Vote $votes
     */
    public function removeVote(Vote $votes)
    {
        $this->votes->removeElement($votes);
    }

    /**
     * Get votes
     *
     * @return ArrayCollection
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Add report
     *
     * @param Report $report
     * @return Article
     */
    public function addReport(Report $report)
    {
        $this->reports[] = $report;

        return $this;
    }

    /**
     * Remove report
     *
     * @param Report $report
     */
    public function removeReport(Report $report)
    {
        $this->reports->removeElement($report);
    }

    /**
     * Get reports
     *
     * @return ArrayCollection
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param File|UploadedFile|null $image
     */
    public function setImageFile($image)
    {
        $this->imageFile = $image;

        if ($image) {
            $this->updatedAt = new \DateTime();
        }
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        $end = '/web/article/image';

        return (substr((string) $this->imageFile, -strlen($end)) === $end)
            ? null
            : $this->imageFile;
    }

    /**
     * @param string $imageName
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    public function savePreviousImageName()
    {
        $this->previousImageName = $this->imageName;
    }

    /**
     * @return string
     */
    public function getPreviousImageName()
    {
        return $this->previousImageName;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return string
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Rewrite string to slug
     *
     * @internal param string $slug
     * @return string
     */
    public function __toSlug()
    {
        $slug = $this->title;

        // replace non letter or digits by -
        $slug = preg_replace('#[^\\pL\d]+#u', '-', $slug);

        // trim
        $slug = trim($slug, '-');

        // transliterate
        if (function_exists('iconv')) {
            $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        }

        // lowercase
        $slug = strtolower($slug);

        // remove unwanted characters
        $slug = preg_replace('#[^-\w]+#', '', $slug);

        if (empty($slug)) {
            return 'n-a';
        }

        return $slug;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getType()
    {
        if($this->asDraft === true)
        {
            return 'Brouillon';
        }

        else if($this->published === true)
        {
            return 'Publié';
        }

        else if($this->locked === true)
        {
            return 'Fermé';
        }

        return 'Undefined';
    }

    /**
     * @return boolean
     */
    public function isAnonymous()
    {
        return $this->anonymous;
    }

    /**
     * @param boolean $anonymous
     */
    public function setAnonymous($anonymous)
    {
        $this->anonymous = $anonymous;
    }
}
