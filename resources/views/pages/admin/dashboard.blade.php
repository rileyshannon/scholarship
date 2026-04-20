<?php

use App\Enums\ApplicationStatus;
use App\Enums\EducationLevel;
use App\Enums\FlightTraining;
use App\Enums\Gpa;
use App\Enums\GradeStatus;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public string $scholarshipId = '';

    public function mount(): void
    {
        $active = Scholarship::where('is_active', true)->first();
        $this->scholarshipId = $active?->id ?? '';
    }

    protected function scopedApplications()
    {
        return ScholarshipApplication::query()
            ->when($this->scholarshipId, fn ($q) => $q->where('scholarship_id', $this->scholarshipId));
    }

    #[Computed]
    public function scholarships()
    {
        return Scholarship::orderByDesc('created_at')->get();
    }

    #[Computed]
    public function totalApplications(): int
    {
        return $this->scopedApplications()->count();
    }

    #[Computed]
    public function gradingComplete(): int
    {
        return $this->scopedApplications()->where('status', ApplicationStatus::Completed)->count();
    }

    #[Computed]
    public function awaitingGrading(): int
    {
        return $this->totalApplications - $this->gradingComplete;
    }

    #[Computed]
    public function averageScore(): float
    {
        return round($this->scopedApplications()->where('final_score', '>', 0)->avg('final_score') ?? 0, 1);
    }

    #[Computed]
    public function applicationsPerDay(): array
    {
        return $this->scopedApplications()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($row) => ['date' => $row->date, 'count' => $row->count])
            ->toArray();
    }

    #[Computed]
    public function genderData(): array
    {
        return $this->scopedApplications()
            ->selectRaw('gender, COUNT(*) as count')
            ->groupBy('gender')
            ->get()
            ->map(fn($row) => ['gender' => $row->gender, 'count' => $row->count])
            ->toArray();
    }

    #[Computed]
    public function stateData(): array
    {
        return $this->scopedApplications()
            ->selectRaw('state, COUNT(*) as count')
            ->groupBy('state')
            ->orderByDesc('count')
            ->limit(15)
            ->get()
            ->map(fn($row) => ['state' => $row->state, 'count' => $row->count])
            ->toArray();
    }

    #[Computed]
    public function educationData(): array
    {
        return $this->scopedApplications()
            ->selectRaw('education_level, COUNT(*) as count')
            ->groupBy('education_level')
            ->get()
            ->map(fn($row) => [
                'level' => EducationLevel::from($row->education_level)->label(),
                'count' => $row->count,
            ])
            ->toArray();
    }

    #[Computed]
    public function gpaData(): array
    {
        return $this->scopedApplications()
            ->selectRaw('gpa, COUNT(*) as count')
            ->groupBy('gpa')
            ->get()
            ->map(fn($row) => [
                'gpa' => Gpa::from($row->gpa)->label(),
                'count' => $row->count,
            ])
            ->toArray();
    }

    #[Computed]
    public function flightTrainingData(): array
    {
        return $this->scopedApplications()
            ->selectRaw('flight_training, COUNT(*) as count')
            ->groupBy('flight_training')
            ->get()
            ->map(fn($row) => [
                'training' => FlightTraining::from($row->flight_training)->label(),
                'count' => $row->count,
            ])
            ->toArray();
    }

    public function render()
    {
        return $this->view()->layout('layouts::admin');
    }
};
?>

