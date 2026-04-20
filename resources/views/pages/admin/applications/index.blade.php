<?php

use App\Enums\ApplicationStatus;
use App\Enums\GradeStatus;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public string $scholarshipId = '';

    public function mount(): void
    {
        $active = Scholarship::where('is_active', true)->first();
        $this->scholarshipId = $active?->id ?? '';
    }

    #[Computed]
    public function scholarships()
    {
        return Scholarship::orderByDesc('created_at')->get();
    }

    #[Computed]
    public function applications()
    {
        return ScholarshipApplication::query()
            ->when($this->search, fn($q) => $q
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
            )
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->scholarshipId, fn ($q) => $q->where('scholarship_id', $this->scholarshipId))
            ->withCount(['grades' => fn($q) => $q->where('status', GradeStatus::Active)])
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
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
    <flux:heading size="xl">Applications</flux:heading>

    <div class="flex gap-4">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Search by name or email..."
            icon="magnifying-glass"
            class=""
        />
        <flux:select wire:model.live="status">
            <flux:select.option value="">All Statuses</flux:select.option>
            @foreach(ApplicationStatus::cases() as $option)
                <flux:select.option value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:select wire:model.live="scholarshipId">
            <flux:select.option value="">All Scholarships</flux:select.option>
            @foreach($this->scholarships as $scholarship)
                <flux:select.option value="{{ $scholarship->id }}">{{ $scholarship->name }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                               wire:click="sort('name')">Name
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'final_score'" :direction="$sortDirection"
                               wire:click="sort('final_score')">Score
            </flux:table.column>
            <flux:table.column>Grades</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                               wire:click="sort('created_at')">Submitted
            </flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach($this->applications as $application)
                <flux:table.row wire:key="{{ $application->id }}">
                    <flux:table.cell>
                        <div>{{ $application->name }}</div>
                        <div class="text-sm text-zinc-500">{{ $application->email }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $application->final_score ?: '—' }}</flux:table.cell>
                    <flux:table.cell>{{ $application->grades_count }} / 3</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge variant="{{ match($application->status) {
                            ApplicationStatus::Completed->value => 'success',
                            ApplicationStatus::Flagged->value => 'warning',
                            ApplicationStatus::Disqualified->value => 'danger',
                            default => 'zinc'
                        } }}">
                            {{ ApplicationStatus::from($application->status)->label() }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $application->created_at->diffForHumans() }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button href="{{ route('admin.applications.show', $application) }}" size="sm"
                                     variant="ghost">View
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    {{ $this->applications->links() }}
</div>
