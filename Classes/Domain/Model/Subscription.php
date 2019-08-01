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
class Subscription
{

    /**
     * @var string
     * @ORM\Column(length=512)
     * @Flow\Validate(type="NotEmpty")
     */
    protected $endpoint;

    /**
     * @var integer
     * @ORM\Column(nullable=TRUE, type="integer")
     */
    protected $expirationTime;

    /**
     * @var string
     * @ORM\Column(length=512)
     */
    protected $p256dh;

    /**
     * @var string
     * @ORM\Column(length=512)
     */
    protected $auth;


    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param string $endpoint
     * @return void
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }
    /**
     * @return integer
     */
    public function getExpirationTime()
    {
        return $this->expirationTime;
    }

    /**
     * @param integer $expirationTime
     * @return void
     */
    public function setExpirationTime($expirationTime = null)
    {
        $this->expirationTime = $expirationTime;
    }
    /**
     * @return string
     */
    public function getP256dh()
    {
        return $this->p256dh;
    }

    /**
     * @param string $p256dh
     * @return void
     */
    public function setP256dh($p256dh)
    {
        $this->p256dh = $p256dh;
    }
    /**
     * @return string
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @param string $auth
     * @return void
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;
    }
}
