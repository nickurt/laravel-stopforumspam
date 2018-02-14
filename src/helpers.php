<?php

use nickurt\StopForumSpam\StopForumSpam;

if (! function_exists('stopforumspam')) {
    function stopforumspam()
    {
        return app(StopForumSpam::class);
    }
}