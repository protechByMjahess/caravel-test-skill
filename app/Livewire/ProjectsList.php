<?php

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectsList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $taskStatusFilter = 'all'; // all, todo, in_progress, done

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'taskStatusFilter' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTaskStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteProject($projectId)
    {
        try {
            $project = Project::findOrFail($projectId);
            
            // Check if user can delete this project
            if (Auth::user()->cannot('delete', $project)) {
                session()->flash('error', 'You are not authorized to delete this project.');
                return;
            }

            $project->delete();
            session()->flash('success', 'Project deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while deleting the project.');
        }
    }

    public function render()
    {
        try {
            $projects = Auth::user()
                ->projects()
                ->with(['tasks' => function ($query) {
                    $query->select('id', 'project_id', 'title', 'description', 'status', 'created_at')
                          ->when($this->taskStatusFilter !== 'all', function ($q) {
                              $q->where('status', $this->taskStatusFilter);
                          });
                }])
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate(10);

            return view('livewire.projects-list', [
                'projects' => $projects
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while loading projects: ' . $e->getMessage());
            
            return view('livewire.projects-list', [
                'projects' => collect()->paginate(10)
            ]);
        }
    }
}