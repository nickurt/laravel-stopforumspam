<?php

namespace nickurt\StopForumSpam\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsSpamEmail implements Rule
{
    /**
     * @var
     */
    protected $email;

    /**
     * @var
     */
    protected $frequency;

    /**
     * Create a new rule instance.
     *
     * @param $email
     * @param $frequency
     *
     * @return void
     */
    public function __construct($email, $frequency = 10)
    {
        $this->email = $email;
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
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $sfs = (new \nickurt\StopForumSpam\StopForumSpam())
            ->setEmail($this->email)
            ->setFrequency($this->frequency);

        return $sfs->isSpamEmail() ? false : true;
    }
}
