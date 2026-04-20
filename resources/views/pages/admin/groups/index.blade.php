<?php

use App\Models\GradingGroup;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public string $editingId = '';
    public string $name = '';

    #[Computed]
    public function groups()
    {
        return GradingGroup::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->withCount('users')
            ->withCount('applications')
            ->orderBy('name')
            ->paginate(20);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $group = GradingGroup::findOrFail($id);
        $this->editingId = $id;
        $this->name = $group->name;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:grading_groups,name,' . $this->editingId],
        ]);

        if ($this->editingId) {
            GradingGroup::findOrFail($this->editingId)->update(['name' => $this->name]);
        } else {
            GradingGroup::create(['name' => $this->name]);
        }

        $this->resetForm();
        $this->showModal = false;
        unset($this->groups);
    }

    public function delete(string $id): void
    {
        GradingGroup::findOrFail($id)->delete();
        unset($this->groups);
    }

    private function resetForm(): void
    {
        $this->editingId = '';
        $this->name = '';
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return $this->view()->layout('layouts::admin');
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Grading Groups</flux:heading>
        <flux:button wire:click="create" variant="primary">Add Group</flux:button>
    </div>

    <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="Search groups..."
        icon="magnifying-glass"
    />

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Graders</flux:table.column>
            <flux:table.column>Applications</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach($this->groups as $group)
                <flux:table.row wire:key="{{ $group->id }}">
                    <flux:table.cell>{{ $group->name }}</flux:table.cell>
                    <flux:table.cell>{{ $group->users_count }}</flux:table.cell>
                    <flux:table.cell>{{ $group->applications_count }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex gap-2">
                            <flux:button wire:click="edit('{{ $group->id }}')" size="sm" variant="ghost">Edit</flux:button>
                            <flux:button wire:click="delete('{{ $group->id }}')" size="sm" variant="ghost" wire:confirm="Are you sure? This will affect all graders in this group.">Delete</flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    {{ $this->groups->links() }}

    <flux:modal wire:model="showModal" class="max-w-md">
        <div class="space-y-6">
            <flux:heading size="lg">{{ $editingId ? 'Edit Group' : 'Add Group' }}</flux:heading>
            <flux:input wire:model="name" label="Group Name" />
            <div class="flex gap-4 justify-end">
                <flux:button wire:click="$set('showModal', false)" variant="ghost">Cancel</flux:button>
                <flux:button wire:click="save" variant="primary">{{ $editingId ? 'Save Changes' : 'Create Group' }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
