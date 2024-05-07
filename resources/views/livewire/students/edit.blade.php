<?php

use Livewire\Volt\Component;
use App\Models\Student;
use App\Models\Exam;
use App\Models\ClassForm;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\StudentDetail;
use Livewire\Attributes\Validate;

new class extends Component {
    public Student $student;
    public $studentId;
    #[Validate('required')]
    public $name;
    #[Validate('required')]
    public $adm_no;
    #[Validate('required')]
    public $form;
    #[Validate('required')]
    public $stream_id;
    #[Validate('required')]
    public $exam1;
    #[Validate('required')]
    public $exam2;
    #[Validate('required')]
    public $exam3;
    #[Validate('required')]
    public $teacher;
    #[Validate('required')]
    public $subject_id;
    public $subjects;
    public $primary_school;
    public $kcpe_year;
    public $kcpe_marks;
    public $kcpe_position;
    public $classes;
    public $streams;
    public $studentDetailsId;
    public $examId;
    public $form_sequence_number;
    
    public function mount($id)
    {
        $this->student = Student::find($id);
        if ($this->student) {
            $this->studentId = $this->student->id;
            $this->form_sequence_number = $this->student->form_sequence_number;
            $this->name = $this->student->name;
            $this->adm_no = $this->student->adm_no;
            $this->form = $this->student->class_id; // assuming the student has a class_id field linking to the classes table
            $this->stream_id = $this->student->stream_id;
            $studentDetails = $this->student->details;
            if ($studentDetails) {
                $this->studentDetailsId = $studentDetails->id;
                $this->primary_school = $studentDetails->primary_school;
                $this->kcpe_year = $studentDetails->kcpe_year;
                $this->kcpe_marks = $studentDetails->kcpe_marks;
                $this->kcpe_position = $studentDetails->kcpe_position;
            }

        $exam = Exam::where('student_id', $this->studentId)->first();
        if ($exam) {
            $this->exam1 = $exam->exam1;
            $this->exam2 = $exam->exam2;
            $this->exam3 = $exam->exam3;
            $this->teacher = $exam->teacher;
            $this->subject_id = $exam->subject_id;
            $this->examId = $exam->id;
        }
    }

    $this->classes = ClassForm::all();
    $this->streams = Stream::all();
    $this->subjects = Subject::all();
    $this->subject_id = $this->subjects->first()->id;
}

    public function updateStudent()
    { 
        $validatedData = $this->validate();

        $validatedData['form'] = $this->form;

        $student = Student::find($this->studentId);

        if ($student) {
            // Update the Student model
            $student->update($validatedData);

            // Update the StudentDetail model
            StudentDetail::updateOrCreate(
                ['id' => $this->studentDetailsId, 'student_id' => $this->studentId],
                [
                    'primary_school' => $this->primary_school,
                    'kcpe_year' => $this->kcpe_year,
                    'kcpe_marks' => $this->kcpe_marks,
                    'kcpe_position' => $this->kcpe_position,
                ]
            );
            
            // Update the Exam model
            Exam::updateOrCreate(
                ['id' => $this->examId, 'student_id' => $this->studentId, 'subject_id' => $this->subject_id],
                [
                    'subject_id' => $this->subject_id,
                    'exam1' => $this->exam1,
                    'exam2' => $this->exam2,
                    'exam3' => $this->exam3,
                    'teacher' => $this->teacher,
                ]
            );

            $this->dispatch('success', message: "Student details updated successfully.!");
                
        } else {
            session()->flash('error', 'Failed to update student details.');
        }
        
        $this->dispatch('student-updated');
    }

    public function cancel(): void
    {
        $this->dispatch('student-edit-canceled');
    }


public function updatedSubjectId()
{  
    if ($this->subject_id) {
        $exam = Exam::where('student_id', $this->studentId)->where('subject_id', $this->subject_id)->first();
        if ($exam) {
            $this->exam1 = $exam->exam1;
            $this->exam2 = $exam->exam2;
            $this->exam3 = $exam->exam3;
            $this->teacher = $exam->teacher;
            $this->examId = $exam->id;
        } else {
            $this->exam1 = '';
            $this->exam2 = '';
            $this->exam3 = '';
            $this->teacher = '';
            $this->examId = null;
        }
    }
}
}; ?>

