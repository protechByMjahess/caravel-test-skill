@extends('layouts.app')

@section('title', 'Projects List')

@section('content')
    @livewire('projects-list')
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Smooth transitions for Alpine.js */
    [x-cloak] { display: none !important; }
    
    /* Custom scrollbar for task list */
    .task-list::-webkit-scrollbar {
        width: 4px;
    }
    
    .task-list::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 2px;
    }
    
    .task-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 2px;
    }
    
    .task-list::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endpush

