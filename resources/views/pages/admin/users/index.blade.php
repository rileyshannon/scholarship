<?php

use App\Models\GradingGroup;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public bool $showCreateModal = false;

    // Create/Edit form
    public string $editingId = '';
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $gradingGroupId = '';
    public bool $isAdmin = false;

    #[Computed]
    public function users()
    {
        return User::query()
            ->when($this->search, fn ($q) => $q
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
            )
            ->with('gradingGroup')
            ->withCount('applicationGrades')
            ->orderBy('name')
            ->paginate(20);
    }

    #[Computed]
    public function gradingGroups()
    {
        return GradingGroup::orderBy('name')->get();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function edit(string $id): void
    {
        $user = User::findOrFail($id);
        $this->editingId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->gradingGroupId = $user->grading_group_id ?? '';
        $this->isAdmin = $user->is_admin;
        $this->showCreateModal = true;
    }

    public function save(): void
    {
        $rules = [
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'unique:users,email,' . $this->editingId],
            'gradingGroupId' => ['nullable', 'exists:grading_groups,id'],
            'isAdmin'        => ['boolean'],
        ];

        if (!$this->editingId) {
            $rules['password'] = ['required', 'string', 'min:8'];
        }

        $this->validate($rules);

        $data = [
            'name'             => $this->name,
            'email'            => $this->email,
            'grading_group_id' => $this->gradingGroupId ?: null,
            'is_admin'         => $this->isAdmin,
        ];

        if ($this->password) {
            $data['password'] = bcrypt($this->password);
        }

        if ($this->editingId) {
            User::findOrFail($this->editingId)->update($data);
        } else {
            User::create($data);
        }

        $this->resetForm();
        $this->showCreateModal = false;
        unset($this->users);
    }

    public function delete(string $id): void
    {
        User::findOrFail($id)->delete();
        unset($this->users);
    }

    private function resetForm(): void
    {
        $this->editingId = '';
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->gradingGroupId = '';
        $this->isAdmin = false;
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
        <flux:heading size="xl">Users</flux:heading>
        <flux:button wire:click="create" variant="primary">Add User</flux:button>
    </div>

    <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="Search by name or email..."
        icon="magnifying-glass"
    />

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Group</flux:table.column>
            <flux:table.column>Role</flux:table.column>
            <flux:table.column>Grades Submitted</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach($this->users as $user)
                <flux:table.row wire:key="{{ $user->id }}">
                    <flux:table.cell>
                        <div>{{ $user->name }}</div>
                        <div class="text-sm text-zinc-500">{{ $user->email }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $user->gradingGroup?->name ?? '—' }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge variant="{{ $user->is_admin ? 'primary' : 'zinc' }}">
                            {{ $user->is_admin ? 'Admin' : 'Grader' }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $user->application_grades_count }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex gap-2">
                            <flux:button wire:click="edit('{{ $user->id }}')" size="sm" variant="ghost">Edit</flux:button>
                            <flux:button wire:click="delete('{{ $user->id }}')" size="sm" variant="ghost" wire:confirm="Are you sure you want to delete this user?">Delete</flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    {{ $this->users->links() }}

    {{-- Create/Edit Modal --}}
    <flux:modal wire:model="showCreateModal" class="max-w-lg">
        <div class="space-y-6">
            <flux:heading size="lg">{{ $editingId ? 'Edit User' : 'Add User' }}</flux:heading>

            <flux:input wire:model="name" label="Name" />
            <flux:input wire:model="email" label="Email" type="email" />
            <flux:input wire:model="password" label="Password" type="password" :description="$editingId ? 'Leave blank to keep current password.' : ''" />

            <flux:select wire:model="gradingGroupId" label="Grading Group" placeholder="Select a group...">
                <flux:select.option value="">No Group</flux:select.option>
                @foreach($this->gradingGroups as $group)
                    <flux:select.option value="{{ $group->id }}">{{ $group->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:switch wire:model="isAdmin" label="Admin" />

            <div class="flex gap-4 justify-end">
                <flux:button wire:click="$set('showCreateModal', false)" variant="ghost">Cancel</flux:button>
                <flux:button wire:click="save" variant="primary">{{ $editingId ? 'Save Changes' : 'Create User' }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
