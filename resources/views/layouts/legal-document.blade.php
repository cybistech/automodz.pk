@extends('layouts.shop')

@section('title', trim($__env->yieldContent('legal-title')))
@section('meta_title', trim($__env->yieldContent('legal-title')).' | '.config('site.name'))
@section('meta_description', trim($__env->yieldContent('legal-meta-description')))
@section('canonical', url()->current())
@section('meta_image', \App\Support\Seo::defaultOgImage())
@section('meta_image_alt', config('site.name').' — '.trim($__env->yieldContent('legal-title')))
@section('robots', 'index, follow')
@section('og_type', 'website')

@push('head')
<style>
    .legal-prose h2 { margin-top: 0.25rem; font-size: 1.125rem; font-weight: 700; color: #f8fafc; }
    .legal-prose h3 { margin-top: 0.5rem; font-size: 0.95rem; font-weight: 600; color: #e2e8f0; }
    .legal-prose p, .legal-prose li { color: #cbd5e1; }
    .legal-prose ul { margin-top: 0.5rem; list-style: disc; padding-left: 1.25rem; }
    .legal-prose ul li + li { margin-top: 0.35rem; }
    .legal-prose a { color: #fb923c; }
    .legal-prose a:hover { color: #fdba74; }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
    <nav class="mb-6 text-sm text-slate-400">
        <a href="{{ route('home') }}" class="hover:text-orange-400">Home</a>
        <span class="mx-1">/</span>
        <span class="text-slate-300">@yield('legal-title')</span>
    </nav>

    <header class="border-b border-slate-800 pb-6">
        <h1 class="font-display text-3xl font-bold text-white sm:text-4xl">@yield('legal-title')</h1>
        <p class="mt-2 text-sm text-slate-400">Last updated: @yield('legal-last-updated')</p>
        <p class="mt-4 text-sm leading-relaxed text-slate-300">
            This document applies to {{ config('site.name') }}
            (<a href="{{ config('site.url') }}" class="text-orange-400 hover:text-orange-300">{{ config('site.domain') }}</a>),
            based in {{ config('site.office_city') }}, {{ config('site.office_country') }}.
            Contact:
            <a href="mailto:{{ config('site.email') }}" class="text-orange-400 hover:text-orange-300">{{ config('site.email') }}</a>.
        </p>
    </header>

    <article class="legal-prose mt-8 space-y-8 text-sm leading-relaxed">
        @yield('legal-body')
    </article>
</div>
@endsection
