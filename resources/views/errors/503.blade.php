@extends('errors.layout')

@section('title', 'System Maintenance')
@section('code', '503')

@section('icon')
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
    </svg>
@endsection

@section('heading', 'Scheduled System Maintenance')

@section('message')
    The DEMO Ethics Review Board portal is currently undergoing scheduled maintenance and system optimization. Service will resume shortly.
@endsection

@section('actions')
    <button onclick="window.location.reload();" class="w-full sm:w-auto px-5 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition-colors cursor-pointer" style="background-color: #800000;">
        Check Status (Refresh)
    </button>
    <a href="/faq" class="w-full sm:w-auto px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs rounded-xl transition-colors">
        Read Submission Guidelines
    </a>
@endsection
