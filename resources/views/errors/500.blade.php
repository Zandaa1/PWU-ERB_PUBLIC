@extends('errors.layout')

@section('title', 'System Issue')
@section('code', '500')

@section('icon')
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
@endsection

@section('heading', 'Something Went Wrong')

@section('message')
    An unexpected technical issue occurred while processing your request. Please try again in a few moments or return to your dashboard.
@endsection

@section('actions')
    <a href="/dashboard" class="w-full sm:w-auto px-5 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition-colors" style="background-color: #800000;">
        Return to Dashboard
    </a>
    <button onclick="window.location.reload();" class="w-full sm:w-auto px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs rounded-xl transition-colors cursor-pointer">
        Try Again
    </button>
@endsection
