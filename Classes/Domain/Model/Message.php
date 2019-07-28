<?php
namespace Ewert\WebPush\Domain\Model;

/*
 * This file is part of the Ewert.WebPush package.
 */

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Message
{

    /**
     * @var string
     * @ORM\Column(length=512)
     * @Flow\Validate(type="NotEmpty")
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(nullable=TRUE)
     */
    protected $direction;

    /**
     * @var string
     * @ORM\Column(nullable=TRUE)
     */
    protected $lang;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=TRUE)
     */
    protected $body;

    /**
     * @var string
     * @ORM\Column(nullable=TRUE)
     */
    protected $tag;

    /**
     * @var string
     * @ORM\Column(nullable=TRUE)
     */
    protected $image;

    /**
     * @var string
     * @ORM\Column(nullable=TRUE)
     */
    protected $icon;

    /**
     * @var string
     * @ORM\Column(nullable=TRUE)
     */
    protected $badge;

    /**
     * @var array
     * @ORM\Column(type="simple_array", nullable=TRUE)
     */
    protected $vibrate;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    protected $timestamp;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=TRUE)
     */
    protected $renotify;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=TRUE)
     */
    protected $silent;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=TRUE)
     */
    protected $requireInteraction;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=TRUE)
     */
    protected $data;

    /**
     * @var array
     * @ORM\Column(type="json_array", nullable=TRUE)
     */
    protected $actions;


    /**
     * Constructs a new message
     *
     * @param string $title The title of the message
     * @param string $body The body of the message (if any)
     * @api
     */
    public function __construct($title, $body = null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->timestamp = time();
    }


    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     * @return void
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }
    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     * @return void
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }
    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return void
     */
    public function setBody($body)
    {
        $this->body = $body;
    }
    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     * @return void
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }
    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     * @return void
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return void
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }
    /**
     * @return string
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * @param string $badge
     * @return void
     */
    public function setBadge($badge)
    {
        $this->badge = $badge;
    }
    /**
     * @return array
     */
    public function getVibrate()
    {
        return $this->vibrate;
    }

    /**
     * @param array $vibrate
     * @return void
     */
    public function setVibrate(array $vibrate)
    {
        $this->vibrate = $vibrate;
    }
    /**
     * @return integer
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param integer $timestamp
     * @return void
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }
    /**
     * @return boolean
     */
    public function getRenotify()
    {
        return $this->renotify;
    }

    /**
     * @param boolean $renotify
     * @return void
     */
    public function setRenotify($renotify)
    {
        $this->renotify = $renotify;
    }
    /**
     * @return boolean
     */
    public function getSilent()
    {
        return $this->silent;
    }

    /**
     * @param boolean $silent
     * @return void
     */
    public function setSilent($silent)
    {
        $this->silent = $silent;
    }
    /**
     * @return boolean
     */
    public function getRequireInteraction()
    {
        return $this->requireInteraction;
    }

    /**
     * @param boolean $requireInteraction
     * @return void
     */
    public function setRequireInteraction($requireInteraction)
    {
        $this->requireInteraction = $requireInteraction;
    }
    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     * @return void
     */
    public function setData($data)
    {
        $this->data = $data;
    }
    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     * @return void
     */
    public function setActions(array $actions)
    {
        $this->actions = $actions;
    }
}
