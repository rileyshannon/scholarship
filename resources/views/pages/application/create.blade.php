<?php

use App\Actions\SubmitApplication;
use App\Enums\{EducationLevel, FlightInstruction, FlightTraining, Gender, Gpa};
use App\Models\Scholarship;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public int $step = 1;
    public int $totalSteps = 6;

    // Step 1 - Personal Info
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $city = '';
    public string $state = '';
    public string $gender = '';
    public bool $ppot_member = false;
    public string $ppot_mentor = '';
    public bool $prior_applicant = false;
    public string $reference = '';

    // Step 2 - Flight Training
    public string $flight_school = '';
    public string $flight_training = '';
    public string $total_time = '';
    public string $flight_instruction = '';

    // Step 3 - Education
    public string $education_level = '';
    public string $school = '';
    public string $graduation_month = '';
    public string $graduation_year = '';
    public string $gpa = '';
    public string $academics = '';

    // Step 4 - Goals & Awards
    public string $short_term_goal = '';
    public string $long_term_goal = '';
    public bool $has_received_awards = false;
    public string $received_awards = '';

    // Step 5 - Community & Employment
    public string $other_organizations = '';
    public string $volunteer_events = '';
    public array $employment_histories = [
        ['employer_name' => '', 'position' => '', 'length' => ''],
    ];
    public string $career_aspirations = '';

    // Step 6 - Essays
    public string $essay_one = '';
    public string $essay_two = '';

    protected function stepRules(): array
    {
        return [
            1 => [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'phone' => ['required', 'string', 'max:20'],
                'city' => ['required', 'string', 'max:255'],
                'state' => ['required', 'string', 'max:2'],
                'gender' => ['required', 'string'],
                'ppot_member' => ['required', 'boolean'],
                'ppot_mentor' => ['required_if:ppot_member,true', 'string', 'max:255'],
                'prior_applicant' => ['required', 'boolean'],
                'reference' => ['required', 'string'],
            ],
            2 => [
                'flight_school' => ['required', 'string'],
                'flight_training' => ['required', 'string'],
                'total_time' => ['required', 'string'],
                'flight_instruction' => ['required', 'string'],
            ],
            3 => [
                'education_level' => ['required', 'string'],
                'school' => ['required', 'string', 'max:255'],
                'graduation_month' => ['required', 'string'],
                'graduation_year' => ['required', 'integer'],
                'gpa' => ['required', 'string'],
                'academics' => ['required', 'string', 'max:3000'],
            ],
            4 => [
                'short_term_goal' => ['required', 'string', 'max:3000'],
                'long_term_goal' => ['required', 'string', 'max:3000'],
                'has_received_awards' => ['required', 'boolean'],
                'received_awards' => ['nullable', 'required_if:has_received_awards,true', 'string', 'max:3000'],
            ],
            5 => [
                'other_organizations' => ['nullable', 'string', 'max:3000'],
                'volunteer_events' => ['nullable', 'string', 'max:3000'],
                'employment_histories' => ['nullable', 'array', 'max:5'],
                'employment_histories.*.employer_name' => ['required_with:employment_histories', 'string', 'max:255'],
                'employment_histories.*.position' => ['required_with:employment_histories', 'string', 'max:255'],
                'employment_histories.*.length' => ['required_with:employment_histories', 'string'],
                'career_aspirations' => ['required', 'string', 'max:3000'],
            ],
            6 => [
                'essay_one' => ['required', 'string', 'max:5000'],
                'essay_two' => ['required', 'string', 'max:5000'],
            ],
        ];
    }

    public function nextStep(): void
    {
//        $this->validate($this->stepRules()[$this->step]);
        $this->step++;
    }

    public function previousStep(): void
    {
        $this->step--;
    }

    public function addEmployment(): void
    {
        if (count($this->employment_histories) < 5) {
            $this->employment_histories[] = ['employer_name' => '', 'position' => '', 'length' => ''];
        }
    }

    public function removeEmployment(): void
    {
        array_splice($this->employment_histories, $index, 1);
    }

    public function submit(): void
    {
        $this->validate(collect($this->stepRules())->flatten(1)->all());

        (new SubmitApplication)->handle([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'city' => $this->city,
            'state' => $this->state,
            'gender' => $this->gender,
            'ppot_member' => $this->ppot_member,
            'ppot_mentor' => $this->ppot_mentor,
            'prior_applicant' => $this->prior_applicant,
            'reference' => $this->reference,
            'flight_school' => $this->flight_school,
            'flight_training' => $this->flight_training,
            'total_time' => $this->total_time,
            'flight_instruction' => $this->flight_instruction,
            'education_level' => $this->education_level,
            'school' => $this->school,
            'graduation_month' => $this->graduation_month,
            'graduation_year' => $this->graduation_year,
            'gpa' => $this->gpa,
            'academics' => $this->academics,
            'short_term_goal' => $this->short_term_goal,
            'long_term_goal' => $this->long_term_goal,
            'has_received_awards' => $this->has_received_awards,
            'received_awards' => $this->received_awards,
            'other_organizations' => $this->other_organizations,
            'volunteer_events' => $this->volunteer_events,
            'employment_histories' => $this->employment_histories,
            'career_aspirations' => $this->career_aspirations,
            'essay_one' => $this->essay_one,
            'essay_two' => $this->essay_two,
        ]);

        Flux::toast(
            text: 'We have received your scholarship application!',
            heading: 'Success!',
            variant: 'success',
        );

        $this->redirectRoute('application.success', navigate: true);
    }

    #[Computed]
    public function scholarship(): ?Scholarship
    {
        return Scholarship::where('is_active', true)->first();
    }

    public function mount(): void
    {
        if (!$this->scholarship) {
            $this->redirect(route('index'), navigate: true);
        }
    }

    public function render()
    {
        return $this->view()->layout('layouts::guest');
    }
};
?>

