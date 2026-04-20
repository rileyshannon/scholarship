<?php

use App\Enums\FaqType;
use App\Models\FaqItem;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    #[Computed]
    public function faqs()
    {
        return FaqItem::where('type', FaqType::Faq)->orderBy('sort_order')->get();
    }

    #[Computed]
    public function eligibility()
    {
        return FaqItem::where('type', FaqType::Eligibility)->orderBy('sort_order')->get();
    }

    public function render()
    {
        return $this->view()->layout('layouts::guest');
    }
};
?>

<div class="max-w-3xl mx-auto space-y-12 py-10">

    <div>
        <flux:link href="{{ route('index') }}">&larr; Go back</flux:link>
    </div>

    {{-- FAQ --}}
    <div class="space-y-6">
        <div class="text-center">
            <flux:heading size="xl">Frequently Asked Questions</flux:heading>
        </div>

        <div class="space-y-3">
            @foreach($this->faqs as $item)
                <flux:card class="space-y-2">
                    <flux:heading size="sm">{{ $item->question }}</flux:heading>
                    <flux:text>{{ $item->answer }}</flux:text>
                </flux:card>
            @endforeach
        </div>
    </div>

    {{-- Eligibility --}}
    <div class="space-y-6">
        <div class="text-center">
            <flux:heading size="xl">Eligibility Criteria</flux:heading>
        </div>

        <div class="space-y-3">
            @foreach($this->eligibility as $item)
                <flux:card class="space-y-2">
                    <flux:heading size="sm">{{ $item->question }}</flux:heading>
                    <flux:text>{{ $item->answer }}</flux:text>
                </flux:card>
            @endforeach
        </div>
    </div>

</div>
