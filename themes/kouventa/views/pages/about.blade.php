@extends('kouventa::layouts.app')

@section('content')
<div class="kv-canvas-wrap">
<!-- ============ NAV + HERO ============ -->
<section style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative">
<div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none"><div style="position: absolute; right: -240px; top: -200px; width: 900px; height: 900px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div></div>
<div style="padding: 88px 80px 240px; display: flex; flex-direction: column; gap: 18px; position: relative">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 13px; letter-spacing: 0.1em; text-transform: uppercase; color: #93A4B6">About us</span>
<h1 style="margin: 0; font-size: 64px; line-height: 1.05; font-weight: 800; letter-spacing: -0.035em">From business communication<br>to <span style="color: #2BD46A">enterprise AI.</span></h1>
</div>
</section>

<!-- ============ TEAM + VALUES ============ -->
<section style="padding: 0 80px; margin-top: -170px; position: relative; z-index: 2">
<div style="display: grid; grid-template-columns: minmax(0, 1fr) 420px; gap: 24px; align-items: stretch">
<img src="{{ theme_asset('images/kouventa-team.jpg') }}" alt="The Kouventa team" style="width: 100%; height: 620px; object-fit: cover; object-position: center 45%; border-radius: 22px; display: block; box-shadow: 0 24px 60px rgba(7,28,51,0.18)">
<div style="display: flex; flex-direction: column; gap: 16px">
<span style="height: 24px; font-family: 'JetBrains Mono', monospace; font-size: 13px; letter-spacing: 0.1em; text-transform: uppercase; color: #93A4B6; text-align: right">Our value</span>
<div style="flex: 1 1 0; background: #FFFFFF; border-radius: 18px; padding: 24px 26px; display: flex; flex-direction: column; justify-content: center; gap: 12px; box-shadow: 0 18px 40px rgba(7,28,51,0.12)">
<div style="display: flex; align-items: center; gap: 14px">
<span style="width: 48px; height: 48px; border-radius: 14px; background: #E6F8EC; color: #13803F; display: flex; align-items: center; justify-content: center; flex-shrink: 0"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"></path><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"></path><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"></path><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"></path></svg></span>
<span style="font-size: 22px; font-weight: 800; letter-spacing: -0.01em">Agile</span>
</div>
<p style="margin: 0; font-size: 15px; line-height: 1.6; color: #4A5866">We focus on collaboration, responsiveness, and delivering working solutions to foster innovation and exceed stakeholder expectations.</p>
</div>
<div style="flex: 1 1 0; background: #FFFFFF; border-radius: 18px; padding: 24px 26px; display: flex; flex-direction: column; justify-content: center; gap: 12px; box-shadow: 0 18px 40px rgba(7,28,51,0.12)">
<div style="display: flex; align-items: center; gap: 14px">
<span style="width: 48px; height: 48px; border-radius: 14px; background: #E6F8EC; color: #13803F; display: flex; align-items: center; justify-content: center; flex-shrink: 0"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 1-15.5 6.2"></path><path d="M3 12a9 9 0 0 1 15.5-6.2"></path><path d="M18 2v4h-4"></path><path d="M6 22v-4h4"></path><circle cx="12" cy="12" r="3"></circle></svg></span>
<span style="font-size: 22px; font-weight: 800; letter-spacing: -0.01em">Adaptive</span>
</div>
<p style="margin: 0; font-size: 15px; line-height: 1.6; color: #4A5866">We thrive on adaptability, shaping solutions to evolving needs. Our success is rooted in embracing change, growth, and innovation.</p>
</div>
<div style="flex: 1 1 0; background: #FFFFFF; border-radius: 18px; padding: 24px 26px; display: flex; flex-direction: column; justify-content: center; gap: 12px; box-shadow: 0 18px 40px rgba(7,28,51,0.12)">
<div style="display: flex; align-items: center; gap: 14px">
<span style="width: 48px; height: 48px; border-radius: 14px; background: #E6F8EC; color: #13803F; display: flex; align-items: center; justify-content: center; flex-shrink: 0"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="5" width="14" height="14" rx="3"></rect><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"></path><path d="M9.5 15l1.5-6 1.5 6M10 13.5h2M15 9v6"></path></svg></span>
<span style="font-size: 22px; font-weight: 800; letter-spacing: -0.01em">AI Friendly</span>
</div>
<p style="margin: 0; font-size: 15px; line-height: 1.6; color: #4A5866">We excel with AI-friendly innovation, leveraging technology to enhance customer experiences and create meaningful, future-ready interactions.</p>
</div>
</div>
</div>
</section>

