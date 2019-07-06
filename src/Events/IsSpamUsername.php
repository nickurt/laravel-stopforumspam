<?php

namespace nickurt\StopForumSpam\Events;

class IsSpamUsername
{
    /** @var string */
    public $username;

    /**
     * @param string $username
     */
    public function __construct($username)
    {
        $this->username = $username;
    }
}