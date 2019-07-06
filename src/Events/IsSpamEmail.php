<?php

namespace nickurt\StopForumSpam\Events;

class IsSpamEmail
{
    /** @var string */
    public $email;

    /**
     * @param string $email
     */
    public function __construct($email)
    {
        $this->email = $email;
    }
}