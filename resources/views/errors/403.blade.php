@extends('errors.layout')

@section('title', 'Access Restricted')
@section('code', '403')

@section('icon')
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
    </svg>
@endsection

@section('heading', 'Access Restricted')

@section('message')
    You do not have permission to access this page or resource. Please ensure you are signed in with the correct DEMO institutional account.
@endsection

@section('actions')
    <a href="/dashboard" class="w-full sm:w-auto px-5 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition-colors" style="background-color: #800000;">
        Back to Dashboard
    </a>
    <a href="/" class="w-full sm:w-auto px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs rounded-xl transition-colors">
        Sign In with Another Account
    </a>
@endsection
