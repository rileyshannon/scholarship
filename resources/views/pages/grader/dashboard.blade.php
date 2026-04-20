<?php

use App\Enums\GradeStatus;
use App\Models\ScholarshipApplication;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';

    #[Computed]
    public function applications(): LengthAwarePaginator
    {
        return ScholarshipApplication::query()
            ->whereHas('graders', fn($q) => $q->where('users.id', auth()->id()))
            ->whereAny(['name', 'email'], 'like', "%{$this->search}")
            ->with([
                'grades' => fn($q) => $q
                    ->where('user_id', auth()->id())
                    ->where('status', GradeStatus::Active)
            ])
            ->latest()
            ->paginate(15);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
};
?>

<div class="space-y-6">
    <flux:heading size="xl">My Applications</flux:heading>

    <flux:input wire:model.live.debounce.300ms="search" placeholder="Search by applicant id" icon="magnifying-glass" />

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Applicant</flux:table.column>
            <flux:table.column>Submitted</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach($this->applications as $application)
                <flux:table.row wire:key="{{ $application->id }}">
                    <flux:table.cell>
                        <div>{{ $application->id }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $application->created_at->diffForHumans() }}</flux:table.cell>
                    <flux:table.cell>
                        @if($application->grades->isNotEmpty())
                            <flux:badge variant="success">Graded</flux:badge>
                        @else
                            <flux:badge variant="warning">Pending</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if($application->grades->isEmpty())
                            <flux:button href="{{ route('grader.grade.show', $application) }}" size="sm">Grade</flux:button>
                        @else
                            <flux:button href="{{ route('grader.grade.show', $application) }}" size="sm" variant="ghost">View</flux:button>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    {{ $this->applications->links() }}
</div>