<div>
    @if(now()->isBetween($this->scholarship->opens_at, $this->scholarship->closes_at))
    <div class="mb-8">
        <flux:progress :value="($step / $totalSteps) * 100" color="cyan"/>
    </div>

    <form wire:submit="submit">

        @if($step === 1)
            <div class="space-y-4">
                <flux:input wire:model="name" label="Full Name"/>
                <flux:input wire:model="email" label="Email Address" type="email"/>
                <flux:input wire:model="phone" label="Phone Number" type="tel"/>
                <flux:input wire:model="city" label="City"/>
                <flux:select wire:model="state" label="State">
                    @foreach(\App\Enums\State::cases() as $option)
                        <flux:select.option value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model="gender" label="Gender">
                    @foreach(Gender::cases() as $option)
                        <flux:select.option value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:switch wire:model.live="ppot_member" label="Are you currently a PPOT Member?" align="left"/>

                @if($ppot_member)
                    <flux:input wire:model="ppot_mentor" label="Who is your mentor?"
                                description="If you do not have a mentor, please write N/A."/>
                @endif

                <flux:switch wire:model="prior_applicant" label="Have you applied for a PPOT Scholarship in the past?"
                             align="left"/>

                <flux:select wire:model="reference" label="How did you hear about this scholarship?">
                    @foreach(\App\Enums\Reference::cases() as $option)
                        <flux:select.option value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        @endif
        @if($step === 2)
            <div class="space-y-6">
                <flux:select wire:model="flight_school" label="Flight School">
                    {{--                        @foreach(\App\Enums\FlightSchool::cases() as $option)--}}
                    {{--                            <flux:select.option value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>--}}
                    {{--                        @endforeach--}}
                </flux:select>
                <flux:select wire:model="flight_training" label="Highest Level of Flight Training Completed">
                    @foreach(FlightTraining::cases() as $option)
                        <flux:select.option value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select wire:model="total_time" label="Total Flight Time (excluding simulator time)">
                    {{--                        @foreach(\App\Enums\FlightTime::cases() as $option)--}}
                    {{--                            <flux:select.option value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>--}}
                    {{--                        @endforeach--}}
                </flux:select>
                <flux:select wire:model="flight_instruction" label="Recent Flight Instruction Received or Given"
                             description="If you have earned a flight certificate or rating in the previous 6 or 12 months, please select that option.">
                    @foreach(FlightInstruction::cases() as $option)
                        <flux:select.option value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        @endif

        {{-- Step 3: Education --}}
        @if($step === 3)
            <div class="space-y-6">
                <flux:select wire:model="education_level" label="Highest Level of Education Completed">
                    @foreach(EducationLevel::cases() as $option)
                        <flux:select.option value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input wire:model="school" label="School Name"/>
                <div class="grid grid-cols-2 gap-4">
                    <flux:select wire:model="graduation_month" label="Graduation Month">
{{--                        @foreach(\App\Enums\Month::cases() as $option)--}}
{{--                            <flux:select.option value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>--}}
{{--                        @endforeach--}}
                    </flux:select>
                    <flux:select wire:model="graduation_year" label="Graduation Year">
                        @foreach(range(date('Y') + 8, 1980) as $year)
                            <flux:select.option value="{{ $year }}">{{ $year }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <flux:select wire:model="gpa" label="Most Recent Cumulative GPA (4.0 scale)">
                    @foreach(Gpa::cases() as $option)
                        <flux:select.option value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:textarea wire:model="academics" label="Academic Honors & Scholarships"
                               description="If you do not have any information to fill in, please write N/A." rows="4"/>
            </div>
        @endif

        {{-- Step 4: Goals & Awards --}}
        @if($step === 4)
            <div class="space-y-6">
                <flux:textarea wire:model="short_term_goal" label="Short Term Aviation Goal (next 12 months)"
                               description="If undecided or not applicable please write N/A." rows="4"/>
                <flux:textarea wire:model="long_term_goal" label="Long Term Aviation Goal (next 10-20 years)"
                               description="If undecided or not applicable please write N/A." rows="4"/>
                <flux:switch wire:model.live="has_received_awards"
                             label="Have you ever received any aviation related awards?"
                             description="Ex: WAI/NGPA/OBAP/LPA Scholarships, Safety Awards, Flight School or Flight Team Awards, or Similar"/>
                @if($has_received_awards)
                    <flux:textarea wire:model="received_awards" label="Please list and describe your aviation award(s)"
                                   rows="4"/>
                @endif
            </div>
        @endif

        {{-- Step 5: Community & Employment --}}
        @if($step === 5)
            <div class="space-y-6">
                <flux:textarea wire:model="other_organizations" label="Other Professional or Aviation Organizations"
                               description="If you do not have any information to fill in, please write N/A." rows="4"/>
                <flux:textarea wire:model="volunteer_events" label="Recent Volunteer Events (previous five years)"
                               description="If you do not have any information to fill in, please write N/A." rows="4"/>

                <div class="space-y-4">
                    <flux:heading size="sm">Employment History</flux:heading>
                    @foreach($employment_histories as $index => $employment)
                        <div class="grid grid-cols-3 gap-4 items-end">
                            <flux:input wire:model="employment_histories.{{ $index }}.employer_name"
                                        label="Employer Name"/>
                            <flux:input wire:model="employment_histories.{{ $index }}.position" label="Position"/>
                            <flux:select wire:model="employment_histories.{{ $index }}.length" label="Length">
{{--                                @foreach(\App\Enums\EmploymentLength::cases() as $option)--}}
{{--                                    <flux:select.option--}}
{{--                                        value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>--}}
{{--                                @endforeach--}}
                            </flux:select>
                            @if($index > 0)
                                <div class="col-span-3 flex justify-end">
                                    <flux:button wire:click="removeEmployment({{ $index }})" variant="ghost" size="sm">
                                        Remove
                                    </flux:button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                    @if(count($employment_histories) < 5)
                        <flux:button wire:click="addEmployment" variant="ghost" size="sm">+ Add Employment</flux:button>
                    @endif
                </div>

                <flux:textarea wire:model="career_aspirations" label="Career Progression & Aspirations" rows="4"/>
            </div>
        @endif

        {{-- Step 6: Essays --}}
        @if($step === 6)
            <div class="space-y-6">
                <flux:textarea wire:model="essay_one" label="Essay One"
                               description="In 500 words or less, please describe why you believe you should be selected as one of our scholarship recipients."
                               rows="10"/>
                <flux:textarea wire:model="essay_two" label="Essay Two"
                               description="In 500 words or less, please describe what being a good mentee or mentor means to you."
                               rows="10"/>

                <flux:callout variant="warning" icon="exclamation-triangle">
                    <flux:callout.heading>Before You Submit</flux:callout.heading>
                    <flux:callout.text>
                        Once submitted, you will not be able to edit or delete your application. By clicking "Submit
                        Application" you affirm that all information is complete and truthful. Any misrepresented or
                        falsified entries will result in disqualification.
                    </flux:callout.text>
                </flux:callout>
            </div>
        @endif

        <div class="flex justify-between mt-8">
            @if($step > 1)
                <flux:button wire:click="previousStep" variant="ghost">Back</flux:button>
            @else
                <div></div>
            @endif

            @if($step < $totalSteps)
                <flux:button wire:click="nextStep">Next Step</flux:button>
            @else
                <flux:button type="submit" variant="primary">Submit Application</flux:button>
            @endif
        </div>
    </form>
    @else
        <div class="max-w-lg mx-auto py-16 space-y-4 text-center">
            <flux:heading size="xl">Applications Are Closed</flux:heading>
            <flux:subheading>
                @if(now()->isBefore($this->scholarship->opens_at))
                    Applications for the {{ $this->scholarship->name }} open on
                    {{ $this->scholarship->opens_at->format('F j, Y \a\t g:i A T') }}.
                @else
                    Applications for the {{ $this->scholarship->name }} closed on
                    {{ $this->scholarship->closes_at->format('F j, Y \a\t g:i A T') }}.
                    Award announcements will be made on {{ $this->scholarship->award_date->format('F j, Y') }}.
                @endif
            </flux:subheading>
            <flux:button href="{{ route('index') }}" variant="ghost">Return Home</flux:button>
        </div>
    @endif
</div>
