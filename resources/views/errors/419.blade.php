@extends('errors.layout')

@section('title', 'Session Expired')
@section('code', '419')

@section('icon')
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
@endsection

@section('heading', 'Session Timed Out')

@section('message')
    Your security session has expired due to a period of inactivity. Please refresh the page or sign in again to continue safely.
@endsection

@section('actions')
    <a href="/" class="w-full sm:w-auto px-5 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition-colors" style="background-color: #800000;">
        Sign In Again
    </a>
    <button onclick="window.location.reload();" class="w-full sm:w-auto px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs rounded-xl transition-colors cursor-pointer">
        Refresh Current Page
    </button>
@endsection
