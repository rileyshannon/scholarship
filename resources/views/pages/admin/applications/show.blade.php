<?php

use App\Actions\ReassignGrader;
use App\Enums\ApplicationStatus;
use App\Enums\GradeStatus;
use App\Models\ScholarshipApplication;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public ScholarshipApplication $application;

    public string $reassignFrom = '';
    public string $reassignTo = '';

    #[Computed]
    public function graders()
    {
        return $this->application->graders()->withPivot('assigned_at')->get();
    }

    #[Computed]
    public function grades()
    {
        return $this->application->grades()
            ->with('user')
            ->where('status', GradeStatus::Active)
            ->get();
    }

    #[Computed]
    public function availableGraders()
    {
        return User::where('is_admin', false)
            ->whereNotIn('id', $this->graders->pluck('id'))
            ->orderBy('name')
            ->get();
    }

    public function reassign(): void
    {
        $this->validate([
            'reassignFrom' => ['required', 'exists:users,id'],
            'reassignTo'   => ['required', 'exists:users,id', 'different:reassignFrom'],
        ]);

        (new ReassignGrader)->handle(
            $this->application,
            User::find($this->reassignFrom),
            User::find($this->reassignTo),
        );

        $this->reassignFrom = '';
        $this->reassignTo = '';
        unset($this->graders, $this->grades);
    }

    public function updateStatus(string $status): void
    {
        $this->application->update(['status' => $status]);
    }

    public function render()
    {
        return $this->view()->layout('layouts::admin');
    }
};
?>

