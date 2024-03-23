<?php

namespace nickurt\StopForumSpam\Events;

class IsSpamIp
{
    /** @var string */
    public $ip;

    /** @var int */
    public $frequency;

    /**
     * @param  string  $ip
     */
    public function __construct($ip, int $frequency)
    {
        $this->ip = $ip;
        $this->frequency = $frequency;
    }
}
