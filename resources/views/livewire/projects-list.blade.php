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
            <div class="flex flex-col md:flex-row gap-4">
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

        <!-- Projects List -->
        @if($projects->count() > 0)
            <div class="grid gap-6">
                @foreach($projects as $project)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                        <a href="{{ route('projects.show', $project) }}" 
                                           class="hover:text-blue-600 transition-colors duration-200">
                                            {{ $project->name }}
                                        </a>
                                    </h3>
                                    
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
                                            <span>{{ $project->tasks->where('completed', true)->count() }} completed</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock mr-2"></i>
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
                                        <span>{{ round(($project->tasks->where('completed', true)->count() / $project->tasks->count()) * 100) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                             style="width: {{ ($project->tasks->where('completed', true)->count() / $project->tasks->count()) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $projects->links() }}
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