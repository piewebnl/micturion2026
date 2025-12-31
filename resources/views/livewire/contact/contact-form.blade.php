<div>

    @if ($showForm)
        <form wire:submit.prevent="submit" x-data="{ submitForm() { this.$refs.submitButton.click(); } }" class="max-w-lg">

            @method('post')
            @csrf

            <x-forms.input :wireModel="'form.name'" id="name" placeholder="" name="name" type="text" label="Name"
                class="mb-6">
            </x-forms.input>


            <x-forms.input :wireModel="'form.email'" id="email" placeholder="" name="email" type="text" label="E-mail"
                class="mb-6">
            </x-forms.input>

            <x-forms.textarea :wireModel="'form.comments'" id="comments" placeholder="" name="comments" label="Comments"
                class="mb-6">
            </x-forms.textarea>


            <x-buttons.button type="submit" class="btn-primary" x-ref="submitButton">Save</x-buttons.button>



        </form>
    @else
        <h2 class="text-lg">Thank you for your message.</h1>
    @endif
</div>
