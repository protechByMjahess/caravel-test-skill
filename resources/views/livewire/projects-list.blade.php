<div>
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Projects</h1>
                <p class="text-gray-600 mt-2">Manage and track your projects</p>
            </div>
            <a href="{{ route('projects.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-plus mr-2"></i>New Project
            </a>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Search Input -->
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Projects</label>
                    <div class="relative">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               id="search"
                               placeholder="Search by name or description..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Task Status Filter -->
                <div class="md:w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter Tasks</label>
                    <select wire:model.live="taskStatusFilter" 
                            class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all">All Tasks</option>
                        <option value="todo">To Do</option>
                        <option value="in_progress">In Progress</option>
                        <option value="done">Done</option>
                    </select>
                </div>

                <!-- Sort Options -->
                <div class="md:w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select wire:model.live="sortBy" 
                            class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="created_at">Date Created</option>
                        <option value="name">Name</option>
                        <option value="updated_at">Last Updated</option>
                    </select>
                </div>

                <div class="md:w-32">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Direction</label>
                    <select wire:model.live="sortDirection" 
                            class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="desc">Descending</option>
                        <option value="asc">Ascending</option>
                    </select>
                </div>

                <div class="md:w-32">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Per Page</label>
                    <select wire:model.live="perPage" 
                            class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Pagination Info -->
        @if($projects->count() > 0)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        <span class="text-sm text-blue-700">
                            Page {{ $projects->currentPage() }} of {{ $projects->lastPage() }} 
                            ({{ $projects->total() }} total projects)
                        </span>
                    </div>
                    <div class="text-sm text-blue-600">
                        {{ $projects->perPage() }} projects per page
                    </div>
                </div>
            </div>
        @endif

        <!-- Projects List -->
        @if($projects->count() > 0)
            <div class="grid gap-6">
                @foreach($projects as $project)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200" 
                         x-data="{ expanded: false }">
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                            <a href="{{ route('projects.show', $project) }}" 
                                               class="hover:text-blue-600 transition-colors duration-200">
                                                {{ $project->name }}
                                            </a>
                                        </h3>
                                        
                                        @if($project->tasks->count() > 0)
                                            <button @click="expanded = !expanded" 
                                                    class="flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors duration-200">
                                                <span x-text="expanded ? 'Hide Tasks' : 'Show Tasks'"></span>
                                                <i class="fas fa-chevron-down ml-2 transition-transform duration-200" 
                                                   :class="{ 'rotate-180': expanded }"></i>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    @if($project->description)
                                        <p class="text-gray-600 mb-4 line-clamp-2">{{ $project->description }}</p>
                                    @endif

                                    <!-- Project Stats -->
                                    <div class="flex items-center space-x-6 text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <i class="fas fa-tasks mr-2"></i>
                                            <span>{{ $project->tasks->count() }} tasks</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-check-circle mr-2 text-green-500"></i>
                                            <span>{{ $project->tasks->where('status', 'done')->count() }} done</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock mr-2 text-blue-500"></i>
                                            <span>{{ $project->tasks->where('status', 'in_progress')->count() }} in progress</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar mr-2"></i>
                                            <span>{{ $project->created_at->format('M j, Y') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center space-x-2 ml-4">
                                    <a href="{{ route('projects.show', $project) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition-colors duration-200"
                                       title="View Project">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('projects.edit', $project) }}" 
                                       class="text-green-600 hover:text-green-800 transition-colors duration-200"
                                       title="Edit Project">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button wire:click="deleteProject({{ $project->id }})"
                                            wire:confirm="Are you sure you want to delete this project? This action cannot be undone."
                                            class="text-red-600 hover:text-red-800 transition-colors duration-200"
                                            title="Delete Project">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            @if($project->tasks->count() > 0)
                                <div class="mt-4">
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>Progress</span>
                                        <span>{{ round(($project->tasks->where('status', 'done')->count() / $project->tasks->count()) * 100) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                             style="width: {{ ($project->tasks->where('status', 'done')->count() / $project->tasks->count()) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Expandable Tasks Section -->
                        <div x-show="expanded" 
                             x-cloak
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform -translate-y-2"
                             class="border-t border-gray-200 bg-gray-50">
                            <div class="p-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-list-check mr-2 text-blue-600"></i>
                                    Tasks ({{ $project->tasks->count() }})
                                </h4>
                                
                                @if($project->tasks->count() > 0)
                                    <div class="space-y-3 task-list max-h-96 overflow-y-auto">
                                        @foreach($project->tasks as $task)
                                            <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                                                <div class="flex items-center space-x-3">
                                                    <div class="flex-shrink-0">
                                                        @if($task->status === 'done')
                                                            <i class="fas fa-check-circle text-green-500 text-lg"></i>
                                                        @elseif($task->status === 'in_progress')
                                                            <i class="fas fa-clock text-blue-500 text-lg"></i>
                                                        @else
                                                            <i class="fas fa-circle text-gray-300 text-lg"></i>
                                                        @endif
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 {{ $task->status === 'done' ? 'line-through text-gray-500' : '' }}">
                                                            {{ $task->title ?? 'Untitled Task' }}
                                                        </p>
                                                        @if($task->description)
                                                            <p class="text-xs text-gray-500 mt-1 line-clamp-1">
                                                                {{ $task->description }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2 text-xs text-gray-500">
                                                    <span>{{ $task->created_at ? $task->created_at->format('M j') : 'N/A' }}</span>
                                                    @if($task->status === 'done')
                                                        <span class="text-green-600 font-medium">Done</span>
                                                    @elseif($task->status === 'in_progress')
                                                        <span class="text-blue-600 font-medium">In Progress</span>
                                                    @else
                                                        <span class="text-orange-600 font-medium">To Do</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8 text-gray-500">
                                        <i class="fas fa-tasks text-4xl mb-2"></i>
                                        <p>No tasks yet</p>
                                        <a href="{{ route('projects.show', $project) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                                            Add your first task
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-lg shadow-sm">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <!-- Mobile pagination -->
                        @if ($projects->onFirstPage())
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-not-allowed">
                                Previous
                            </span>
                        @else
                            <button wire:click="previousPage" 
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </button>
                        @endif

                        @if ($projects->hasMorePages())
                            <button wire:click="nextPage" 
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </button>
                        @else
                            <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-not-allowed">
                                Next
                            </span>
                        @endif
</div>

                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ $projects->firstItem() }}</span>
                                to
                                <span class="font-medium">{{ $projects->lastItem() }}</span>
                                of
                                <span class="font-medium">{{ $projects->total() }}</span>
                                results
                            </p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Go to Page Input -->
                            <div class="flex items-center space-x-2">
                                <label for="gotoPage" class="text-sm text-gray-700">Go to:</label>
                                <input type="number" 
                                       id="gotoPage"
                                       min="1" 
                                       max="{{ $projects->lastPage() }}" 
                                       wire:keydown.enter="$set('page', $event.target.value)"
                                       class="w-16 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <button wire:click="gotoPage(document.getElementById('gotoPage').value)" 
                                        class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                                    Go
                                </button>
                            </div>
                            
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <!-- Previous Page Link -->
                                @if ($projects->onFirstPage())
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-not-allowed">
                                        <i class="fas fa-chevron-left h-5 w-5"></i>
                                    </span>
                                @else
                                    <button wire:click="previousPage" 
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-chevron-left h-5 w-5"></i>
                                    </button>
                                @endif

                                <!-- Pagination Elements -->
                                @foreach ($projects->getUrlRange(1, $projects->lastPage()) as $page => $url)
                                    @if ($page == $projects->currentPage())
                                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-50 text-sm font-medium text-blue-600">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <button wire:click="gotoPage({{ $page }})" 
                                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            {{ $page }}
                                        </button>
                                    @endif
                                @endforeach

                                <!-- Next Page Link -->
                                @if ($projects->hasMorePages())
                                    <button wire:click="nextPage" 
                                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-chevron-right h-5 w-5"></i>
                                    </button>
                                @else
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-not-allowed">
                                        <i class="fas fa-chevron-right h-5 w-5"></i>
                                    </span>
                                @endif
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-folder-open text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No projects found</h3>
                <p class="text-gray-600 mb-6">
                    @if($search)
                        No projects match your search criteria.
                    @else
                        Get started by creating your first project.
                    @endif
                </p>
                <a href="{{ route('projects.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-plus mr-2"></i>Create Project
                </a>
            </div>
        @endif
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
            <span class="text-gray-700">Loading...</span>
        </div>
    </div>
</div>