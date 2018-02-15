<?php

namespace nickurt\StopForumSpam\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsSpamUsername implements Rule
{
    /**
     * @var
     */
    protected $username;

    /**
     * @var
     */
    protected $frequency;

    /**
     * Create a new rule instance.
     *
     * @param $username
     * @param $frequency
     *
     * @return void
     */
    public function __construct($username, $frequency = 10)
    {
        $this->username = $username;
        $this->frequency = $frequency;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $sfs = (new \nickurt\StopForumSpam\StopForumSpam())
            ->setUsername($this->username)
            ->setFrequency($this->frequency);

        return $sfs->isSpamUsername() ? false : true;
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
}
