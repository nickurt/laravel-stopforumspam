<?php

namespace nickurt\StopForumSpam\Events;

class IsSpamEmail
{
    /** @var string */
    public $email;

    /** @var int */
    public $frequency;

    /**
     * @param string $email
     * @param int $frequency
     */
    public function __construct($email, int $frequency)
    {
        $this->email = $email;
        $this->frequency = $frequency;
    }
}