<div>
    <div class="container">
        <form wire:submit="updateStudent" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name:</label>
                <input type="text" id="name" wire:model="name" class="form-input">
                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label for="adm_no" class="block text-sm font-medium text-gray-700">Admission Number:</label>
                <input type="text" id="adm_no" wire:model="adm_no" class="form-input">
                @error('adm_no') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label for="primary_school" class="block text-sm font-medium text-gray-700">Primary School:</label>
                <input type="text" id="primary_school" wire:model="primary_school" class="form-input">
            </div>
            
            <div>
                <label for="kcpe_year" class="block text-sm font-medium text-gray-700">KCPE Year:</label>
                <input type="number" id="kcpe_year" wire:model="kcpe_year" class="form-input">
            </div>
            
            <div>
                <label for="kcpe_marks" class="block text-sm font-medium text-gray-700">KCPE Marks:</label>
                <input type="number" id="kcpe_marks" wire:model="kcpe_marks" class="form-input">
            </div>
    
            <div>
                <label for="kcpe_position" class="block text-sm font-medium text-gray-700">KCPE Position:</label>
                <input type="number" id="kcpe_position" wire:model="kcpe_position" class="form-input">
            </div>
    
            <div>
                <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject:</label>
                <select id="subject_id" wire:model="subject_id" class="form-select">
                    <option value="">-- Select Subject --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
                @error('subject_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
    
            <div>
                <label for="exam1" class="block text-sm font-medium text-gray-700">Exam 1:</label>
                <input type="number" id="exam1" wire:model="exam1" class="form-input">
                @error('exam1') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
    
            <div>
                <label for="exam2" class="block text-sm font-medium text-gray-700">Exam 2:</label>
                <input type="number" id="exam2" wire:model="exam2" class="form-input">
                @error('exam2') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
    
            <div>
                <label for="exam3" class="block text-sm font-medium text-gray-700">Exam 3:</label>
                <input type="number" id="exam3" wire:model="exam3" class="form-input">
                @error('exam3') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
    
            <div>
                <label for="teacher" class="block text-sm font-medium text-gray-700">Teacher:</label>
                <input type="text" id="teacher" wire:model="teacher" class="form-input">
                @error('teacher') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
    
            <div>
                <label for="form" class="block text-sm font-medium text-gray-700">Form:</label>
                <select wire:model="form" class="form-select">
                    <option value="">Select Form</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
                @error('form') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
    
            <div>
                <label for="stream_id" class="block text-sm font-medium text-gray-700">Stream:</label>
                <select wire:model="stream_id" class="form-select">
                    <option value="">Select Stream</option>
                    @foreach ($streams as $stream)
                        <option value="{{ $stream->id }}">{{ $stream->name }}</option>
                    @endforeach
                </select>
                @error('stream_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
    
            <div class="col-span-2">
                <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Update Student</button>
                <button wire:click.prevent="cancel" class="w-full mt-2 py-2 px-4 border border-transparent rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Cancel</button>
            </div>
        </form>
    </div>

    {{-- Flash success message --}}
    <div x-data="{ open: false, message: '' }" 
            x-cloak
        @success.window="open = true; message = $event.detail.message; setTimeout(() => open = false, 4000)"
        x-show="open"
        class="mt-4 bg-green-500 text-white font-bold py-2 px-4 rounded">
        <span x-text="message"></span>
    </div>

     {{-- Flash error message --}}
     @if (session('error'))
     <div x-data="{ open: true }" 
          x-init="setTimeout(() => open = false, 4000)"
          x-show="open"
          class="mt-4 bg-red-500 text-white font-bold py-2 px-4 rounded">
         {{ session('error') }}
     </div>
  @endif    
</div>
