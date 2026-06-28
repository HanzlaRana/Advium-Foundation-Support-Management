<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            [
                'name'                 => 'Ration Box Support',
                'slug'                 => 'ration-box',
                'description'          => 'Monthly ration packages for low-income families struggling to put food on the table.',
                'category'             => 'Food Aid',
                'type'                 => 'free',
                'icon'                 => '🛒',
                'total_helped'         => 4280,
                'is_active'            => true,
                'eligibility_criteria' => 'Low-income families with monthly income below PKR 25,000.',
                'required_documents'   => ['CNIC', 'Utility Bill', 'Family Photo'],
                'loan_amount'          => null,
                'loan_duration_months' => null,
            ],
            [
                'name'                 => 'Water Filtration',
                'slug'                 => 'water-filtration',
                'description'          => 'Clean drinking water filtration plants installed in underserved communities.',
                'category'             => 'Community',
                'type'                 => 'free',
                'icon'                 => '💧',
                'total_helped'         => 1850,
                'is_active'            => true,
                'eligibility_criteria' => 'Communities without access to clean drinking water.',
                'required_documents'   => ['CNIC', 'Community Letter'],
                'loan_amount'          => null,
                'loan_duration_months' => null,
            ],
            [
                'name'                 => 'Sewing Machine',
                'slug'                 => 'sewing-machine',
                'description'          => 'Free sewing machines for women to start home-based tailoring businesses.',
                'category'             => 'Women Empowerment',
                'type'                 => 'free',
                'icon'                 => '🪡',
                'total_helped'         => 1240,
                'is_active'            => true,
                'eligibility_criteria' => 'Women from low-income families with basic sewing skills.',
                'required_documents'   => ['CNIC', 'Skill Certificate', 'Family Income Proof'],
                'loan_amount'          => null,
                'loan_duration_months' => null,
            ],
            [
                'name'                 => 'Children Education Support',
                'slug'                 => 'education-support',
                'description'          => 'School fees, books, and uniforms for children of underprivileged families.',
                'category'             => 'Education',
                'type'                 => 'free',
                'icon'                 => '📚',
                'total_helped'         => 3120,
                'is_active'            => true,
                'eligibility_criteria' => 'Children aged 5-18 from families unable to afford education.',
                'required_documents'   => ['Child CNIC/B-Form', 'Parent CNIC', 'School Admission Letter'],
                'loan_amount'          => null,
                'loan_duration_months' => null,
            ],
            [
                'name'                 => 'Disabled Bike Support',
                'slug'                 => 'disabled-bike',
                'description'          => 'Modified motorbikes for persons with disabilities — interest-free loan.',
                'category'             => 'Mobility',
                'type'                 => 'loan',
                'icon'                 => '♿',
                'total_helped'         => 920,
                'is_active'            => true,
                'eligibility_criteria' => 'Persons with physical disabilities with valid disability certificate.',
                'required_documents'   => ['CNIC', 'Disability Certificate', 'Medical Report'],
                'loan_amount'          => 150000.00,
                'loan_duration_months' => 24,
            ],
            [
                'name'                 => 'Rickshaw Program',
                'slug'                 => 'rickshaw',
                'description'          => 'Auto-rickshaws for unemployed men to start earning — interest-free 36-month loan.',
                'category'             => 'Employment',
                'type'                 => 'loan',
                'icon'                 => '🛺',
                'total_helped'         => 990,
                'is_active'            => true,
                'eligibility_criteria' => 'Unemployed men aged 18-45 with valid driving license.',
                'required_documents'   => ['CNIC', 'Driving License', 'Guarantor CNIC'],
                'loan_amount'          => 350000.00,
                'loan_duration_months' => 36,
            ],
        ];

        foreach ($programs as $program) {
            Program::create($program);
        }
    }
}