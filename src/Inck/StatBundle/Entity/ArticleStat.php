<?php

namespace Inck\StatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Inck\ArticleBundle\Entity\Article;

/**
 * ArticleStat
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ArticleStat extends Statistic
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Votes for this Article
     * and organize the voting in a chronological table
     *
     * @param $article
     * @return array
     */
    static function getStatVotes(Article $article)
    {
        $votes = $article->getVotes();

        $statVotes = array();
        $valueVote = 0;

        foreach($votes as $key => $vote)
        {
            $vote->getUp() ? $valueVote++ : $valueVote--;
            $statVotes[$key] = array($vote->getUp(), $vote->getSubmittedOn(), $valueVote);
        }

        return $statVotes;
    }

    /**
     * Set article
     *
     * @param \Inck\ArticleBundle\Entity\Article $article
     * @return ArticleStat
     */
    public function setArticle(\Inck\ArticleBundle\Entity\Article $article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return \Inck\ArticleBundle\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }
}
