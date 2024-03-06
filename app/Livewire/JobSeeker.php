<?php

namespace App\Livewire;

use App\Models\Experience;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobType;
use Livewire\Component;
use Livewire\WithPagination;

class JobSeeker extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search;
    public $jobs;
    public $categories;
    public $category_id;
    public $job_types;
    public $type_id;
    public $job_experience;
    public $experience_id;

    public function mount()
    {
        $this->loadJobs();
        
    }

    public function loadJobs()
    {
        $query = Job::query();

        if ($this->category_id) {
            $query->where('job_category_id', $this->category_id);
        }

        if ($this->type_id) {
            $query->where('job_type_id', $this->type_id);
        }

        if ($this->experience_id) {
            $query->where('experience_id', $this->experience_id);
        }

        if ($this->search) {
            $query->where(function ($subQuery) {
                $subQuery->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('short_description', 'like', '%' . $this->search . '%')
                    ->orWhere('company_name', 'like', '%' . $this->search . '%')
                    ->orWhere('location', 'like', '%' . $this->search . '%');
            });
        }

        $this->jobs = $query->orderBy('id', 'DESC')->get();
    }

    public function searchtab()
    {
        $this->loadJobs();
    }

    public function getByCategory($id)
    {
        $this->resetPage();
        $this->category_id = $id;
        $this->loadJobs();
    }

    public function getByType($id)
    {
        $this->resetPage();
        $this->type_id = $id;
        $this->loadJobs();
    }

    public function getByExperience($id)
    {
        $this->resetPage();
        $this->experience_id = $id;
        $this->loadJobs();
    }

    public function render()
    {
        $this->categories = JobCategory::all();
        $this->job_types = JobType::all();
        $this->job_experience = Experience::all();

        return view('livewire.job-seeker', [
            'jobs' => $this->jobs,
        ]);
    }
}
