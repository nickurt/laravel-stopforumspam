<?php

namespace nickurt\StopForumSpam\Events;

class IsSpamUsername
{
    /** @var string */
    public $username;

    /** @var int */
    public $frequency;

    /**
     * @param  string  $username
     */
    public function __construct($username, int $frequency)
    {
        $this->username = $username;
        $this->frequency = $frequency;
    }
}
