<?php

namespace App\Livewire\Contact;

use Livewire\Attributes\Rule;
use Livewire\Form;

class ContactFormFields extends Form
{
    #[Rule('required|string|max:255')]
    public string $name;

    #[Rule('required|email')]
    public string $email;

    #[Rule('required|max:1000')]
    public string $comments;
}
