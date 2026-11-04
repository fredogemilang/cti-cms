@extends('kouventa::layouts.app')

@php
    $heroTitle = $page?->titleBlock('hero_title', ['prefix' => 'Get in touch', 'main' => 'with us']);
    $heroBadge = $page?->block('hero_badge', 'Enjoy a 30-day free trial');
    $locationTitle = $page?->block('location_title', 'Location');
    $locationText = $page?->block('location_text', 'Centennial Tower, 12th Floor, Jl. Jend. Gatot Subroto Kav. 24-25, Jakarta');
    $phoneTitle = $page?->block('phone_title', 'Phone');
    $phoneNumber = $page?->block('phone_number', '+62 811 8496 889');
    $emailTitle = $page?->block('email_title', 'Email');
    $emailAddress = $page?->block('email_address', 'kouventa@helios.id');
@endphp

@section('content')
<div class="kv-canvas-wrap">
<!-- ============ NAV + HERO ============ -->
<section style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative; width: 100%;">
<div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none">
    <div style="position: absolute; left: 50%; top: -200px; width: 1000px; height: 1000px; margin-left: -500px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div>
</div>
<div class="kv-container" style="padding-top: 80px; padding-bottom: 220px; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 18px; position: relative">
<h1 style="margin: 0; font-size: clamp(36px, 5vw, 64px); line-height: 1.05; font-weight: 800; letter-spacing: -0.035em">{{ $heroTitle['prefix'] ?? 'Get in touch' }} <span style="color: #2BD46A">{{ $heroTitle['main'] ?? 'with us' }}</span></h1>
<span style="display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 18px; color: #B9C6D3"><span style="width: 24px; height: 24px; border-radius: 50%; background: #2BD46A; color: #06251A; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5L20 7"></path></svg></span>{{ $heroBadge }}</span>
</div>
</section>

<!-- ============ FORM & CONTACT INFO ============ -->
<section style="width: 100%; margin-top: -170px; position: relative; z-index: 2">
<div class="kv-container" style="padding-bottom: 120px;">
<h2 class="sr-only">{{ t('kouventa.Schedule a Live Product Consultation', 'Schedule a Live Product Consultation') }}</h2>

<div style="max-width: 920px; width: 100%; margin: 0 auto; display: flex; flex-direction: column; gap: 28px">
@php
    $theme = active_theme();
    $assignments = setting("theme_{$theme->slug}_form_assignments", []);
    $formId = $assignments['book_demo_form'] ?? null;
    $form = $formId ? \App\Models\Form::with('fields')->find($formId) : null;
@endphp

@if($form)
    @include('kouventa::partials.tailwind-form', ['form' => $form])
@else
    <div class="p-6 rounded-2xl bg-white border border-slate-200 text-center flex flex-col items-center gap-3">
        <h3 class="text-lg font-bold text-[#0E1A24]">{{ t('kouventa.Book a demo', 'Book a demo') }}</h3>
        <p class="text-sm text-[#4A5866] max-w-md">
            {{ t('kouventa.Book a working session with our solution team. We\'ll map your use cases to the right tier and deployment.', 'Book a working session with our solution team. We\'ll map your use cases to the right tier and deployment.') }}
        </p>
        <a href="#whatsapp" @click.prevent="$dispatch('open-whatsapp-modal')" data-open-whatsapp class="inline-block mt-2 px-6 py-3 rounded-xl bg-[#2BD46A] text-[#06251A] font-bold text-sm cursor-pointer">
            {{ t('kouventa.Contact sales', 'Contact sales') }}
        </a>
    </div>
@endif

<!-- Contact Cards: 3 cards arranged horizontally underneath the form -->
<div class="kv-contact-grid">
    <div style="display: flex; gap: 16px; align-items: flex-start; background: #FFFFFF; border-radius: 16px; padding: 22px; border: 1px solid #E3E8E6; box-shadow: 0 4px 20px rgba(7,28,51,0.05)">
        <span style="width: 44px; height: 44px; border-radius: 12px; background: #E6F8EC; color: #13803F; display: flex; align-items: center; justify-content: center; flex-shrink: 0">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>
        </span>
        <div style="display: flex; flex-direction: column; gap: 4px; min-width: 0">
            <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: #0E1A24">{{ $locationTitle }}</h3>
            <span style="font-size: 14px; line-height: 1.55; color: #4A5866">{{ $locationText }}</span>
        </div>
    </div>

    <div style="display: flex; gap: 16px; align-items: flex-start; background: #FFFFFF; border-radius: 16px; padding: 22px; border: 1px solid #E3E8E6; box-shadow: 0 4px 20px rgba(7,28,51,0.05)">
        <span style="width: 44px; height: 44px; border-radius: 12px; background: #E6F8EC; color: #13803F; display: flex; align-items: center; justify-content: center; flex-shrink: 0">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"></path></svg>
        </span>
        <div style="display: flex; flex-direction: column; gap: 4px; min-width: 0">
            <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: #0E1A24">{{ $phoneTitle }}</h3>
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phoneNumber) }}" style="font-size: 14px; line-height: 1.55; color: #4A5866; text-decoration: none">{{ $phoneNumber }}</a>
        </div>
    </div>

    <div style="display: flex; gap: 16px; align-items: flex-start; background: #FFFFFF; border-radius: 16px; padding: 22px; border: 1px solid #E3E8E6; box-shadow: 0 4px 20px rgba(7,28,51,0.05)">
        <span style="width: 44px; height: 44px; border-radius: 12px; background: #E6F8EC; color: #13803F; display: flex; align-items: center; justify-content: center; flex-shrink: 0">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>
        </span>
        <div style="display: flex; flex-direction: column; gap: 4px; min-width: 0">
            <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: #0E1A24">{{ $emailTitle }}</h3>
            <a href="mailto:{{ $emailAddress }}" style="font-size: 14px; line-height: 1.55; color: #4A5866; text-decoration: none">{{ $emailAddress }}</a>
        </div>
    </div>
</div>

</div>
</div>
</section>

<!-- ============ FOOTER ============ -->
</div>
@endsection