<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Application: {{ $application->name }}</flux:heading>
        </div>
        <div>
            <flux:select wire:change="updateStatus($event.target.value)" :value="$application->status">
                @foreach(ApplicationStatus::cases() as $option)
                    <flux:select.option value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    {{-- Personal Info --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Personal Information</flux:heading>
        <div class="grid grid-cols-3 gap-6">
            <div><flux:subheading>Name</flux:subheading><p>{{ $application->name }}</p></div>
            <div><flux:subheading>Email</flux:subheading><p>{{ $application->email }}</p></div>
            <div><flux:subheading>Phone</flux:subheading><p>{{ $application->phone }}</p></div>
            <div><flux:subheading>City</flux:subheading><p>{{ $application->city }}</p></div>
            <div><flux:subheading>State</flux:subheading><p>{{ $application->state }}</p></div>
            <div><flux:subheading>Gender</flux:subheading><p>{{ $application->gender }}</p></div>
            <div><flux:subheading>PPOT Member</flux:subheading><p>{{ $application->ppot_member ? 'Yes' : 'No' }}</p></div>
            <div><flux:subheading>Mentor</flux:subheading><p>{{ $application->ppot_mentor ?? '—' }}</p></div>
            <div><flux:subheading>Prior Applicant</flux:subheading><p>{{ $application->prior_applicant ? 'Yes' : 'No' }}</p></div>
            <div><flux:subheading>Reference</flux:subheading><p>{{ $application->reference }}</p></div>
        </div>
    </flux:card>

    {{-- Flight Training --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Flight Training</flux:heading>
        <div class="grid grid-cols-2 gap-6">
            <div><flux:subheading>Flight School</flux:subheading><p>{{ $application->flight_school }}</p></div>
            <div><flux:subheading>Flight Training</flux:subheading><p>{{ $application->flight_training }}</p></div>
            <div><flux:subheading>Total Time</flux:subheading><p>{{ $application->total_time }}</p></div>
            <div><flux:subheading>Flight Instruction</flux:subheading><p>{{ $application->flight_instruction }}</p></div>
        </div>
    </flux:card>

    {{-- Education --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Education</flux:heading>
        <div class="grid grid-cols-3 gap-6">
            <div><flux:subheading>Education Level</flux:subheading><p>{{ $application->education_level }}</p></div>
            <div><flux:subheading>School</flux:subheading><p>{{ $application->school }}</p></div>
            <div><flux:subheading>Graduation</flux:subheading><p>{{ $application->graduation_month }} {{ $application->graduation_year }}</p></div>
            <div><flux:subheading>GPA</flux:subheading><p>{{ $application->gpa }}</p></div>
        </div>
        <div><flux:subheading>Academics</flux:subheading><p>{{ $application->academics }}</p></div>
    </flux:card>

    {{-- Goals & Awards --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Goals & Awards</flux:heading>
        <div><flux:subheading>Short Term Goal</flux:subheading><p>{{ $application->short_term_goal }}</p></div>
        <div><flux:subheading>Long Term Goal</flux:subheading><p>{{ $application->long_term_goal }}</p></div>
        <div><flux:subheading>Awards</flux:subheading><p>{{ $application->received_awards ?? 'None' }}</p></div>
    </flux:card>

    {{-- Community & Employment --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Community & Employment</flux:heading>
        <div><flux:subheading>Other Organizations</flux:subheading><p>{{ $application->other_organizations ?? 'None' }}</p></div>
        <div><flux:subheading>Volunteer Events</flux:subheading><p>{{ $application->volunteer_events ?? 'None' }}</p></div>
        @if($application->employmentHistories->isNotEmpty())
            <div>
                <flux:subheading>Employment History</flux:subheading>
                <div class="space-y-2 mt-2">
                    @foreach($application->employmentHistories as $employment)
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <span>{{ $employment->employer_name }}</span>
                            <span>{{ $employment->position }}</span>
                            <span>{{ $employment->length }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        <div><flux:subheading>Career Aspirations</flux:subheading><p>{{ $application->career_aspirations }}</p></div>
    </flux:card>

    {{-- Essays --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Essays</flux:heading>
        <div><flux:subheading>Essay One</flux:subheading><p class="whitespace-pre-wrap">{{ $application->essay_one }}</p></div>
        <div><flux:subheading>Essay Two</flux:subheading><p class="whitespace-pre-wrap">{{ $application->essay_two }}</p></div>
    </flux:card>

    {{-- Scoring Summary --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Scoring</flux:heading>
        <div class="grid grid-cols-3 gap-6">
            <div><flux:subheading>Auto Score</flux:subheading><p>{{ $application->auto_score }}</p></div>
            <div><flux:subheading>Final Score</flux:subheading><p>{{ $application->final_score ?: '—' }}</p></div>
        </div>
    </flux:card>

    {{-- Graders & Grades --}}
    <flux:card class="space-y-6">
        <flux:heading size="lg">Graders</flux:heading>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Grader</flux:table.column>
                <flux:table.column>Assigned</flux:table.column>
                <flux:table.column>Score</flux:table.column>
                <flux:table.column>Reassign</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach($this->graders as $grader)
                    <flux:table.row wire:key="{{ $grader->id }}">
                        <flux:table.cell>{{ $grader->name }}</flux:table.cell>
                        <flux:table.cell>{{ \Carbon\Carbon::parse($grader->pivot->assigned_at)->diffForHumans() }}</flux:table.cell>
                        <flux:table.cell>
                            @php $grade = $this->grades->firstWhere('user_id', $grader->id); @endphp
                            {{ $grade ? $grade->final_score : '—' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                wire:click="$set('reassignFrom', '{{ $grader->id }}')"
                                size="sm"
                                variant="ghost"
                            >
                                Reassign
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        @if($reassignFrom)
            <div class="flex gap-4 items-end">
                <flux:select wire:model="reassignTo" label="Replace with" class="flex-1">
                    <flux:select.option value="">Select a grader...</flux:select.option>
                    @foreach($this->availableGraders as $grader)
                        <flux:select.option value="{{ $grader->id }}">{{ $grader->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:button wire:click="reassign" variant="primary">Confirm</flux:button>
                <flux:button wire:click="$set('reassignFrom', '')" variant="ghost">Cancel</flux:button>
            </div>
        @endif

        {{-- Grade Details --}}
        @if($this->grades->isNotEmpty())
            <div class="space-y-4">
                <flux:heading size="sm">Grade Breakdown</flux:heading>
                @foreach($this->grades as $grade)
                    <flux:card class="space-y-4">
                        <div class="flex justify-between">
                            <flux:heading size="sm">{{ $grade->user->name }}</flux:heading>
                            <flux:badge>Total: {{ $grade->final_score }}</flux:badge>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            @foreach([
                                'short_term_goal' => 'Short Term Goal',
                                'long_term_goal' => 'Long Term Goal',
                                'received_awards' => 'Awards',
                                'academics' => 'Academics',
                                'other_organizations' => 'Organizations',
                                'volunteer_events' => 'Volunteer Events',
                                'career_progression' => 'Career Progression',
                                'essay_one' => 'Essay One',
                                'essay_two' => 'Essay Two',
                            ] as $key => $label)
                                <div>
                                    <span class="font-medium">{{ $label }}:</span>
                                    {{ $grade->{$key . '_grade'} }} / 20
                                    @if($grade->{$key . '_comments'})
                                        <p class="text-zinc-500 text-xs mt-1">{{ $grade->{$key . '_comments'} }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </flux:card>
                @endforeach
            </div>
        @endif
    </flux:card>
</div>
