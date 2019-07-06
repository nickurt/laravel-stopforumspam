<?php

namespace nickurt\StopForumSpam\Events;

class IsSpamIp
{
    /** @var string */
    public $ip;

    /**
     * @param string $ip
     */
    public function __construct($ip)
    {
        $this->ip = $ip;
    }
}