@extends('iccom::layouts.app')

@section('title', $page->getMetaTitle())

@section('content')
    <section class="hero-section d-flex align-items-center position-relative py-5">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h1 class="display-4 fw-bold mb-4">{{ $page->getBlockValue('hero_title', 'Partner with iCCom') }}</h1>
                    <p class="lead mb-4">{{ $page->getBlockValue('hero_description', "Join forces with Indonesia's largest cloud community. Lets collaborate to nurture talent and drive innovation.") }}</p>
                </div>
                <div class="col-lg-6 text-center" data-aos="fade-left" data-aos-delay="200">
                    @if($page->getBlockValue('hero_image'))
                        <img src="{{ asset('storage/' . $page->getBlockValue('hero_image')) }}" class="img-fluid" style="max-height: 300px;">
                    @else
                        <img src="{{ asset('themes/iccom/assets/strategic-alliance-icon-with-white-bg.png') }}" class="img-fluid" style="max-height: 300px;">
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="partner-form-section py-5 bg-light">
        <div class="container">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden mx-auto" style="max-width: 800px;" data-aos="fade-up">
                <div class="card-header bg-primary text-white p-4 text-center">
                    <h3 class="fw-bold mb-0">Partnership Inquiry</h3>
                </div>
                <div class="card-body p-5">

                    @if (session('partner_success'))
                        <div class="alert alert-success text-center mb-4">
                            <i class="material-icons align-middle me-1">check_circle</i>
                            {{ session('partner_success') }}
                        </div>
                    @endif

                    <form action="{{ route('partner.store') }}" method="POST" novalidate>
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Company / Organization Name <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror"
                                    placeholder="Your Company Name" value="{{ old('company_name') }}" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Website</label>
                                <input type="url" name="website" class="form-control @error('website') is-invalid @enderror"
                                    placeholder="https://" value="{{ old('website') }}">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Contact Person Name <span class="text-danger">*</span></label>
                                <input type="text" name="contact_name" class="form-control @error('contact_name') is-invalid @enderror"
                                    placeholder="Full Name" value="{{ old('contact_name') }}" required>
                                @error('contact_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    placeholder="email@company.com" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Partnership Type <span class="text-danger">*</span></label>
                                <select name="partnership_type" class="form-select @error('partnership_type') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('partnership_type') ? '' : 'selected' }}>Choose partnership type...</option>
                                    <option value="corporate" {{ old('partnership_type') == 'corporate' ? 'selected' : '' }}>Corporate Partner (Sponsorship)</option>
                                    <option value="university" {{ old('partnership_type') == 'university' ? 'selected' : '' }}>University Partner (Education)</option>
                                    <option value="community" {{ old('partnership_type') == 'community' ? 'selected' : '' }}>Community Partner (Collaboration)</option>
                                    <option value="media" {{ old('partnership_type') == 'media' ? 'selected' : '' }}>Media Partner</option>
                                    <option value="other" {{ old('partnership_type') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('partnership_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Message / Proposal Summary <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="5"
                                    placeholder="Tell us how you'd like to partner with us..." required>{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 text-center mt-4">
                                <button type="submit" class="btn btn-warning btn-cta text-white rounded-pill px-5 py-3 fw-bold shadow">Submit Inquiry</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- Additional Page Builder Blocks --}}
    @foreach($blocks as $block)
        @if($block->is_active && !in_array($block->name, ['hero_title', 'hero_description', 'hero_image']))
            <section class="block-section py-4" data-block-name="{{ $block->name }}">
                <div class="container">
                    @switch($block->type)
                        @case('text')
                            <p class="fs-5 text-body">{{ $block->localizedValue }}</p>
                            @break
                        @case('textarea')
                            <div class="text-body">{!! nl2br(e($block->localizedValue)) !!}</div>
                            @break
                        @case('wysiwyg')
                            <div class="wysiwyg-content">{!! $block->localizedValue !!}</div>
                            @break
                        @case('media')
                            @if($block->value)
                                <img src="{{ asset('storage/' . $block->value) }}" alt="{{ $block->label }}" class="img-fluid rounded-4">
                            @endif
                            @break
                        @case('gallery')
                            @php $images = $block->getDecodedValue() ?? []; @endphp
                            @if(count($images) > 0)
                                <div class="row g-3">
                                    @foreach($images as $image)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <img src="{{ asset('storage/' . $image) }}" alt="Gallery" class="img-fluid rounded-3 w-100" style="height: 200px; object-fit: cover;">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @break
                        @case('repeater')
                            @php $rows = $block->localizedValue(); if (!is_array($rows)) $rows = []; @endphp
                            @if(count($rows) > 0)
                                <div class="row g-4">
                                    @foreach($rows as $row)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card border-0 shadow-sm h-100 rounded-4">
                                                <div class="card-body">
                                                    @foreach($block->childBlocks as $childBlock)
                                                        @if($childBlock->is_active && isset($row[$childBlock->name]))
                                                            @if($childBlock->type === 'media' && $row[$childBlock->name])
                                                                <img src="{{ asset('storage/' . $row[$childBlock->name]) }}" alt="{{ $childBlock->label }}" class="img-fluid rounded-3 mb-2 w-100" style="height: 160px; object-fit: cover;">
                                                            @else
                                                                <small class="text-muted d-block fw-bold text-uppercase">{{ $childBlock->label }}</small>
                                                                <span class="text-body">{{ $row[$childBlock->name] }}</span>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @break
                        @default
                            @includeFirst(['iccom::pages._block_' . $block->type, 'pages._block_' . $block->type], ['block' => $block])
                    @endswitch
                </div>
            </section>
        @endif
    @endforeach
@endsection
