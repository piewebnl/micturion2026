<?php

namespace App\Livewire\Contact;

use App\Email\EmailContactForm;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ContactForm extends Component
{
    public ContactFormFields $form;

    public $showForm = true;

    public function submit()
    {
        $validated = $this->validate();

        if (!Mail::to('pie@micturion.com')->send(new EmailContactForm($validated))) {
            return response()->error('Email error');
        }

        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.contact.contact-form');
    }
}
