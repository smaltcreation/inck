<?php

namespace Inck\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Inck\ArticleBundle\Entity\CategoryRepository")
 */
class Category
{
    const PERMISSION_DEFAULT = 'ROLE_USER';
    const PERMISSION_ADMIN = 'ROLE_SUPER_ADMIN';

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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $slug;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $permissions;

    /**
     * @ORM\ManyToMany(targetEntity="Inck\ArticleBundle\Entity\Article", mappedBy="categories"))
     */
    private $articles;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->permissions = array();
        $this->addPermission(static::PERMISSION_DEFAULT);
        $this->articles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->slug = $this->__toSlug();

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
     * Set description
     *
     * @param string $description
     * @return Category
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
     * @return string
     */
    public function __toSlug()
    {
        $slug = $this->name;

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
     * @param \Inck\ArticleBundle\Entity\Article $articles
     * @return Category
     */
    public function addArticle(\Inck\ArticleBundle\Entity\Article $articles)
    {
        $this->articles[] = $articles;

        return $this;
    }

    /**
     * Remove articles
     *
     * @param \Inck\ArticleBundle\Entity\Article $articles
     */
    public function removeArticle(\Inck\ArticleBundle\Entity\Article $articles)
    {
        $this->articles->removeElement($articles);
    }

    /**
     * Set permissions
     *
     * @param array $permissions
     * @return Category
     */
    public function setPermissions($permissions)
    {
        $this->permissions = array();

        foreach ($permissions as $permission) {
            $this->addPermission($permission);
        }

        return $this;
    }

    /**
     * Get permissions
     *
     * @return array 
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Add permission
     *
     * @param string $permission
     * @return Category
     */
    public function addPermission($permission)
    {
        $permission = strtoupper($permission);
        if ($permission === static::PERMISSION_DEFAULT) {
            return $this;
        }

        if (!in_array($permission, $this->permissions, true)) {
            $this->permissions[] = $permission;
        }

        return $this;
    }

    /**
     * Check permission
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        return in_array(strtoupper($permission), $this->getPermissions(), true);
    }

    /**
     * Remove permission
     *
     * @param string $permission
     * @return Category
     */
    public function removePermission($permission)
    {
        if ($key = array_search(strtoupper($permission), $this->permissions, true) !== false) {
            unset ($this->permissions[$key]);
            $this->permissions = array_values($this->permissions);
        }

        return $this;
    }

    /**
     * Set permission ROLE_ADMIN
     *
     * @param $boolean
     * @return Category
     */
    public function setSuperAdmin($boolean)
    {
        if ($boolean === true) {
            $this->addPermission(static::PERMISSION_ADMIN);
        }
        else {
            $this->removePermission(static::PERMISSION_ADMIN);
        }

        return $this;
    }
}
