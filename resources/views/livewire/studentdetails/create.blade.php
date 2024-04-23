<?php

use Livewire\Volt\Component;

new class extends Component {
    #[Validate('required|string|max:255')]
    public $name;
    #[Validate('required|integer')]
    public $adm_no;
    #[Validate('required|string|max:255')]
    public $primary_school;
    #[Validate('required|integer')]
    public $kcpe_year;
    #[Validate('required|integer')]
    public $kcpe_marks;
    #[Validate('required|integer')]
    public $kcpe_position;
    public $student_id;

    public function submit()
    {
        // Validation rules
        $this->validate();

        // Find the student or create a new one
        $student = Student::firstOrCreate(
            ['adm_no' => $this->adm_no],
            ['name' => $this->name]
        );

        // Set the student_id property to the student's id 
        $this->student_id = $student->id;

        // Update or create student details
        $studentDetails = StudentDetail::updateOrCreate(
            ['student_id' => $this->student_id],
            [
                'primary_school' => $this->primary_school,
                'kcpe_year' => $this->kcpe_year,
                'kcpe_marks' => $this->kcpe_marks,
                'kcpe_position' => $this->kcpe_position,
            ]
        );

        // Show a success message or redirect to another page
        session()->flash('message', 'Student details saved successfully.');
    }
}; ?>

<div>
    //
</div>