<!-- ============ HISTORY ============ -->
<section id="kv-history" style="padding: 120px 80px 128px; display: flex; flex-direction: column; gap: 56px">
<h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">History</h2>
<div style="position: relative; display: flex; flex-direction: column; gap: 48px">
<div style="position: absolute; left: 235px; top: 112px; bottom: 112px; width: 2px; background: repeating-linear-gradient(to bottom, #C3CDC8 0 6px, transparent 6px 12px)"></div>
<div style="position: absolute; left: 235px; top: 112px; bottom: 112px; width: 2px; background: #2BD46A; transform-origin: top; animation: kvTlF 7s ease-in-out both; animation-play-state: running"></div>
<div style="display: grid; grid-template-columns: 180px 48px 400px minmax(0, 1fr); gap: 32px; align-items: center; position: relative; animation: kvTlR0 7s ease both; animation-play-state: running">
<span style="font-size: 72px; line-height: 1; font-weight: 800; letter-spacing: -0.03em; color: #C3CDC8; text-align: right">2014</span>
<span style="width: 48px; display: flex; justify-content: center"><span style="width: 18px; height: 18px; border-radius: 50%; background: #2BD46A; box-shadow: 0 0 0 6px #F4F6F5, 0 0 0 8px rgba(43,212,106,0.35); animation: kvTlD0 7s ease both; animation-play-state: running"></span></span>
<img src="{{ theme_asset('images/office-building.jpg') }}" alt="Office building" style="width: 400px; height: 224px; object-fit: cover; border-radius: 18px; display: block">
<div style="display: flex; flex-direction: column; gap: 10px">
<h3 style="margin: 0; font-size: 26px; line-height: 1.2; font-weight: 800; color: #13803F; letter-spacing: -0.01em">Established</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.65; color: #4A5866">PT Helios Informatika Nusantara (Helios) was established in 2014 as IT Value Added Distributor (VAD). As a member of PT Computrade Technology International (CTI Group), Helios collaborates with some of the world's leading IT global companies to provide our clients with cutting-edge and innovative technologies.</p>
</div>
</div>
<div style="display: grid; grid-template-columns: 180px 48px 400px minmax(0, 1fr); gap: 32px; align-items: center; position: relative; animation: kvTlR1 7s ease both; animation-play-state: running">
<span style="font-size: 72px; line-height: 1; font-weight: 800; letter-spacing: -0.03em; color: #C3CDC8; text-align: right">2021</span>
<span style="width: 48px; display: flex; justify-content: center"><span style="width: 18px; height: 18px; border-radius: 50%; background: #2BD46A; box-shadow: 0 0 0 6px #F4F6F5, 0 0 0 8px rgba(43,212,106,0.35); animation: kvTlD1 7s ease both; animation-play-state: running"></span></span>
<img src="{{ theme_asset('images/team-planning.jpg') }}" alt="Team planning with sticky notes" style="width: 400px; height: 224px; object-fit: cover; border-radius: 18px; display: block">
<div style="display: flex; flex-direction: column; gap: 10px">
<h3 style="margin: 0; font-size: 26px; line-height: 1.2; font-weight: 800; color: #13803F; letter-spacing: -0.01em">Embracing The Digital Era</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.65; color: #4A5866">We created Kouventa, an All-In-One Customer Engagement solution, to help businesses maintain and grow their customer engagement. We are also committed to providing great service and support to our clients who wish to maximize the usefulness of technology such as Generative AI technology, and API Integration.</p>
</div>
</div>
<div style="display: grid; grid-template-columns: 180px 48px 400px minmax(0, 1fr); gap: 32px; align-items: center; position: relative; animation: kvTlR2 7s ease both; animation-play-state: running">
<span style="font-size: 72px; line-height: 1; font-weight: 800; letter-spacing: -0.03em; color: #C3CDC8; text-align: right">2022</span>
<span style="width: 48px; display: flex; justify-content: center"><span style="width: 18px; height: 18px; border-radius: 50%; background: #2BD46A; box-shadow: 0 0 0 6px #F4F6F5, 0 0 0 8px rgba(43,212,106,0.35); animation: kvTlD2 7s ease both; animation-play-state: running"></span></span>
<img src="{{ theme_asset('images/generative-ai-concept.jpg') }}" alt="Generative AI concept" style="width: 400px; height: 224px; object-fit: cover; border-radius: 18px; display: block">
<div style="display: flex; flex-direction: column; gap: 10px">
<h3 style="margin: 0; font-size: 26px; line-height: 1.2; font-weight: 800; color: #13803F; letter-spacing: -0.01em">Advancing The Generative AI Solutions</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.65; color: #4A5866">Kouventa provides an AI Company Wikipedia solution based on Gen-AI technology, crafted to address enterprise demands for advanced AI adoption in complex operations.</p>
</div>
</div>
<div style="display: grid; grid-template-columns: 180px 48px 400px minmax(0, 1fr); gap: 32px; align-items: center; position: relative; animation: kvTlR3 7s ease both; animation-play-state: running">
<span style="font-size: 72px; line-height: 1; font-weight: 800; letter-spacing: -0.03em; color: #C3CDC8; text-align: right">2026</span>
<span style="width: 48px; display: flex; justify-content: center"><span style="width: 18px; height: 18px; border-radius: 50%; background: #2BD46A; box-shadow: 0 0 0 6px #F4F6F5, 0 0 0 8px rgba(43,212,106,0.35); animation: kvTlD3 7s ease both; animation-play-state: running"></span></span>
<img src="{{ theme_asset('images/agentic-ai-tablet.jpg') }}" alt="Agentic AI concept on a tablet" style="width: 400px; height: 224px; object-fit: cover; border-radius: 18px; display: block">
<div style="display: flex; flex-direction: column; gap: 10px">
<h3 style="margin: 0; font-size: 26px; line-height: 1.2; font-weight: 800; color: #13803F; letter-spacing: -0.01em">Agentic AI with AI Task Force</h3>
<p style="margin: 0; font-size: 16px; line-height: 1.65; color: #4A5866">Kouventa launched AI Task Force, its Agentic AI solution, allowing intelligent agents to reason, plan, and take action across enterprise applications to automate business processes from start to finish.</p>
</div>
</div>
</div>
</section>

<!-- ============ FOOTER ============ -->
</div>
@endsection
