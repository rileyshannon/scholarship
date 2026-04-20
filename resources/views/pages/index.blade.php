<?php

use App\Models\Scholarship;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public bool $checkboxOne = false;
    public bool $checkboxTwo = false;
    public bool $checkboxThree = false;
    public bool $checkboxFour = false;
    public bool $checkboxFive = false;
    public bool $agreeToTerms = false;

    #[Computed]
    public function scholarship(): ?Scholarship
    {
        return Scholarship::where('is_active', true)->first();
    }

    #[Computed]
    public function allChecked(): bool
    {
        return $this->checkboxOne
            && $this->checkboxTwo
            && $this->checkboxThree
            && $this->checkboxFour
            && $this->checkboxFive;
    }

    #[Computed]
    public function isOpen(): bool
    {
        return $this->scholarship
            && now()->isBetween($this->scholarship->opens_at, $this->scholarship->closes_at);
    }

    public function render()
    {
        return $this->view()->layout('layouts::guest');
    }
};
?>

<div class="max-w-3xl mx-auto space-y-8 py-10">

    {{-- Header --}}
    <div class="space-y-4">
        <flux:heading size="xl">Professional Pilots of Tomorrow</flux:heading>
        <flux:heading size="lg">Scholarship Application Portal</flux:heading>
        <flux:text>Welcome to the PPOT scholarship application portal!</flux:text>
        <flux:text>
            To view some frequently asked questions, please <flux:link href="{{ route('faq') }}">click here</flux:link>.
            Be sure to check the <flux:link href="https://theppot.org" target="_blank">PPOT website</flux:link> often,
            as well as follow us on
            <flux:link href="https://www.facebook.com/ProfessionalPilots/" target="_blank">Facebook</flux:link>,
            <flux:link href="https://www.instagram.com/professionalpilots/" target="_blank">Instagram</flux:link>, and
            <flux:link href="https://x.com/The_PPOT" target="_blank">X</flux:link>
            for information on future scholarships!
        </flux:text>

        @if($this->scholarship)
            <flux:text>Scholarship Application Opens: <strong>{{ $this->scholarship->opens_at->format('F j, Y \a\t g:i A T') }}</strong></flux:text>
            <flux:text>Scholarship Application Closes: <strong>{{ $this->scholarship->closes_at->format('F j, Y \a\t g:i A T') }}</strong></flux:text>
            <flux:text>The winner will be announced in the weeks following this application. Please stay tuned!</flux:text>
        @endif
    </div>

    {{-- AI Warning --}}
    <flux:callout variant="warning" icon="exclamation-triangle">
        <flux:callout.text>
            Avoid relying on AI to generate your responses. Our graders see hundreds of applications every year
            and can recognize the difference between AI-generated responses and authentic ones. Be yourself,
            tell your story, and be genuine.
        </flux:callout.text>
    </flux:callout>

    {{-- Eligibility & Application Start --}}
    @if($this->isOpen)
        <div class="space-y-4">
            <flux:text>To continue, please check all that apply below:</flux:text>

            <flux:card class="space-y-4">
                <flux:checkbox wire:model.live="checkboxOne" label="I am a United States Citizen, Permanent Resident or International Student under a F1 Visa." />
                <flux:checkbox wire:model.live="checkboxTwo" label="I am residing in the continental United States, Alaska, Hawaii, Guam, the U.S. Virgin Islands, or Puerto Rico." />
                <flux:checkbox wire:model.live="checkboxThree" label="I will be enrolled in a flight school at the time of scholarship disbursement. (The scholarship funds will be disbursed directly to your flight school.)" />
                <flux:checkbox wire:model.live="checkboxFour" label="I DO NOT currently hold and AM NOT currently eligible for an ATP or Restricted ATP." />
                <flux:checkbox wire:model.live="checkboxFive" label="I am at least 16 years of age. (The minimum age to solo an airplane is 16 years old.)" />
            </flux:card>

            @if($this->allChecked)
                <flux:callout variant="info" icon="information-circle">
                    <flux:callout.heading>Attention!</flux:callout.heading>
                    <flux:callout.text>
                        <p>By continuing to the application, you are agreeing that all the above statements are true and correct. If any of the conditions above are not met, you are not eligible for this scholarship.</p>
                        <p class="mt-2">Please read the following instructions carefully before starting this application:</p>
                        <ol class="list-decimal ms-6 mt-2 space-y-1">
                            <li>Please complete this application entirely in one sitting</li>
                            <li>All fields are required unless stated otherwise</li>
                            <li><strong>DO NOT</strong> click back at any time during the application</li>
                            <li><strong>DO NOT</strong> close out of the application until advised</li>
                        </ol>
                        <div class="mt-4">
                            <flux:checkbox wire:model.live="agreeToTerms" label="I agree to all the statements above" />
                        </div>
                    </flux:callout.text>
                </flux:callout>

                @if($agreeToTerms)
                    <flux:button href="{{ route('application.create') }}" variant="primary">
                        Start Application
                    </flux:button>
                @endif
            @endif
        </div>

    @elseif($this->scholarship && now()->isBefore($this->scholarship->opens_at))
        <flux:callout variant="info">
            <flux:callout.text>Applications for the <strong>{{ $this->scholarship->name }}</strong> open on {{ $this->scholarship->opens_at->format('F j, Y \a\t g:i A T') }}.</flux:callout.text>
        </flux:callout>

    @elseif($this->scholarship)
        <flux:callout variant="danger">
            <flux:callout.text>The application portal is currently closed. Award announcements will be made on {{ $this->scholarship->award_date->format('F j, Y') }}.</flux:callout.text>
        </flux:callout>

    @else
        <flux:callout variant="danger">
            <flux:callout.text>The application portal is currently closed.</flux:callout.text>
        </flux:callout>
    @endif

</div>
