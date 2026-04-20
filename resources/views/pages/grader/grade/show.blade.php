<?php

use App\Actions\CalculateFinalScore;
use App\Actions\CalculateGradeScore;
use App\Enums\GradeStatus;
use App\Models\ScholarshipApplication;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Flux\Flux;

new class extends Component {
    public ScholarshipApplication $application;

    public bool $readOnly = false;
    public int $short_term_goal_grade = 0;
    public string $short_term_goal_comments = '';
    public int $long_term_goal_grade = 0;
    public string $long_term_goal_comments = '';
    public int $received_awards_grade = 0;
    public string $received_awards_comments = '';
    public int $academics_grade = 0;
    public string $academics_comments = '';
    public int $other_organizations_grade = 0;
    public string $other_organizations_comments = '';
    public int $volunteer_events_grade = 0;
    public string $volunteer_events_comments = '';
    public int $career_progression_grade = 0;
    public string $career_progression_comments = '';
    public int $essay_one_grade = 0;
    public string $essay_one_comments = '';
    public int $essay_two_grade = 0;
    public string $essay_two_comments = '';

    public function mount(ScholarshipApplication $application): void
    {
        $alreadyGraded = $application->grades()
            ->where('user_id', auth()->id())
            ->where('status', GradeStatus::Active)
            ->exists();

        if ($alreadyGraded) {
            $this->readOnly = true;
            // Load their existing grade into the properties
            $grade = $application->grades()
                ->where('user_id', auth()->id())
                ->where('status', GradeStatus::Active)
                ->first();

            $this->fill($grade->only([
                'short_term_goal_grade', 'short_term_goal_comments',
                'long_term_goal_grade', 'long_term_goal_comments',
                'received_awards_grade', 'received_awards_comments',
                'academics_grade', 'academics_comments',
                'other_organizations_grade', 'other_organizations_comments',
                'volunteer_events_grade', 'volunteer_events_comments',
                'career_progression_grade', 'career_progression_comments',
                'essay_one_grade', 'essay_one_comments',
                'essay_two_grade', 'essay_two_comments',
            ]));
        }
    }

    protected function rules(): array
    {
        return [
            'short_term_goal_grade'         => ['required', 'integer', 'min:0', 'max:20'],
            'short_term_goal_comments'      => ['nullable', 'string', 'max:1000'],
            'long_term_goal_grade'          => ['required', 'integer', 'min:0', 'max:20'],
            'long_term_goal_comments'       => ['nullable', 'string', 'max:1000'],
            'received_awards_grade'         => ['required', 'integer', 'min:0', 'max:20'],
            'received_awards_comments'      => ['nullable', 'string', 'max:1000'],
            'academics_grade'               => ['required', 'integer', 'min:0', 'max:20'],
            'academics_comments'            => ['nullable', 'string', 'max:1000'],
            'other_organizations_grade'     => ['required', 'integer', 'min:0', 'max:20'],
            'other_organizations_comments'  => ['nullable', 'string', 'max:1000'],
            'volunteer_events_grade'        => ['required', 'integer', 'min:0', 'max:20'],
            'volunteer_events_comments'     => ['nullable', 'string', 'max:1000'],
            'career_progression_grade'      => ['required', 'integer', 'min:0', 'max:20'],
            'career_progression_comments'   => ['nullable', 'string', 'max:1000'],
            'essay_one_grade'               => ['required', 'integer', 'min:0', 'max:20'],
            'essay_one_comments'            => ['nullable', 'string', 'max:1000'],
            'essay_two_grade'               => ['required', 'integer', 'min:0', 'max:20'],
            'essay_two_comments'            => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function submit(): void
    {
        $this->validate();

        $data = $this->only([
            'short_term_goal_grade', 'short_term_goal_comments',
            'long_term_goal_grade', 'long_term_goal_comments',
            'received_awards_grade', 'received_awards_comments',
            'academics_grade', 'academics_comments',
            'other_organizations_grade', 'other_organizations_comments',
            'volunteer_events_grade', 'volunteer_events_comments',
            'career_progression_grade', 'career_progression_comments',
            'essay_one_grade', 'essay_one_comments',
            'essay_two_grade', 'essay_two_comments',
        ]);

        $this->application->grades()->create([
            ...$data,
            'user_id'     => auth()->id(),
            'final_score' => (new CalculateGradeScore)->handle($data),
        ]);

        if ($this->application->grades()->where('status', GradeStatus::Active)->count() === 3) {
            (new CalculateFinalScore)->handle($this->application);
        }

        Flux::toast(
            text: 'Grade successfully submitted',
            heading: 'Success!',
            variant: 'success',
        );

        $this->redirectRoute('grader.dashboard', navigate: true);
    }
};
?>

<div class="space-y-8">
    <flux:heading size="xl">Grade Application</flux:heading>

    {{-- Application Details (anonymized) --}}
    <flux:card class="space-y-6">
        <flux:heading size="lg">Applicant Information</flux:heading>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <flux:subheading>Flight School</flux:subheading>
                <p>{{ $application->flight_school }}</p>
            </div>
            <div>
                <flux:subheading>Flight Training</flux:subheading>
                <p>{{ $application->flight_training }}</p>
            </div>
            <div>
                <flux:subheading>Total Flight Time</flux:subheading>
                <p>{{ $application->total_time }}</p>
            </div>
            <div>
                <flux:subheading>Flight Instruction</flux:subheading>
                <p>{{ $application->flight_instruction }}</p>
            </div>
            <div>
                <flux:subheading>Education Level</flux:subheading>
                <p>{{ $application->education_level }}</p>
            </div>
            <div>
                <flux:subheading>GPA</flux:subheading>
                <p>{{ $application->gpa }}</p>
            </div>
        </div>

        <div>
            <flux:subheading>Short Term Goal</flux:subheading>
            <p>{{ $application->short_term_goal }}</p>
        </div>
        <div>
            <flux:subheading>Long Term Goal</flux:subheading>
            <p>{{ $application->long_term_goal }}</p>
        </div>
        <div>
            <flux:subheading>Awards</flux:subheading>
            <p>{{ $application->received_awards ?? 'None' }}</p>
        </div>
        <div>
            <flux:subheading>Academics</flux:subheading>
            <p>{{ $application->academics }}</p>
        </div>
        <div>
            <flux:subheading>Other Organizations</flux:subheading>
            <p>{{ $application->other_organizations ?? 'None' }}</p>
        </div>
        <div>
            <flux:subheading>Volunteer Events</flux:subheading>
            <p>{{ $application->volunteer_events ?? 'None' }}</p>
        </div>

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

        <div>
            <flux:subheading>Career Aspirations</flux:subheading>
            <p>{{ $application->career_aspirations }}</p>
        </div>
        <div>
            <flux:subheading>Essay One</flux:subheading>
            <p class="whitespace-pre-wrap">{{ $application->essay_one }}</p>
        </div>
        <div>
            <flux:subheading>Essay Two</flux:subheading>
            <p class="whitespace-pre-wrap">{{ $application->essay_two }}</p>
        </div>
    </flux:card>

    {{-- Grading Form --}}
    <form wire:submit="submit" class="space-y-6">
        <flux:heading size="lg">Scores</flux:heading>

        {{-- Short Term Goal --}}
        <flux:card class="space-y-4">
            <flux:heading size="sm">Short Term Goal</flux:heading>
            <p class="text-sm italic text-zinc-500">{{ $application->short_term_goal }}</p>
            <flux:separator />
            <div class="grid grid-cols-2 gap-4">
                <flux:input type="number" wire:model="short_term_goal_grade" label="Grade (0-2)" min="0" max="2" :disabled="$readOnly" />
                <flux:textarea wire:model="short_term_goal_comments" label="Comments" rows="2" :disabled="$readOnly" />
            </div>
            <flux:accordion variant="reverse" transition>
                <flux:accordion.item heading="Rubric Reference">
                    <ul class="list-disc ml-5 space-y-1 text-sm">
                        <li>2 points = quantifiable goal (i.e. first solo, certificate, rating, knowledge exam or another similar aviation milestone)</li>
                        <li>1 point = an abstract goal that does not include an aviation milestone</li>
                        <li>0 points = no goal listed or a goal has no direct relation to aviation</li>
                    </ul>
                </flux:accordion.item>
            </flux:accordion>
        </flux:card>

        {{-- Long Term Goal --}}
        <flux:card class="space-y-4">
            <flux:heading size="sm">Long Term Goal</flux:heading>
            <p class="text-sm italic text-zinc-500">{{ $application->long_term_goal }}</p>
            <flux:separator />
            <div class="grid grid-cols-2 gap-4">
                <flux:input type="number" wire:model="long_term_goal_grade" label="Grade (0-1)" min="0" max="1" :disabled="$readOnly" />
                <flux:textarea wire:model="long_term_goal_comments" label="Comments" rows="2" :disabled="$readOnly" />
            </div>
            <flux:accordion variant="reverse" transition>
                <flux:accordion.item heading="Rubric Reference">
                    <ul class="list-disc ml-5 space-y-1 text-sm">
                        <li>1 point = applicant mentions a career goal within aviation</li>
                        <li>0 points = no goal listed or a goal has no direct relation to aviation</li>
                    </ul>
                </flux:accordion.item>
            </flux:accordion>
        </flux:card>

        {{-- Received Awards --}}
        <flux:card class="space-y-4">
            <flux:heading size="sm">Awards & Recognition</flux:heading>
            <p class="text-sm italic text-zinc-500">{{ $application->received_awards ?? 'None listed' }}</p>
            <flux:separator />
            <div class="grid grid-cols-2 gap-4">
                <flux:input type="number" wire:model="received_awards_grade" label="Grade (0-5)" min="0" max="5" :disabled="$readOnly" />
                <flux:textarea wire:model="received_awards_comments" label="Comments" rows="2" :disabled="$readOnly" />
            </div>
            <flux:accordion variant="reverse" transition>
                <flux:accordion.item heading="Rubric Reference">
                    <ul class="list-disc ml-5 space-y-1 text-sm">
                        <li>5 points = Applicant mentions two or more awards within aviation</li>
                        <li>3 points = Applicant mentions an award within aviation</li>
                        <li>0 points = Applicant does not mention any awards within aviation</li>
                    </ul>
                </flux:accordion.item>
            </flux:accordion>
        </flux:card>

        {{-- Academics --}}
        <flux:card class="space-y-4">
            <flux:heading size="sm">Academics</flux:heading>
            <p class="text-sm italic text-zinc-500">{{ $application->academics }}</p>
            <flux:separator />
            <div class="grid grid-cols-2 gap-4">
                <flux:input type="number" wire:model="academics_grade" label="Grade (0-4)" min="0" max="4" :disabled="$readOnly" />
                <flux:textarea wire:model="academics_comments" label="Comments" rows="2" :disabled="$readOnly" />
            </div>
            <flux:accordion variant="reverse" transition>
                <flux:accordion.item heading="Rubric Reference">
                    <ul class="list-disc ml-5 space-y-1 text-sm">
                        <li>4 points = Applicant mentions two or more notable achievements within academia</li>
                        <li>2 points = Applicant mentions one notable achievement within academia</li>
                        <li>0 points = Applicant does not mention any achievements within academia</li>
                    </ul>
                </flux:accordion.item>
            </flux:accordion>
        </flux:card>

        {{-- Other Organizations --}}
        <flux:card class="space-y-4">
            <flux:heading size="sm">Other Organizations</flux:heading>
            <p class="text-sm italic text-zinc-500">{{ $application->other_organizations ?? 'None listed' }}</p>
            <flux:separator />
            <div class="grid grid-cols-2 gap-4">
                <flux:input type="number" wire:model="other_organizations_grade" label="Grade (0-5)" min="0" max="5" :disabled="$readOnly" />
                <flux:textarea wire:model="other_organizations_comments" label="Comments" rows="2" :disabled="$readOnly" />
            </div>
            <flux:accordion variant="reverse" transition>
                <flux:accordion.item heading="Rubric Reference">
                    <ul class="list-disc ml-5 space-y-1 text-sm">
                        <li>5 points = Holds a leadership position in one org AND is a member of at least two other organizations</li>
                        <li>4 points = Holds a leadership position in one org AND is a member of at least one other organization</li>
                        <li>3 points = Holds a leadership position in one org OR is a member of at least two organizations</li>
                        <li>2 points = Member of at least one aviation organization</li>
                        <li>1 point = Member of at least one non-aviation professional organization</li>
                        <li>0 points = Does not mention any professional or aviation organization</li>
                    </ul>
                </flux:accordion.item>
            </flux:accordion>
        </flux:card>

        {{-- Volunteer Events --}}
        <flux:card class="space-y-4">
            <flux:heading size="sm">Volunteer Events</flux:heading>
            <p class="text-sm italic text-zinc-500">{{ $application->volunteer_events ?? 'None listed' }}</p>
            <flux:separator />
            <div class="grid grid-cols-2 gap-4">
                <flux:input type="number" wire:model="volunteer_events_grade" label="Grade (0-10)" min="0" max="10" :disabled="$readOnly" />
                <flux:textarea wire:model="volunteer_events_comments" label="Comments" rows="2" :disabled="$readOnly" />
            </div>
            <flux:accordion variant="reverse" transition>
                <flux:accordion.item heading="Rubric Reference">
                    <ul class="list-disc ml-5 space-y-1 text-sm">
                        <li>10 points = 3+ examples of community service AND correlates at least one to a leadership position</li>
                        <li>9 points = 3+ examples of community service AND correlates at least one to a current membership</li>
                        <li>8 points = 3+ examples within the last 2 years OR one example with a leadership position within last 2 years</li>
                        <li>7 points = 3+ examples of community service within the last 5 years</li>
                        <li>6 points = One example of community service 0-2 years ago</li>
                        <li>5 points = One example of community service 3-5 years ago</li>
                        <li>0 points = No examples of community service</li>
                    </ul>
                </flux:accordion.item>
            </flux:accordion>
        </flux:card>

        {{-- Career Progression --}}
        <flux:card class="space-y-4">
            <flux:heading size="sm">Career Progression</flux:heading>
            <p class="text-sm italic text-zinc-500">{{ $application->career_aspirations }}</p>
            <flux:separator />
            <div class="grid grid-cols-2 gap-4">
                <flux:input type="number" wire:model="career_progression_grade" label="Grade (0-10)" min="0" max="10" :disabled="$readOnly" />
                <flux:textarea wire:model="career_progression_comments" label="Comments" rows="2" :disabled="$readOnly" />
            </div>
            <flux:accordion variant="reverse" transition>
                <flux:accordion.item heading="Rubric Reference">
                    <ul class="list-disc ml-5 space-y-1 text-sm">
                        <li>10 points = Promotional element in work history AND describes how work experience applies to future aviation endeavors</li>
                        <li>8 points = Promotional element in work history OR describes how work experience applies to future aviation endeavors</li>
                        <li>6 points = Lists one example of aviation related work history</li>
                        <li>5 points = Lists work history but no aviation related examples</li>
                        <li>1-5 points = No work history but gives valid reason (subject to grader discretion)</li>
                        <li>0 points = No work history and does not give a valid reason</li>
                    </ul>
                </flux:accordion.item>
            </flux:accordion>
        </flux:card>

        {{-- Essay One --}}
        <flux:card class="space-y-4">
            <flux:heading size="sm">Essay One</flux:heading>
            <p class="text-sm italic text-zinc-500 whitespace-pre-wrap">{{ $application->essay_one }}</p>
            <flux:separator />
            <div class="grid grid-cols-2 gap-4">
                <flux:input type="number" wire:model="essay_one_grade" label="Grade (0-20)" min="0" max="20" :disabled="$readOnly" />
                <flux:textarea wire:model="essay_one_comments" label="Comments" rows="2" :disabled="$readOnly" />
            </div>
            <flux:accordion variant="reverse" transition>
                <flux:accordion.item heading="Rubric Reference">
                    <div class="space-y-4 text-sm">
                        <div>
                            <flux:heading size="sm">Grammar and Presentation (2pts)</flux:heading>
                            <ul class="list-disc ml-5 space-y-1">
                                <li>2 points = No grammatical errors and proper paragraph structure</li>
                                <li>1 point = 3 or less grammatical errors and proper paragraph structure</li>
                                <li>0 points = Egregious errors or does not utilize proper paragraph structure</li>
                            </ul>
                        </div>
                        <div>
                            <flux:heading size="sm">Topic (2pts)</flux:heading>
                            <ul class="list-disc ml-5 space-y-1">
                                <li>2 points = Answered the question entirely and remains on topic</li>
                                <li>0 points = Did not answer entirely and/or did not remain on topic</li>
                            </ul>
                        </div>
                        <div>
                            <flux:heading size="sm">Content (7pts)</flux:heading>
                            <ul class="list-disc ml-5 space-y-1">
                                <li>7 points = Explains why they should be selected, how the scholarship will help, what steps they'll take, and expanded on previous answers</li>
                                <li>3-5 points = Some of the above criteria were met</li>
                                <li>0 points = None of the above criteria were met</li>
                            </ul>
                        </div>
                        <div>
                            <flux:heading size="sm">Hardship or Perseverance (3pts)</flux:heading>
                            <ul class="list-disc ml-5 space-y-1">
                                <li>3 points = Mentions a hardship AND how they did or will overcome it OR demonstrates drive to persevere</li>
                                <li>1 point = Mentions a hardship but no mention of how they overcame it</li>
                                <li>0 points = No element of hardship or perseverance</li>
                            </ul>
                        </div>
                        <div>
                            <flux:heading size="sm">Authenticity & X-Factor (6pts)</flux:heading>
                            <ul class="list-disc ml-5 space-y-1">
                                <li>6 points (Excellent) = Very personal and genuine, clear details from own life, passion comes through strongly, stands out as exceptional</li>
                                <li>4-5 points (Good) = Shows effort with some personal detail, mostly authentic, shows promise</li>
                                <li>2-3 points (Fair) = Few personal details, somewhat generic, could apply to many applicants</li>
                                <li>0-1 points (Poor) = Vague, impersonal, or generic, little effort to share a real story</li>
                            </ul>
                        </div>
                    </div>
                </flux:accordion.item>
            </flux:accordion>
        </flux:card>

        {{-- Essay Two --}}
        <flux:card class="space-y-4">
            <flux:heading size="sm">Essay Two</flux:heading>
            <p class="text-sm italic text-zinc-500 whitespace-pre-wrap">{{ $application->essay_two }}</p>
            <flux:separator />
            <div class="grid grid-cols-2 gap-4">
                <flux:input type="number" wire:model="essay_two_grade" label="Grade (0-20)" min="0" max="20" :disabled="$readOnly" />
                <flux:textarea wire:model="essay_two_comments" label="Comments" rows="2" :disabled="$readOnly" />
            </div>
            <flux:accordion variant="reverse" transition>
                <flux:accordion.item heading="Rubric Reference">
                    <div class="space-y-4 text-sm">
                        <div>
                            <flux:heading size="sm">Grammar and Presentation (2pts)</flux:heading>
                            <ul class="list-disc ml-5 space-y-1">
                                <li>2 points = No grammatical errors and proper paragraph structure</li>
                                <li>1 point = 3 or less grammatical errors and proper paragraph structure</li>
                                <li>0 points = Egregious errors or does not utilize proper paragraph structure</li>
                            </ul>
                        </div>
                        <div>
                            <flux:heading size="sm">Topic (2pts)</flux:heading>
                            <ul class="list-disc ml-5 space-y-1">
                                <li>2 points = Answered the question entirely and remains on topic</li>
                                <li>0 points = Did not answer entirely and/or did not remain on topic</li>
                            </ul>
                        </div>
                        <div>
                            <flux:heading size="sm">Content (10pts)</flux:heading>
                            <ul class="list-disc ml-5 space-y-1">
                                <li>10 points = Explains what being a good mentee/mentor means, how mentorship affects growth, expands from own life, includes element of PPOT values</li>
                                <li>8 points = Explains what being a good mentee/mentor means, how mentorship affects growth, expands from own life</li>
                                <li>2-6 points = Some of the above criteria were met</li>
                                <li>0 points = None of the above criteria were met</li>
                            </ul>
                        </div>
                        <div>
                            <flux:heading size="sm">Authenticity & X-Factor (6pts)</flux:heading>
                            <ul class="list-disc ml-5 space-y-1">
                                <li>6 points (Excellent) = Very personal and genuine, clear details from own life, passion comes through strongly, stands out as exceptional</li>
                                <li>4-5 points (Good) = Shows effort with some personal detail, mostly authentic, shows promise</li>
                                <li>2-3 points (Fair) = Few personal details, somewhat generic, could apply to many applicants</li>
                                <li>0-1 points (Poor) = Vague, impersonal, or generic, little effort to share a real story</li>
                            </ul>
                        </div>
                    </div>
                </flux:accordion.item>
            </flux:accordion>
        </flux:card>

        @if(!$readOnly)
            <flux:button type="submit" variant="primary">Submit Grade</flux:button>
        @else
            <flux:callout variant="info">You have already graded this application.</flux:callout>
        @endif

    </form>
</div>
