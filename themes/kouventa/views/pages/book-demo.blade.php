@extends('kouventa::layouts.app')

@section('content')
<div class="kv-canvas-wrap">
<!-- ============ NAV + HERO ============ -->
<section style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative">
<div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none"><div style="position: absolute; right: -240px; top: -200px; width: 900px; height: 900px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div></div>
<div aria-hidden="true" style="position: absolute; right: 60px; bottom: 0; width: 500px; height: 440px; pointer-events: none">
<div style="position: absolute; left: 50%; top: 70px; width: 400px; height: 400px; margin-left: -200px; border-radius: 50%; background: radial-gradient(circle, rgba(43,212,106,0.22) 0%, rgba(43,212,106,0.07) 45%, rgba(43,212,106,0) 70%)"></div>
<img src="{{ theme_asset('images/hero-ce-mockup.png') }}" alt="" style="position: absolute; left: 50%; bottom: 0; height: 360px; width: auto; transform: translateX(-50%); display: block; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.35))">
</div>
<div style="padding: 80px 80px 220px; display: flex; flex-direction: column; gap: 18px; position: relative">
<h1 style="margin: 0; font-size: 64px; line-height: 1.05; font-weight: 800; letter-spacing: -0.035em">Get in touch <span style="color: #2BD46A">with us.</span></h1>
<span style="display: flex; align-items: center; gap: 10px; font-size: 18px; color: #B9C6D3"><span style="width: 24px; height: 24px; border-radius: 50%; background: #2BD46A; color: #06251A; display: flex; align-items: center; justify-content: center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5L20 7"></path></svg></span>Enjoy a 30-day free trial</span>
</div>
</section>

<!-- ============ FORM ============ -->
<section style="padding: 0 80px 120px; margin-top: -170px; position: relative; z-index: 2">
<div style="display: grid; grid-template-columns: minmax(0, 1fr) 440px; gap: 32px; align-items: start">

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
        <a href="mailto:kouventa@helios.id" class="inline-block mt-2 px-6 py-3 rounded-xl bg-[#2BD46A] text-[#06251A] font-bold text-sm">
            {{ t('kouventa.Contact sales', 'Contact sales') }} (kouventa@helios.id)
        </a>
    </div>
@endif

<div style="display: flex; flex-direction: column; gap: 16px; padding-top: 194px">
<div style="display: flex; gap: 16px; align-items: flex-start; background: #FFFFFF; border-radius: 16px; padding: 20px 22px; border: 1px solid #E3E8E6"><span style="width: 44px; height: 44px; border-radius: 12px; background: #E6F8EC; color: #13803F; display: flex; align-items: center; justify-content: center; flex-shrink: 0"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"></path><circle cx="12" cy="10" r="2.5"></circle></svg></span><div style="display: flex; flex-direction: column; gap: 4px"><span style="font-size: 17px; font-weight: 800">Location</span><span style="font-size: 15px; line-height: 1.55; color: #4A5866">Centennial Tower, 12th Floor, Jl. Jend. Gatot Subroto Kav. 24-25, Jakarta</span></div></div>
<div style="display: flex; gap: 16px; align-items: flex-start; background: #FFFFFF; border-radius: 16px; padding: 20px 22px; border: 1px solid #E3E8E6"><span style="width: 44px; height: 44px; border-radius: 12px; background: #E6F8EC; color: #13803F; display: flex; align-items: center; justify-content: center; flex-shrink: 0"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"></path></svg></span><div style="display: flex; flex-direction: column; gap: 4px"><span style="font-size: 17px; font-weight: 800">Phone</span><a href="tel:+62811849689" style="font-size: 15px; line-height: 1.55; color: #4A5866">+62 8118 4968 89</a></div></div>
<div style="display: flex; gap: 16px; align-items: flex-start; background: #FFFFFF; border-radius: 16px; padding: 20px 22px; border: 1px solid #E3E8E6"><span style="width: 44px; height: 44px; border-radius: 12px; background: #E6F8EC; color: #13803F; display: flex; align-items: center; justify-content: center; flex-shrink: 0"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg></span><div style="display: flex; flex-direction: column; gap: 4px"><span style="font-size: 17px; font-weight: 800">Email</span><a href="mailto:kouventa@helios.id" style="font-size: 15px; line-height: 1.55; color: #4A5866">kouventa@helios.id</a></div></div>
</div>
</div>
</section>

<!-- ============ FOOTER ============ -->
</div>
@endsection
