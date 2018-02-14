<?php

namespace nickurt\StopForumSpam\Events;

class IsSpamEmail
{
    /**
     * @var
     */
    public $email;

    /**
     * IsSpamEmail constructor.
     * @param $email
     */
    public function __construct($email)
    {
        $this->email = $email;
    }
}