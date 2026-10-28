@extends('errors.layout')

@section('title', 'Page Not Found')
@section('code', '404')

@section('icon')
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
@endsection

@section('heading', 'Page or Document Not Found')

@section('message')
    The requested ethics review record, document, or page does not exist or may have been moved.
@endsection

@section('actions')
    <a href="/dashboard" class="w-full sm:w-auto px-5 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition-colors" style="background-color: #800000;">
        Return to Dashboard
    </a>
    <a href="/faq" class="w-full sm:w-auto px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs rounded-xl transition-colors">
        Read Submission Guidelines
    </a>
@endsection
