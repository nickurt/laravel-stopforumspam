<?php

namespace nickurt\StopForumSpam\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsSpamUsername implements Rule
{
    /** @var int */
    protected $frequency;

    /**
     * @param int $frequency
     */
    public function __construct($frequency = 10)
    {
        $this->frequency = $frequency;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('stopforumspam::stopforumspam.it_is_currently_not_possible_to_register_with_your_specified_information_please_try_later_again');
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     * @throws \Exception
     */
    public function passes($attribute, $value)
    {
        /** @var \nickurt\StopForumSpam\StopForumSpam $stopForumSpam */
        $stopForumSpam = app('StopForumSpam');

        $stopForumSpam->setUsername($value)->setFrequency($this->frequency);

        return $stopForumSpam->isSpamUsername() ? false : true;
    }
}
