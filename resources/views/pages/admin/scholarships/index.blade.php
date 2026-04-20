<?php

use App\Models\Scholarship;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public bool $showModal = false;
    public string $editingId = '';
    public string $name = '';
    public string $opensAt = '';
    public string $closesAt = '';
    public string $awardDate = '';
    public bool $isActive = false;

    #[Computed]
    public function scholarships()
    {
        return Scholarship::query()
            ->withCount('applications')
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $scholarship = Scholarship::findOrFail($id);
        $this->editingId = $id;
        $this->name = $scholarship->name;
        $this->opensAt = $scholarship->opens_at->format('Y-m-d\TH:i');
        $this->closesAt = $scholarship->closes_at->format('Y-m-d\TH:i');
        $this->awardDate = $scholarship->award_date->format('Y-m-d\TH:i');
        $this->isActive = $scholarship->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'opensAt' => ['required', 'date'],
            'closesAt' => ['required', 'date', 'after:opensAt'],
            'awardDate' => ['required', 'date', 'after:closesAt'],
            'isActive' => ['boolean'],
        ]);

        // Deactivate all others if setting this one as active
        if ($this->isActive) {
            Scholarship::where('id', '!=', $this->editingId)
                ->update(['is_active' => false]);
        }

        $data = [
            'name' => $this->name,
            'opens_at' => $this->opensAt,
            'closes_at' => $this->closesAt,
            'award_date' => $this->awardDate,
            'is_active' => $this->isActive,
        ];

        if ($this->editingId) {
            Scholarship::findOrFail($this->editingId)->update($data);
        } else {
            Scholarship::create($data);
        }

        $this->resetForm();
        $this->showModal = false;
        unset($this->scholarships);
    }

    public function toggleActive(string $id): void
    {
        Scholarship::where('id', '!=', $id)->update(['is_active' => false]);
        Scholarship::findOrFail($id)->update(['is_active' => true]);
        unset($this->scholarships);
    }

    private function resetForm(): void
    {
        $this->editingId = '';
        $this->name = '';
        $this->opensAt = '';
        $this->closesAt = '';
        $this->awardDate = '';
        $this->isActive = false;
    }

    public function render()
    {
        return $this->view()->layout('layouts::admin');
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Scholarships</flux:heading>
        <flux:button wire:click="create" variant="primary">Add Scholarship</flux:button>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Opens</flux:table.column>
            <flux:table.column>Closes</flux:table.column>
            <flux:table.column>Award Date</flux:table.column>
            <flux:table.column>Applications</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach($this->scholarships as $scholarship)
                <flux:table.row wire:key="{{ $scholarship->id }}">
                    <flux:table.cell>{{ $scholarship->name }}</flux:table.cell>
                    <flux:table.cell>{{ $scholarship->opens_at->setTimezone('America/New_York')->format('M j, Y g:i A T') }}</flux:table.cell>
                    <flux:table.cell>{{ $scholarship->closes_at->setTimezone('America/New_York')->format('M j, Y g:i A T') }}</flux:table.cell>
                    <flux:table.cell>{{ $scholarship->award_date->setTimezone('America/New_York')->format('M j, Y g:i A T') }}</flux:table.cell>
                    <flux:table.cell>{{ $scholarship->applications_count }}</flux:table.cell>
                    <flux:table.cell>
                        @if($scholarship->is_active)
                            <flux:badge variant="success">Active</flux:badge>
                        @else
                            <flux:badge variant="zinc">Inactive</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex gap-2">
                            @if(!$scholarship->is_active)
                                <flux:button wire:click="toggleActive('{{ $scholarship->id }}')" size="sm"
                                             variant="ghost">Set Active
                                </flux:button>
                            @endif
                            <flux:button wire:click="edit('{{ $scholarship->id }}')" size="sm" variant="ghost">Edit
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    {{ $this->scholarships->links() }}

    <flux:modal wire:model="showModal" class="max-w-lg">
        <div class="space-y-6">
            <flux:heading size="lg">{{ $editingId ? 'Edit Scholarship' : 'Add Scholarship' }}</flux:heading>
            <flux:input wire:model="name" label="Name"/>
            <flux:input wire:model="opensAt" label="Opens At" type="datetime-local"/>
            <flux:input wire:model="closesAt" label="Closes At" type="datetime-local"/>
            <flux:input wire:model="awardDate" label="Award Date" type="datetime-local"/>
            <flux:switch wire:model="isActive" label="Active"
                         description="Only one scholarship can be active at a time."/>
            <div class="flex gap-4 justify-end">
                <flux:button wire:click="$set('showModal', false)" variant="ghost">Cancel</flux:button>
                <flux:button wire:click="save"
                             variant="primary">{{ $editingId ? 'Save Changes' : 'Create Scholarship' }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
