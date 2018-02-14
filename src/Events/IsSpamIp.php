<?php

namespace nickurt\StopForumSpam\Events;

class IsSpamIp
{
    /**
     * @var
     */
    public $ip;

    /**
     * IsSpamIp constructor.
     * @param $ip
     */
    public function __construct($ip)
    {
        $this->ip = $ip;
    }
}