<div class="space-y-8">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Dashboard</flux:heading>
        <flux:select wire:model.live="scholarshipId" class="w-64">
            <flux:select.option value="">All Scholarships</flux:select.option>
            @foreach($this->scholarships as $scholarship)
                <flux:select.option value="{{ $scholarship->id }}">{{ $scholarship->name }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-4 gap-4">
        <flux:card>
            <flux:text>Total Applications</flux:text>
            <flux:heading size="xl" class="mt-2 tabular-nums">{{ $this->totalApplications }}</flux:heading>
        </flux:card>
        <flux:card>
            <flux:text>Grading Complete</flux:text>
            <flux:heading size="xl" class="mt-2 tabular-nums">{{ $this->gradingComplete }}</flux:heading>
        </flux:card>
        <flux:card>
            <flux:text>Awaiting Grading</flux:text>
            <flux:heading size="xl" class="mt-2 tabular-nums">{{ $this->awaitingGrading }}</flux:heading>
        </flux:card>
        <flux:card>
            <flux:text>Average Score</flux:text>
            <flux:heading size="xl" class="mt-2 tabular-nums">{{ $this->averageScore }}</flux:heading>
        </flux:card>
    </div>

    {{-- Applications Per Day --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Applications Per Day (Last 30 Days)</flux:heading>
        <flux:chart :value="$this->applicationsPerDay" class="aspect-3/1">
            <flux:chart.svg>
                <flux:chart.line field="count" class="text-blue-500"/>
                <flux:chart.area field="count" class="text-blue-200/50"/>
                <flux:chart.axis axis="x" field="date" :format="['month' => 'short', 'day' => 'numeric']">
                    <flux:chart.axis.tick/>
                    <flux:chart.axis.line/>
                </flux:chart.axis>
                <flux:chart.axis axis="y">
                    <flux:chart.axis.grid/>
                    <flux:chart.axis.tick/>
                </flux:chart.axis>
                <flux:chart.cursor/>
            </flux:chart.svg>
            <flux:chart.tooltip>
                <flux:chart.tooltip.heading field="date" :format="['month' => 'long', 'day' => 'numeric']"/>
                <flux:chart.tooltip.value field="count" label="Applications"/>
            </flux:chart.tooltip>
        </flux:chart>
    </flux:card>

    {{-- Gender & GPA --}}
    <div class="grid grid-cols-2 gap-4">
        <flux:card class="space-y-4">
            <flux:heading size="lg">Gender Breakdown</flux:heading>
            <flux:chart :value="$this->genderData" class="aspect-3/2">
                <flux:chart.svg>
                    <flux:chart.bar field="count" class="text-blue-500"/>
                    <flux:chart.axis axis="x" field="gender">
                        <flux:chart.axis.tick/>
                        <flux:chart.axis.line/>
                    </flux:chart.axis>
                    <flux:chart.axis axis="y">
                        <flux:chart.axis.grid/>
                        <flux:chart.axis.tick/>
                    </flux:chart.axis>
                </flux:chart.svg>
                <flux:chart.tooltip>
                    <flux:chart.tooltip.value field="count" label="Applicants"/>
                </flux:chart.tooltip>
            </flux:chart>
        </flux:card>

        <flux:card class="space-y-4">
            <flux:heading size="lg">GPA Distribution</flux:heading>
            <flux:chart :value="$this->gpaData" class="aspect-3/2">
                <flux:chart.svg>
                    <flux:chart.bar field="count" class="text-green-500"/>
                    <flux:chart.axis axis="x" field="gpa">
                        <flux:chart.axis.tick/>
                        <flux:chart.axis.line/>
                    </flux:chart.axis>
                    <flux:chart.axis axis="y">
                        <flux:chart.axis.grid/>
                        <flux:chart.axis.tick/>
                    </flux:chart.axis>
                </flux:chart.svg>
                <flux:chart.tooltip>
                    <flux:chart.tooltip.value field="count" label="Applicants"/>
                </flux:chart.tooltip>
            </flux:chart>
        </flux:card>
    </div>

    {{-- Education Level --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Education Level</flux:heading>
        <flux:chart :value="$this->educationData" class="aspect-3/1">
            <flux:chart.svg>
                <flux:chart.bar field="count" class="text-purple-500"/>
                <flux:chart.axis axis="x" field="level">
                    <flux:chart.axis.tick/>
                    <flux:chart.axis.line/>
                </flux:chart.axis>
                <flux:chart.axis axis="y">
                    <flux:chart.axis.grid/>
                    <flux:chart.axis.tick/>
                </flux:chart.axis>
            </flux:chart.svg>
            <flux:chart.tooltip>
                <flux:chart.tooltip.value field="count" label="Applicants"/>
            </flux:chart.tooltip>
        </flux:chart>
    </flux:card>

    {{-- Flight Training --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Flight Training Level</flux:heading>
        <flux:chart :value="$this->flightTrainingData" class="aspect-3/1">
            <flux:chart.svg>
                <flux:chart.bar field="count" class="text-sky-500"/>
                <flux:chart.axis axis="x" field="training">
                    <flux:chart.axis.tick/>
                    <flux:chart.axis.line/>
                </flux:chart.axis>
                <flux:chart.axis axis="y">
                    <flux:chart.axis.grid/>
                    <flux:chart.axis.tick/>
                </flux:chart.axis>
            </flux:chart.svg>
            <flux:chart.tooltip>
                <flux:chart.tooltip.value field="count" label="Applicants"/>
            </flux:chart.tooltip>
        </flux:chart>
    </flux:card>

    {{-- Top 15 States --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Top 15 States</flux:heading>
        <flux:chart :value="$this->stateData" class="aspect-3/1">
            <flux:chart.svg>
                <flux:chart.bar field="count" class="text-orange-500"/>
                <flux:chart.axis axis="x" field="state">
                    <flux:chart.axis.tick/>
                    <flux:chart.axis.line/>
                </flux:chart.axis>
                <flux:chart.axis axis="y">
                    <flux:chart.axis.grid/>
                    <flux:chart.axis.tick/>
                </flux:chart.axis>
            </flux:chart.svg>
            <flux:chart.tooltip>
                <flux:chart.tooltip.value field="count" label="Applicants"/>
            </flux:chart.tooltip>
        </flux:chart>
    </flux:card>
</div>
