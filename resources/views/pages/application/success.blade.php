<?php

use Livewire\Component;

new class extends Component
{
    public function render()
    {
        return $this->view()->layout('layouts::guest');
    }
};
?>

<div class="max-w-lg mx-auto text-center space-y-4 py-16">
    <flux:heading size="xl">Application Submitted!</flux:heading>
    <flux:subheading>
        Thank you for applying for the PPOT Scholarship. You will receive a confirmation email shortly.
    </flux:subheading>
    <flux:button href="{{ route('index') }}" variant="ghost">Return Home</flux:button>
</div>
