<?php

namespace nickurt\StopForumSpam\Events;

class IsSpamUsername
{
    /**
     * @var
     */
    public $username;

    /**
     * IsSpamUsername constructor.
     * @param $ip
     */
    public function __construct($username)
    {
        $this->username = $username;
    }
}