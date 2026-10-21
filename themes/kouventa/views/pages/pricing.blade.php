@extends('kouventa::layouts.app')

@php
    $heroBadge = $page?->block('hero_badge', 'PRICING');
    $heroTitle = $page?->titleBlock('hero_title', ['prefix' => 'Intelligence you control,', 'main' => 'priced for how you grow.']);
    $heroSubtitle = $page?->block('hero_subtitle', 'Two platforms, three tiers each. Start where your team is today, and scale without switching vendors.');
    $calcTitle = $page?->block('calc_title', 'Estimate your WhatsApp cost');
    $calcSubtitle = $page?->block('calc_subtitle', 'Enter your expected monthly conversations per category to see your estimated monthly and yearly cost.');
    $ctaTitle = $page?->block('cta_title', 'Ready to see how your team would use it?');
    $ctaSubtitle = $page?->block('cta_subtitle', "Book a working session with our solution team. We'll map your use cases to the right tier and deployment.");

    // Structured comparison data
    $ceCategories = array (
  0 => 
  array (
    0 => 'Usage limits',
    1 => 
    array (
      0 => 
      array (
        0 => 'Monthly active users',
        1 => 'Up to 3,000',
        2 => 'Up to 15,000',
        3 => 'Customizable',
      ),
      1 => 
      array (
        0 => 'Contact database',
        1 => '10,000',
        2 => '45,000',
        3 => 'Customizable',
      ),
    ),
  ),
  1 => 
  array (
    0 => 'Channels',
    1 => 
    array (
      0 => 
      array (
        0 => 'WhatsApp',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      1 => 
      array (
        0 => 'Instagram',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      2 => 
      array (
        0 => 'Facebook',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      3 => 
      array (
        0 => 'Livechat',
        1 => 'Add-on',
        2 => 'Add-on',
        3 => 'Add-on',
      ),
    ),
  ),
  2 => 
  array (
    0 => 'Inbound handling',
    1 => 
    array (
      0 => 
      array (
        0 => 'Business profile configuration',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      1 => 
      array (
        0 => 'Static chatbot',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      2 => 
      array (
        0 => 'AI chatbot',
        1 => 'Add-on',
        2 => 'Add-on',
        3 => 'Add-on',
      ),
      3 => 
      array (
        0 => 'Customer service agents',
        1 => 'Unlimited',
        2 => 'Unlimited',
        3 => 'Unlimited',
      ),
      4 => 
      array (
        0 => 'Supervisor mode',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      5 => 
      array (
        0 => 'Agent handoff & routing',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      6 => 
      array (
        0 => 'Response presets',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      7 => 
      array (
        0 => 'Agent notifications',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      8 => 
      array (
        0 => 'Conversation search',
        1 => 'Add-on',
        2 => '✓',
        3 => '✓',
      ),
      9 => 
      array (
        0 => 'CSAT feedback',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
    ),
  ),
  3 => 
  array (
    0 => 'Outbound (WhatsApp)',
    1 => 
    array (
      0 => 
      array (
        0 => 'Broadcast scheduling',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      1 => 
      array (
        0 => 'Message templates (marketing, service, utility, OTP)',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      2 => 
      array (
        0 => 'Media integration (image & video)',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      3 => 
      array (
        0 => 'Form builder (WhatsApp Flow)',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      4 => 
      array (
        0 => 'Media storage',
        1 => '100 GB',
        2 => '250 GB',
        3 => 'Customizable',
      ),
    ),
  ),
  4 => 
  array (
    0 => 'Contact management',
    1 => 
    array (
      0 => 
      array (
        0 => 'Contact labeling',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      1 => 
      array (
        0 => 'Contact grouping',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      2 => 
      array (
        0 => 'Conversation history',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
    ),
  ),
  5 => 
  array (
    0 => 'Platform control',
    1 => 
    array (
      0 => 
      array (
        0 => 'Role-based access',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      1 => 
      array (
        0 => 'Multi-factor authentication',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      2 => 
      array (
        0 => 'Multiple numbers & accounts',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      3 => 
      array (
        0 => 'Analytics dashboard',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      4 => 
      array (
        0 => 'API integration',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      5 => 
      array (
        0 => 'Activity log',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      6 => 
      array (
        0 => 'Data masking',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
    ),
  ),
  6 => 
  array (
    0 => 'Data retention',
    1 => 
    array (
      0 => 
      array (
        0 => 'Conversation history retention',
        1 => '365 days',
        2 => '365 days',
        3 => 'Customizable',
      ),
      1 => 
      array (
        0 => 'Audit log retention',
        1 => '365 days',
        2 => '365 days',
        3 => 'Customizable',
      ),
    ),
  ),
  7 => 
  array (
    0 => 'Service level & support',
    1 => 
    array (
      0 => 
      array (
        0 => 'Uptime SLA',
        1 => '99.5%',
        2 => '99.5%',
        3 => '99.9%',
      ),
      1 => 
      array (
        0 => 'Technical support',
        1 => 'Standard 9×5',
        2 => 'Standard 9×5',
        3 => 'Customizable, up to 24×7',
      ),
      2 => 
      array (
        0 => 'Response time',
        1 => '≤ 4 hours',
        2 => '≤ 4 hours',
        3 => '≤ 4 hours',
      ),
      3 => 
      array (
        0 => 'Warranty',
        1 => '1 year',
        2 => '1 year',
        3 => '1 year',
      ),
    ),
  ),
  8 => 
  array (
    0 => 'Deployment & implementation',
    1 => 
    array (
      0 => 
      array (
        0 => 'Deployment mode',
        1 => 'SaaS cloud',
        2 => 'SaaS cloud',
        3 => 'Hybrid / private cloud / on-premise',
      ),
      1 => 
      array (
        0 => 'Standard implementation',
        1 => 'Guided + user guideline',
        2 => 'Guided + user guideline',
        3 => 'Guided + user guideline',
      ),
      2 => 
      array (
        0 => 'Custom implementation',
        1 => 'Add-on',
        2 => 'Add-on',
        3 => 'Add-on',
      ),
    ),
  ),
);
    $aiCategories = array (
  0 => 
  array (
    0 => 'Business use cases',
    1 => 
    array (
      0 => 
      array (
        0 => 'Company Wikipedia (Generative AI)',
        1 => 'Unlimited',
        2 => 'Unlimited',
        3 => 'Unlimited',
      ),
      1 => 
      array (
        0 => 'Task Force (Agentic AI)',
        1 => '—',
        2 => '3',
        3 => 'Unlimited',
      ),
    ),
  ),
  1 => 
  array (
    0 => 'Generative AI: Company Wikipedia',
    1 => 
    array (
      0 => 
      array (
        0 => 'Dynamic model selection',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      1 => 
      array (
        0 => 'Search the web',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      2 => 
      array (
        0 => 'Persona configuration',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      3 => 
      array (
        0 => 'Multi-language processing',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      4 => 
      array (
        0 => 'Audio-to-text processing',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      5 => 
      array (
        0 => 'Image-to-text processing',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      6 => 
      array (
        0 => 'Prompt template configuration',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
    ),
  ),
  2 => 
  array (
    0 => 'Agentic AI: Task Force',
    1 => 
    array (
      0 => 
      array (
        0 => 'Task scheduler',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
      1 => 
      array (
        0 => 'Dynamic model selection',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
      2 => 
      array (
        0 => 'Persona & work step configuration',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
      3 => 
      array (
        0 => 'Multi-language processing',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
      4 => 
      array (
        0 => 'Audio-to-text processing',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
      5 => 
      array (
        0 => 'Image-to-text processing',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
      6 => 
      array (
        0 => 'Multi-agent configuration',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
    ),
  ),
  3 => 
  array (
    0 => 'Data source management',
    1 => 
    array (
      0 => 
      array (
        0 => 'Embedding',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      1 => 
      array (
        0 => 'Grouping',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      2 => 
      array (
        0 => 'Change approval workflow',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      3 => 
      array (
        0 => 'Documents (PDF, Word, Excel, PPT, TXT, EML, PST)',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      4 => 
      array (
        0 => 'Web sources (HTML, website, SharePoint, Google Drive)',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      5 => 
      array (
        0 => 'Structured data (CSV/TSV, SQL, SQL API)',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      6 => 
      array (
        0 => 'Media (Image, TIFF)',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
    ),
  ),
  4 => 
  array (
    0 => 'Connectors & tools',
    1 => 
    array (
      0 => 
      array (
        0 => 'File creation (Text, PDF, Word, Excel)',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
      1 => 
      array (
        0 => 'Plot & chart generation',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
      2 => 
      array (
        0 => 'Web scraping',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
      3 => 
      array (
        0 => 'Slack workspace',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
      4 => 
      array (
        0 => 'SAP OData',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
      5 => 
      array (
        0 => 'RAG datasource',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
      6 => 
      array (
        0 => 'SQL database',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
      7 => 
      array (
        0 => 'Custom connectors',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
    ),
  ),
  5 => 
  array (
    0 => 'LLM & usage',
    1 => 
    array (
      0 => 
      array (
        0 => 'Curated LLM',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      1 => 
      array (
        0 => 'Custom LLM',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
      2 => 
      array (
        0 => 'AI token (curated model)',
        1 => 'Add-on',
        2 => 'Add-on',
        3 => 'Add-on',
      ),
      3 => 
      array (
        0 => 'API call token',
        1 => '100k / month',
        2 => '400k / month',
        3 => 'Unlimited (full on-prem)',
      ),
      4 => 
      array (
        0 => 'Data storage',
        1 => '100 GB',
        2 => '500 GB',
        3 => 'Customizable',
      ),
    ),
  ),
  6 => 
  array (
    0 => 'Sandbox & learning',
    1 => 
    array (
      0 => 
      array (
        0 => 'Trial run feedback identification',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      1 => 
      array (
        0 => 'Trial run usage feasibility',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      2 => 
      array (
        0 => 'AI learning',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      3 => 
      array (
        0 => 'Feedback management',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
    ),
  ),
  7 => 
  array (
    0 => 'Platform access',
    1 => 
    array (
      0 => 
      array (
        0 => 'Platform seats',
        1 => 'Unlimited',
        2 => 'Unlimited',
        3 => 'Customizable',
      ),
      1 => 
      array (
        0 => 'Chat playground seats',
        1 => 'Up to 300',
        2 => 'Unlimited',
        3 => 'Customizable',
      ),
      2 => 
      array (
        0 => 'Analytics',
        1 => 'Standard',
        2 => 'Advanced',
        3 => 'Advanced',
      ),
    ),
  ),
  8 => 
  array (
    0 => 'Access management',
    1 => 
    array (
      0 => 
      array (
        0 => 'Platform-level role-based access',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      1 => 
      array (
        0 => 'Datasource-level access',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      2 => 
      array (
        0 => 'Runner-level access',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      3 => 
      array (
        0 => 'Agent-level access',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
    ),
  ),
  9 => 
  array (
    0 => 'Platform control',
    1 => 
    array (
      0 => 
      array (
        0 => 'AI guardrails & PII masking',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      1 => 
      array (
        0 => 'Audit trail logs',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
    ),
  ),
  10 => 
  array (
    0 => 'Delivery channels',
    1 => 
    array (
      0 => 
      array (
        0 => 'Livechat',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      1 => 
      array (
        0 => 'WhatsApp',
        1 => 'Add-on',
        2 => 'Add-on',
        3 => 'Add-on',
      ),
      2 => 
      array (
        0 => 'Chat playground',
        1 => '—',
        2 => '✓',
        3 => '✓',
      ),
      3 => 
      array (
        0 => 'SDK',
        1 => '✓',
        2 => '✓',
        3 => '✓',
      ),
      4 => 
      array (
        0 => 'Workflow automation management',
        1 => '—',
        2 => '—',
        3 => '✓',
      ),
    ),
  ),
  11 => 
  array (
    0 => 'Data retention',
    1 => 
    array (
      0 => 
      array (
        0 => 'Conversation history retention',
        1 => '365 days',
        2 => '365 days',
        3 => 'Customizable',
      ),
      1 => 
      array (
        0 => 'Audit log retention',
        1 => '365 days',
        2 => '365 days',
        3 => 'Customizable',
      ),
    ),
  ),
  12 => 
  array (
    0 => 'Service level & support',
    1 => 
    array (
      0 => 
      array (
        0 => 'Uptime SLA',
        1 => '99.5%',
        2 => '99.5%',
        3 => '99.9%',
      ),
      1 => 
      array (
        0 => 'Technical support',
        1 => 'Standard 9×5',
        2 => 'Standard 9×5',
        3 => 'Customizable, up to 24×7',
      ),
      2 => 
      array (
        0 => 'Response time',
        1 => '≤ 4 hours',
        2 => '≤ 4 hours',
        3 => '≤ 4 hours',
      ),
      3 => 
      array (
        0 => 'Warranty',
        1 => '1 year',
        2 => '1 year',
        3 => '1 year',
      ),
    ),
  ),
  13 => 
  array (
    0 => 'Deployment & implementation',
    1 => 
    array (
      0 => 
      array (
        0 => 'Deployment mode',
        1 => 'SaaS cloud',
        2 => 'SaaS cloud',
        3 => 'Hybrid / private cloud / on-premise',
      ),
      1 => 
      array (
        0 => 'Standard implementation',
        1 => 'Guided + user guideline',
        2 => 'Guided + user guideline',
        3 => 'Guided + user guideline',
      ),
      2 => 
      array (
        0 => 'Custom implementation',
        1 => 'Add-on',
        2 => 'Add-on',
        3 => 'Add-on',
      ),
    ),
  ),
);
    $ceAddons = array (
  0 => 
  array (
    0 => 'Livechat channel',
    1 => 'Add web-based livechat to your omnichannel inbox.',
  ),
  1 => 
  array (
    0 => 'AI chatbot',
    1 => 'Company Wikipedia starter package with RAG-powered responses.',
  ),
  2 => 
  array (
    0 => 'Extended contact database',
    1 => 'Additional contact capacity for growing customer bases.',
  ),
  3 => 
  array (
    0 => 'Extended media storage',
    1 => 'Additional storage for shared images and video.',
  ),
  4 => 
  array (
    0 => 'Conversation search',
    1 => 'Full-text search across historical conversations.',
  ),
  5 => 
  array (
    0 => 'Extended data retention',
    1 => 'Keep conversation history and audit logs beyond 365 days.',
  ),
  6 => 
  array (
    0 => 'Golden 9×5 support',
    1 => 'Extended business-hours support with change request tickets.',
  ),
  7 => 
  array (
    0 => 'Platinum 24×7 support',
    1 => 'Round-the-clock coverage with prioritized change requests.',
  ),
  8 => 
  array (
    0 => 'Training sessions',
    1 => 'Guided onboarding for your team, online or in Jabodetabek.',
  ),
  9 => 
  array (
    0 => 'Custom development',
    1 => 'Custom APIs, dashboards, channels, or integrations.',
  ),
);
    $aiAddons = array (
  0 => 
  array (
    0 => 'Customer Engagement platform',
    1 => 'Add WhatsApp delivery channel with omnichannel inbox.',
  ),
  1 => 
  array (
    0 => 'Additional Task Force use case',
    1 => 'Deploy more agentic workflows beyond your plan\'s included quota.',
  ),
  2 => 
  array (
    0 => 'AI token pack',
    1 => 'Additional token capacity for curated model usage.',
  ),
  3 => 
  array (
    0 => 'API call token pack',
    1 => 'Expand your monthly API call quota.',
  ),
  4 => 
  array (
    0 => 'Extended storage',
    1 => 'Additional storage capacity for datasources and embeddings.',
  ),
  5 => 
  array (
    0 => 'Extended data retention',
    1 => 'Keep conversation history and audit logs beyond 365 days.',
  ),
  6 => 
  array (
    0 => 'Golden 9×5 support',
    1 => 'Extended business-hours support with change request tickets.',
  ),
  7 => 
  array (
    0 => 'Platinum 24×7 support',
    1 => 'Round-the-clock coverage with prioritized change requests.',
  ),
  8 => 
  array (
    0 => 'Training sessions',
    1 => 'Guided onboarding for your team, online or in Jabodetabek.',
  ),
  9 => 
  array (
    0 => 'Custom development',
    1 => 'Custom APIs, dashboards, channels, or integrations.',
  ),
);

    $templates = [
        [
            'key' => 'mk',
            'name' => 'Marketing',
            'items' => ['Greetings, events and promotions', 'New product announcements', 'Cart abandonment reminders'],
        ],
        [
            'key' => 'ut',
            'name' => 'Utility',
            'items' => ['Follow-ups on customer actions or requests', 'Opt-in confirmations', 'Account updates and alerts, like payment reminders', 'Feedback surveys'],
        ],
        [
            'key' => 'au',
            'name' => 'Authentication',
            'items' => ['One-time passcodes to verify users', 'Login, account recovery, payment and integrity checks'],
        ],
        [
            'key' => 'sv',
            'name' => 'Service',
            'items' => ['Personalized customer conversations', 'Order assistance', 'Proactive issue resolution', 'Interactive product recommendations'],
        ],
    ];
@endphp

@section('content')
<div x-data="{
    product: (new URLSearchParams(window.location.search).get('tab') === 'ai' ? 'ai' : 'ce'),
    openCats: { 'ai_0': true, 'ce_0': true },
    setTab(tab) {
        this.product = tab;
        try {
            const u = new URL(window.location);
            u.searchParams.set('tab', tab);
            history.replaceState(null, '', u.pathname + u.search + u.hash);
        } catch (e) {}
    },
    toggleCat(key) {
        this.openCats[key] = !this.openCats[key];
    },
    isCatOpen(key, defaultVal = false) {
        return this.openCats[key] !== undefined ? this.openCats[key] : defaultVal;
    },
    expandAll() {
        for (let i = 0; i < 20; i++) {
            this.openCats['ai_' + i] = true;
            this.openCats['ce_' + i] = true;
        }
    },
    collapseAll() {
        this.openCats = {};
    },
    ...whatsappCalculator()
}" 
x-init="window.addEventListener('popstate', () => { const t = new URLSearchParams(window.location.search).get('tab'); if (t === 'ai' || t === 'ce') product = t; })"
class="kv-canvas-wrap">

<!-- ============ HERO & PLATFORM TOGGLE ============ -->
<section style="background: #071C33; color: #FFFFFF; padding: 96px 80px 104px; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 24px; position: relative">
    <div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none">
        <div style="position: absolute; left: 50%; top: -200px; width: 1000px; height: 1000px; margin-left: -500px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div>
    </div>
    
    <span style="font-size: 13px; font-weight: 800; letter-spacing: 0.1em; color: #2BD46A; text-transform: uppercase">{{ $heroBadge }}</span>
    
    <h1 style="margin: 0; font-size: 72px; line-height: 1.04; font-weight: 800; letter-spacing: -0.035em; max-width: 960px">
        {{ $heroTitle['prefix'] ?? 'Intelligence you control,' }}<br><span style="color: #2BD46A">{{ $heroTitle['main'] ?? 'priced for how you grow.' }}</span>
    </h1>
    
    <p style="margin: 0; font-size: 21px; line-height: 1.5; color: #B9C6D3; max-width: 660px">
        {!! nl2br(e($heroSubtitle)) !!}
    </p>

    <!-- Platform Toggle -->
    <div id="pricing-toggle" role="group" aria-label="Choose a platform" style="align-self: center; display: flex; padding: 5px; background: rgba(255,255,255,0.08); border-radius: 14px; gap: 4px; margin-top: 12px; z-index: 10">
        <button type="button" @click="setTab('ai')"
                :class="{ 'is-active': product === 'ai' }"
                class="kv-toggle-btn">
            {{ t('kouventa.Artificial Intelligence', 'Artificial Intelligence') }}
        </button>
        <button type="button" @click="setTab('ce')"
                :class="{ 'is-active': product === 'ce' }"
                class="kv-toggle-btn">
            {{ t('kouventa.Customer Engagement', 'Customer Engagement') }}
        </button>
    </div>
</section>

<!-- ============ PRODUCT PANEL ============ -->
<section style="padding: 104px 80px; display: flex; flex-direction: column; gap: 104px; background: #F4F6F5">

    <!-- ============ TIER CARDS ============ -->
    <div style="display: flex; flex-direction: column; gap: 24px">
        <h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em; color: #0E1A24"
            x-text="product === 'ai' ? '{{ t('kouventa.Artificial Intelligence', 'Artificial Intelligence') }}' : '{{ t('kouventa.Customer Engagement', 'Customer Engagement') }}'">
            Customer Engagement
        </h2>

        <!-- AI TIERS -->
        <div x-show="product === 'ai'" :style="{ display: product === 'ai' ? 'grid' : 'none' }" class="kv-pricing-tier-grid">
            <!-- AI Starter -->
            <article style="background: #FFFFFF; color: #0E1A24; border: 1px solid #E3E8E6; border-radius: 24px; padding: 36px; display: flex; flex-direction: column; gap: 18px; position: relative">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px">
                    <span style="font-size: 30px; font-weight: 800">{{ t('kouventa.Starter', 'Starter') }}</span>
                </div>
                <span style="font-size: 16px; line-height: 1.5; min-height: 48px; color: #4A5866">{{ t('kouventa.Bring AI to your first workflow.', 'Bring AI to your first workflow.') }}</span>
                <div style="display: flex; flex-direction: column; gap: 4px">
                    <span style="font-size: 40px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">IDR 5.991.667</span>
                    <span style="font-size: 14px; color: #4A5866">{{ t('kouventa.per month, billed annually', 'per month, billed annually') }}</span>
                </div>
                <span style="display: flex; align-items: center; gap: 10px; min-height: 48px; box-sizing: border-box; padding: 10px 14px; border-radius: 12px; background: #EAF7EF; color: #13803F; font-size: 13px; font-weight: 700; line-height: 1.35">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>{{ t('kouventa.Unlimited Company Wikipedia use cases', 'Unlimited Company Wikipedia use cases') }}</span>
                </span>
                <ul style="margin: 0; padding: 18px 0 0; list-style: none; border-top: 1px solid #E3E8E6; display: flex; flex-direction: column; gap: 12px; flex-grow: 1">
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Generative AI with source citation', 'Generative AI with source citation') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.19+ supported data source types', '19+ supported data source types') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Curated LLM with dynamic selection', 'Curated LLM with dynamic selection') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.100 GB storage, 100k API calls/month', '100 GB storage, 100k API calls/month') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.AI guardrails & PII masking', 'AI guardrails & PII masking') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Livechat delivery, SDK access', 'Livechat delivery, SDK access') }}</span></li>
                </ul>
                <a href="#whatsapp" @click.prevent="$dispatch('open-whatsapp-modal')" data-open-whatsapp class="kv-btn-action kv-btn-navy" style="margin-top: 8px; width: 100%; box-sizing: border-box; background: #071C33; color: #FFFFFF; font-size: 16px; font-weight: 700; padding: 16px 24px; border-radius: 12px; text-align: center; text-decoration: none; cursor: pointer">{{ t('kouventa.Talk to sales', 'Talk to sales') }}</a>
            </article>

            <!-- AI Grow (Most Popular) -->
            <article style="background: #071C33; color: #FFFFFF; border: 1px solid #071C33; border-radius: 24px; padding: 36px; display: flex; flex-direction: column; gap: 18px; position: relative; box-shadow: 0 24px 60px rgba(7,28,51,0.2)">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px">
                    <span style="font-size: 30px; font-weight: 800">{{ t('kouventa.Grow', 'Grow') }}</span>
                    <span style="font-size: 12px; font-weight: 800; padding: 6px 12px; border-radius: 999px; background: #2BD46A; color: #06251A">{{ t('kouventa.Most popular', 'Most popular') }}</span>
                </div>
                <span style="font-size: 16px; line-height: 1.5; min-height: 48px; color: #B9C6D3">{{ t('kouventa.Add agentic capability across your teams.', 'Add agentic capability across your teams.') }}</span>
                <div style="display: flex; flex-direction: column; gap: 4px">
                    <span style="font-size: 40px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Custom pricing', 'Custom pricing') }}</span>
                    <span style="font-size: 14px; color: #B9C6D3">{{ t('kouventa.annual subscription', 'annual subscription') }}</span>
                </div>
                <span style="display: flex; align-items: center; gap: 10px; min-height: 48px; box-sizing: border-box; padding: 10px 14px; border-radius: 12px; background: rgba(43,212,106,0.14); color: #7BE8A6; font-size: 13px; font-weight: 700; line-height: 1.35">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>{{ t('kouventa.Unlimited Wikipedia + 3 Task Force use cases', 'Unlimited Wikipedia + 3 Task Force use cases') }}</span>
                </span>
                <ul style="margin: 0; padding: 18px 0 0; list-style: none; border-top: 1px solid rgba(255,255,255,0.14); display: flex; flex-direction: column; gap: 12px; flex-grow: 1">
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Everything in Starter', 'Everything in Starter') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Agentic AI with multi-agent orchestration', 'Agentic AI with multi-agent orchestration') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Custom LLM support', 'Custom LLM support') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Connectors: Slack, SAP, SQL, custom', 'Connectors: Slack, SAP, SQL, custom') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.500 GB storage, chat playground', '500 GB storage, chat playground') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.File & chart generation tools', 'File & chart generation tools') }}</span></li>
                </ul>
                <a href="#whatsapp" @click.prevent="$dispatch('open-whatsapp-modal')" data-open-whatsapp class="kv-btn-action kv-btn-primary" style="margin-top: 8px; width: 100%; box-sizing: border-box; background: #2BD46A; color: #06251A; font-size: 16px; font-weight: 700; padding: 16px 24px; border-radius: 12px; text-align: center; text-decoration: none; cursor: pointer">{{ t('kouventa.Talk to sales', 'Talk to sales') }}</a>
            </article>

            <!-- AI Scale -->
            <article style="background: #FFFFFF; color: #0E1A24; border: 1px solid #E3E8E6; border-radius: 24px; padding: 36px; display: flex; flex-direction: column; gap: 18px; position: relative">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px">
                    <span style="font-size: 30px; font-weight: 800">{{ t('kouventa.Scale', 'Scale') }}</span>
                </div>
                <span style="font-size: 16px; line-height: 1.5; min-height: 48px; color: #4A5866">{{ t('kouventa.Enterprise AI, fully orchestrated.', 'Enterprise AI, fully orchestrated.') }}</span>
                <div style="display: flex; flex-direction: column; gap: 4px">
                    <span style="font-size: 40px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Custom pricing', 'Custom pricing') }}</span>
                    <span style="font-size: 14px; color: #4A5866">{{ t('kouventa.custom scope, one-time or subscription', 'custom scope, one-time or subscription') }}</span>
                </div>
                <span style="display: flex; align-items: center; gap: 10px; min-height: 48px; box-sizing: border-box; padding: 10px 14px; border-radius: 12px; background: #EAF7EF; color: #13803F; font-size: 13px; font-weight: 700; line-height: 1.35">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>{{ t('kouventa.Unlimited use cases & workflows', 'Unlimited use cases & workflows') }}</span>
                </span>
                <ul style="margin: 0; padding: 18px 0 0; list-style: none; border-top: 1px solid #E3E8E6; display: flex; flex-direction: column; gap: 12px; flex-grow: 1">
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Everything in Grow', 'Everything in Grow') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Full workflow automation orchestration', 'Full workflow automation orchestration') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Hybrid, private cloud, or on-premise', 'Hybrid, private cloud, or on-premise') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Custom connectors & fine-tuning', 'Custom connectors & fine-tuning') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Dedicated engineering pod', 'Dedicated engineering pod') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Audit trail & change approval gates', 'Audit trail & change approval gates') }}</span></li>
                </ul>
                <a href="#whatsapp" @click.prevent="$dispatch('open-whatsapp-modal')" data-open-whatsapp class="kv-btn-action kv-btn-outline" style="margin-top: 8px; width: 100%; box-sizing: border-box; background: transparent; color: #0E1A24; font-size: 16px; font-weight: 700; padding: 15px 24px; border-radius: 12px; border: 1.5px solid #0E1A24; text-align: center; text-decoration: none; cursor: pointer">{{ t('kouventa.Talk to sales', 'Talk to sales') }}</a>
            </article>
        </div>

        <!-- CE TIERS -->
        <div x-show="product === 'ce'" :style="{ display: product === 'ce' ? 'grid' : 'none' }" class="kv-pricing-tier-grid">
            <!-- CE Starter -->
            <article style="background: #FFFFFF; color: #0E1A24; border: 1px solid #E3E8E6; border-radius: 24px; padding: 36px; display: flex; flex-direction: column; gap: 18px; position: relative">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px">
                    <span style="font-size: 30px; font-weight: 800">{{ t('kouventa.Starter', 'Starter') }}</span>
                </div>
                <span style="font-size: 16px; line-height: 1.5; min-height: 48px; color: #4A5866">{{ t('kouventa.For teams starting their omnichannel journey.', 'For teams starting their omnichannel journey.') }}</span>
                <div style="display: flex; flex-direction: column; gap: 4px">
                    <span style="font-size: 40px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">IDR 1.499.000</span>
                    <span style="font-size: 14px; color: #4A5866">{{ t('kouventa.per month', 'per month') }}</span>
                </div>
                <span style="display: flex; align-items: center; gap: 10px; min-height: 48px; box-sizing: border-box; padding: 10px 14px; border-radius: 12px; background: #EAF7EF; color: #13803F; font-size: 13px; font-weight: 700; line-height: 1.35">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>{{ t('kouventa.Up to 3,000 monthly active users', 'Up to 3,000 monthly active users') }}</span>
                </span>
                <ul style="margin: 0; padding: 18px 0 0; list-style: none; border-top: 1px solid #E3E8E6; display: flex; flex-direction: column; gap: 12px; flex-grow: 1">
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.WhatsApp, Instagram, Facebook', 'WhatsApp, Instagram, Facebook') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Unlimited customer service agents', 'Unlimited customer service agents') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Static chatbot & agent routing', 'Static chatbot & agent routing') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.10,000 contact database', '10,000 contact database') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Analytics, role-based access, MFA', 'Analytics, role-based access, MFA') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.SaaS cloud deployment', 'SaaS cloud deployment') }}</span></li>
                </ul>
                <a href="#whatsapp" @click.prevent="$dispatch('open-whatsapp-modal')" data-open-whatsapp class="kv-btn-action kv-btn-navy" style="margin-top: 8px; width: 100%; box-sizing: border-box; background: #071C33; color: #FFFFFF; font-size: 16px; font-weight: 700; padding: 16px 24px; border-radius: 12px; text-align: center; text-decoration: none; cursor: pointer">{{ t('kouventa.Talk to sales', 'Talk to sales') }}</a>
            </article>

            <!-- CE Professional (Most Popular) -->
            <article style="background: #071C33; color: #FFFFFF; border: 1px solid #071C33; border-radius: 24px; padding: 36px; display: flex; flex-direction: column; gap: 18px; position: relative; box-shadow: 0 24px 60px rgba(7,28,51,0.2)">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px">
                    <span style="font-size: 30px; font-weight: 800">{{ t('kouventa.Professional', 'Professional') }}</span>
                    <span style="font-size: 12px; font-weight: 800; padding: 6px 12px; border-radius: 999px; background: #2BD46A; color: #06251A">{{ t('kouventa.Most popular', 'Most popular') }}</span>
                </div>
                <span style="font-size: 16px; line-height: 1.5; min-height: 48px; color: #B9C6D3">{{ t('kouventa.For growing teams scaling engagement across channels.', 'For growing teams scaling engagement across channels.') }}</span>
                <div style="display: flex; flex-direction: column; gap: 4px">
                    <span style="font-size: 40px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">IDR 4.499.000</span>
                    <span style="font-size: 14px; color: #B9C6D3">{{ t('kouventa.per month', 'per month') }}</span>
                </div>
                <span style="display: flex; align-items: center; gap: 10px; min-height: 48px; box-sizing: border-box; padding: 10px 14px; border-radius: 12px; background: rgba(43,212,106,0.14); color: #7BE8A6; font-size: 13px; font-weight: 700; line-height: 1.35">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>{{ t('kouventa.Up to 15,000 monthly active users', 'Up to 15,000 monthly active users') }}</span>
                </span>
                <ul style="margin: 0; padding: 18px 0 0; list-style: none; border-top: 1px solid rgba(255,255,255,0.14); display: flex; flex-direction: column; gap: 12px; flex-grow: 1">
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Everything in Starter', 'Everything in Starter') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Conversation search across history', 'Conversation search across history') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.45,000 contact database', '45,000 contact database') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.WhatsApp Flow forms & templates', 'WhatsApp Flow forms & templates') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Broadcast scheduling & CSAT', 'Broadcast scheduling & CSAT') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Standard 9×5 support, 99.5% uptime', 'Standard 9×5 support, 99.5% uptime') }}</span></li>
                </ul>
                <a href="#whatsapp" @click.prevent="$dispatch('open-whatsapp-modal')" data-open-whatsapp class="kv-btn-action kv-btn-primary" style="margin-top: 8px; width: 100%; box-sizing: border-box; background: #2BD46A; color: #06251A; font-size: 16px; font-weight: 700; padding: 16px 24px; border-radius: 12px; text-align: center; text-decoration: none; cursor: pointer">{{ t('kouventa.Talk to sales', 'Talk to sales') }}</a>
            </article>

            <!-- CE Enterprise -->
            <article style="background: #FFFFFF; color: #0E1A24; border: 1px solid #E3E8E6; border-radius: 24px; padding: 36px; display: flex; flex-direction: column; gap: 18px; position: relative">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px">
                    <span style="font-size: 30px; font-weight: 800">{{ t('kouventa.Enterprise', 'Enterprise') }}</span>
                </div>
                <span style="font-size: 16px; line-height: 1.5; min-height: 48px; color: #4A5866">{{ t('kouventa.Full control for enterprise-scale operations.', 'Full control for enterprise-scale operations.') }}</span>
                <div style="display: flex; flex-direction: column; gap: 4px">
                    <span style="font-size: 40px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em">{{ t('kouventa.Custom pricing', 'Custom pricing') }}</span>
                    <span style="font-size: 14px; color: #4A5866">{{ t('kouventa.custom scope, one-time or subscription', 'custom scope, one-time or subscription') }}</span>
                </div>
                <span style="display: flex; align-items: center; gap: 10px; min-height: 48px; box-sizing: border-box; padding: 10px 14px; border-radius: 12px; background: #EAF7EF; color: #13803F; font-size: 13px; font-weight: 700; line-height: 1.35">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>{{ t('kouventa.Customizable MAU & contact database', 'Customizable MAU & contact database') }}</span>
                </span>
                <ul style="margin: 0; padding: 18px 0 0; list-style: none; border-top: 1px solid #E3E8E6; display: flex; flex-direction: column; gap: 12px; flex-grow: 1">
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Everything in Professional', 'Everything in Professional') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Hybrid, private cloud, or on-premise', 'Hybrid, private cloud, or on-premise') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Platinum 24×7 support', 'Platinum 24×7 support') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.99.9% uptime SLA', '99.9% uptime SLA') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Dedicated implementation team', 'Dedicated implementation team') }}</span></li>
                    <li style="display: flex; gap: 12px; align-items: flex-start; font-size: 16px; line-height: 1.45"><span style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></span><span>{{ t('kouventa.Data masking & audit trail', 'Data masking & audit trail') }}</span></li>
                </ul>
                <a href="#whatsapp" @click.prevent="$dispatch('open-whatsapp-modal')" data-open-whatsapp class="kv-btn-action kv-btn-outline" style="margin-top: 8px; width: 100%; box-sizing: border-box; background: transparent; color: #0E1A24; font-size: 16px; font-weight: 700; padding: 15px 24px; border-radius: 12px; border: 1.5px solid #0E1A24; text-align: center; text-decoration: none; cursor: pointer">{{ t('kouventa.Talk to sales', 'Talk to sales') }}</a>
            </article>
        </div>
    </div>

    <!-- ============ COMPARE EVERY FEATURE (TABLE) ============ -->
    <div style="display: flex; flex-direction: column; gap: 32px">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; flex-wrap: wrap">
            <div style="display: flex; flex-direction: column; gap: 10px">
                <h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em; color: #0E1A24">
                    {{ t('kouventa.Compare every feature', 'Compare every feature') }}
                </h2>
                <p style="margin: 0; font-size: 16px; color: #4A5866"
                   x-text="product === 'ai' ? '{{ t('kouventa.The full picture of what each Artificial Intelligence tier includes.', 'The full picture of what each Artificial Intelligence tier includes.') }}' : '{{ t('kouventa.The full picture of what each Customer Engagement tier includes.', 'The full picture of what each Customer Engagement tier includes.') }}'">
                    {{ t('kouventa.The full picture of what each Customer Engagement tier includes.', 'The full picture of what each Customer Engagement tier includes.') }}
                </p>
            </div>
            <div style="display: flex; gap: 8px; flex-shrink: 0">
                <button type="button" @click="expandAll()" class="kv-btn-action kv-btn-table-control" style="min-height: 44px; padding: 10px 16px; border-radius: 10px; border: 1px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; font-size: 14px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; cursor: pointer">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 9l5-5 5 5M7 15l5 5 5-5"></path></svg>
                    {{ t('kouventa.Expand all', 'Expand all') }}
                </button>
                <button type="button" @click="collapseAll()" class="kv-btn-action kv-btn-table-control" style="min-height: 44px; padding: 10px 16px; border-radius: 10px; border: 1px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; font-size: 14px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; cursor: pointer">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4l5 5 5-5M7 20l5-5 5 5"></path></svg>
                    {{ t('kouventa.Collapse all', 'Collapse all') }}
                </button>
            </div>
        </div>

        <div class="kv-compare-wrapper">
            <!-- Sticky Header -->
            <div class="kv-compare-header">
                <span style="padding: 16px 24px">{{ t('kouventa.Feature', 'Feature') }}</span>
                <span style="padding: 16px 24px" x-text="product === 'ai' ? 'Starter' : 'Starter'">Starter</span>
                <span style="padding: 16px 24px" x-text="product === 'ai' ? 'Grow' : 'Professional'">Professional</span>
                <span style="padding: 16px 24px" x-text="product === 'ai' ? 'Scale' : 'Enterprise'">Enterprise</span>
            </div>

            <!-- CE Categories -->
            <div x-show="product === 'ce'">
                @foreach ($ceCategories as $cIndex => $category)
                    @php $catKey = 'ce_' . $cIndex; @endphp
                    <div style="border-bottom: 1px solid #E3E8E6">
                        <button type="button" @click="toggleCat('{{ $catKey }}')" class="kv-table-cat-btn" style="width: 100%; min-height: 48px; border: 0; background: #F4F6F5; padding: 12px 24px; display: flex; justify-content: space-between; align-items: center; font-size: 14px; font-weight: 800; color: #0E1A24; text-align: left; cursor: pointer">
                            <span>{{ t('kouventa.' . $category[0], $category[0]) }}</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" :style="{ transform: isCatOpen('{{ $catKey }}') ? 'rotate(180deg)' : 'rotate(0deg)', transition: 'transform 0.2s ease' }">
                                <path d="m6 9 6 6 6-6"></path>
                            </svg>
                        </button>
                        <div x-show="isCatOpen('{{ $catKey }}')" style="display: flex; flex-direction: column">
                            @foreach ($category[1] as $row)
                                <div class="kv-compare-row">
                                    <span style="padding: 13px 24px; font-weight: 600; color: #0E1A24">
                                        {{ t('kouventa.' . $row[0], $row[0]) }}
                                    </span>
                                    @for ($col = 1; $col <= 3; $col++)
                                        @php $val = $row[$col] ?? '—'; @endphp
                                        <span style="padding: 13px 24px; display: flex; align-items: center; color: #2B3947">
                                            @if ($val === '✓')
                                                <span aria-label="Included" style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                                                </span>
                                            @elseif ($val === '—')
                                                <span aria-label="Not included" style="color: #5B6875">—</span>
                                            @elseif ($val === 'Add-on')
                                                <span style="font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 999px; background: #F4F6F5; color: #4A5866">{{ t('kouventa.Add-on', 'Add-on') }}</span>
                                            @else
                                                <span>{{ t('kouventa.' . $val, $val) }}</span>
                                            @endif
                                        </span>
                                    @endfor
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- AI Categories -->
            <div x-show="product === 'ai'">
                @foreach ($aiCategories as $cIndex => $category)
                    @php $catKey = 'ai_' . $cIndex; @endphp
                    <div style="border-bottom: 1px solid #E3E8E6">
                        <button type="button" @click="toggleCat('{{ $catKey }}')" class="kv-table-cat-btn" style="width: 100%; min-height: 48px; border: 0; background: #F4F6F5; padding: 12px 24px; display: flex; justify-content: space-between; align-items: center; font-size: 14px; font-weight: 800; color: #0E1A24; text-align: left; cursor: pointer">
                            <span>{{ t('kouventa.' . $category[0], $category[0]) }}</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" :style="{ transform: isCatOpen('{{ $catKey }}') ? 'rotate(180deg)' : 'rotate(0deg)', transition: 'transform 0.2s ease' }">
                                <path d="m6 9 6 6 6-6"></path>
                            </svg>
                        </button>
                        <div x-show="isCatOpen('{{ $catKey }}')" style="display: flex; flex-direction: column">
                            @foreach ($category[1] as $row)
                                <div class="kv-compare-row">
                                    <span style="padding: 13px 24px; font-weight: 600; color: #0E1A24">
                                        {{ t('kouventa.' . $row[0], $row[0]) }}
                                    </span>
                                    @for ($col = 1; $col <= 3; $col++)
                                        @php $val = $row[$col] ?? '—'; @endphp
                                        <span style="padding: 13px 24px; display: flex; align-items: center; color: #2B3947">
                                            @if ($val === '✓')
                                                <span aria-label="Included" style="width: 22px; height: 22px; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                                                </span>
                                            @elseif ($val === '—')
                                                <span aria-label="Not included" style="color: #5B6875">—</span>
                                            @elseif ($val === 'Add-on')
                                                <span style="font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 999px; background: #F4F6F5; color: #4A5866">{{ t('kouventa.Add-on', 'Add-on') }}</span>
                                            @else
                                                <span>{{ t('kouventa.' . $val, $val) }}</span>
                                            @endif
                                        </span>
                                    @endfor
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ============ EXTEND YOUR PLAN (ADD-ONS) ============ -->
    <div style="display: flex; flex-direction: column; gap: 32px">
        <div style="display: flex; flex-direction: column; gap: 10px">
            <h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em; color: #0E1A24">
                {{ t('kouventa.Extend your plan', 'Extend your plan') }}
            </h2>
            <p style="margin: 0; font-size: 16px; color: #4A5866"
               x-text="product === 'ai' ? '{{ t('kouventa.Add capabilities as your AI adoption grows.', 'Add capabilities as your AI adoption grows.') }}' : '{{ t('kouventa.Add capabilities as you need them, on any tier.', 'Add capabilities as you need them, on any tier.') }}'">
                {{ t('kouventa.Add capabilities as you need them, on any tier.', 'Add capabilities as you need them, on any tier.') }}
            </p>
        </div>

        <!-- CE Addons -->
        <div x-show="product === 'ce'" class="kv-addon-grid">
            @foreach ($ceAddons as $addon)
                <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 16px; padding: 22px; display: flex; flex-direction: column; gap: 8px">
                    <span style="font-size: 16px; font-weight: 800; color: #0E1A24">{{ t('kouventa.' . $addon[0], $addon[0]) }}</span>
                    <span style="font-size: 14px; line-height: 1.55; color: #4A5866">{{ t('kouventa.' . $addon[1], $addon[1]) }}</span>
                </div>
            @endforeach
        </div>

        <!-- AI Addons -->
        <div x-show="product === 'ai'" class="kv-addon-grid">
            @foreach ($aiAddons as $addon)
                <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 16px; padding: 22px; display: flex; flex-direction: column; gap: 8px">
                    <span style="font-size: 16px; font-weight: 800; color: #0E1A24">{{ t('kouventa.' . $addon[0], $addon[0]) }}</span>
                    <span style="font-size: 14px; line-height: 1.55; color: #4A5866">{{ t('kouventa.' . $addon[1], $addon[1]) }}</span>
                </div>
            @endforeach
        </div>

        <p style="margin: 0; font-size: 16px; color: #4A5866">
            {{ t('kouventa.Available across all plans.', 'Available across all plans.') }}
            <a href="#whatsapp" @click.prevent="$dispatch('open-whatsapp-modal')" data-open-whatsapp style="font-weight: 700; color: #0E1A24; text-decoration: underline; cursor: pointer">{{ t('kouventa.Talk to sales', 'Talk to sales') }}</a>
            {{ t('kouventa.for scoping and pricing.', 'for scoping and pricing.') }}
        </p>
    </div>

    <!-- ============ WHATSAPP MESSAGE TEMPLATES (CE ONLY) ============ -->
    <div x-show="product === 'ce'" style="display: flex; flex-direction: column; gap: 32px">
        <div style="display: flex; flex-direction: column; gap: 10px">
            <h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em; color: #0E1A24">
                {{ t('kouventa.WhatsApp message templates', 'WhatsApp message templates') }}
            </h2>
            <p style="margin: 0; font-size: 16px; color: #4A5866">
                {{ t('kouventa.WhatsApp charges per conversation, based on the template category you send.', 'WhatsApp charges per conversation, based on the template category you send.') }}
            </p>
        </div>

        <div class="kv-template-grid">
            @foreach ($templates as $tp)
                <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 20px; padding: 24px; display: flex; flex-direction: column; gap: 18px">
                    <div style="display: flex; align-items: center; gap: 14px; padding-bottom: 18px; border-bottom: 1px solid #E3E8E6">
                        <span style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: #071C33; color: #2BD46A; display: flex; align-items: center; justify-content: center">
                            @if ($tp['key'] === 'mk')
                                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11v2a1 1 0 0 0 1 1h2l6 4V6L6 10H4a1 1 0 0 0-1 1Z"></path><path d="M16 9a4 4 0 0 1 0 6M19 6a8 8 0 0 1 0 12"></path></svg>
                            @elseif ($tp['key'] === 'ut')
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0"></path></svg>
                            @elseif ($tp['key'] === 'au')
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V7a4 4 0 0 1 8 0v4"></path></svg>
                            @elseif ($tp['key'] === 'sv')
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14v-2a8 8 0 0 1 16 0v2"></path><rect x="2" y="14" width="5" height="6" rx="2"></rect><rect x="17" y="14" width="5" height="6" rx="2"></rect></svg>
                            @endif
                        </span>
                        <span style="font-size: 20px; font-weight: 800; color: #0E1A24">{{ t('kouventa.' . $tp['name'], $tp['name']) }}</span>
                    </div>
                    <ul style="margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 12px">
                        @foreach ($tp['items'] as $item)
                            <li style="display: flex; gap: 10px; align-items: flex-start; font-size: 14px; line-height: 1.5; color: #2B3947">
                                <span style="width: 18px; height: 18px; flex-shrink: 0; border-radius: 50%; background: #DDF3E4; display: flex; align-items: center; justify-content: center; margin-top: 2px">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg>
                                </span>
                                <span>{{ t('kouventa.' . $item, $item) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ============ WHATSAPP CALCULATOR (CE ONLY) ============ -->
    <div x-show="product === 'ce'" style="display: flex; flex-direction: column; gap: 32px">
        <div style="display: flex; flex-direction: column; gap: 10px">
            <h2 style="margin: 0; font-size: 44px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em; color: #0E1A24">{{ $calcTitle }}</h2>
            <p style="margin: 0; font-size: 16px; color: #4A5866">{{ $calcSubtitle }}</p>
        </div>

        <div style="background: #FFFFFF; border: 1px solid #E3E8E6; border-radius: 24px; padding: 40px; display: grid; grid-template-columns: minmax(0, 1.25fr) minmax(0, 1fr); gap: 40px; align-items: start">
            <div style="display: flex; flex-direction: column; gap: 20px">
                <div style="display: grid; grid-template-columns: minmax(0, 1fr) 24px 120px 24px minmax(0, 1fr); gap: 8px; font-size: 12px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: #6B7A88">
                    <span>{{ t('kouventa.Conversations / month', 'Conversations / month') }}</span><span></span><span style="text-align: center">{{ t('kouventa.Rate (IDR)', 'Rate (IDR') }}</span><span></span><span style="text-align: right">{{ t('kouventa.Subtotal (IDR)', 'Subtotal (IDR') }}</span>
                </div>

                <!-- Marketing -->
                <div style="display: flex; flex-direction: column; gap: 8px">
                    <span style="display: flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 700; color: #0E1A24">{{ t('kouventa.Marketing', 'Marketing') }}<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5M12 8h.01"></path></svg></span>
                    <div style="display: grid; grid-template-columns: minmax(0, 1fr) 24px 120px 24px minmax(0, 1fr); gap: 8px; align-items: center">
                        <input type="number" min="0" inputmode="numeric" aria-label="Marketing conversations per month" x-model.number="mk" style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; padding: 0 12px">
                        <span style="text-align: center; color: #6B7A88; font-weight: 700">×</span>
                        <span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #6B7A88; display: flex; align-items: center; justify-content: center">616</span>
                        <span style="text-align: center; color: #6B7A88; font-weight: 700">=</span>
                        <span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #0E1A24; display: flex; align-items: center; justify-content: flex-end; padding: 0 16px" x-text="fmtNum(subMk)">0</span>
                    </div>
                </div>

                <!-- Utility -->
                <div style="display: flex; flex-direction: column; gap: 8px">
                    <span style="display: flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 700; color: #0E1A24">{{ t('kouventa.Utility', 'Utility') }}<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5M12 8h.01"></path></svg></span>
                    <div style="display: grid; grid-template-columns: minmax(0, 1fr) 24px 120px 24px minmax(0, 1fr); gap: 8px; align-items: center">
                        <input type="number" min="0" inputmode="numeric" aria-label="Utility conversations per month" x-model.number="ut" style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; padding: 0 12px">
                        <span style="text-align: center; color: #6B7A88; font-weight: 700">×</span>
                        <span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #6B7A88; display: flex; align-items: center; justify-content: center">375</span>
                        <span style="text-align: center; color: #6B7A88; font-weight: 700">=</span>
                        <span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #0E1A24; display: flex; align-items: center; justify-content: flex-end; padding: 0 16px" x-text="fmtNum(subUt)">0</span>
                    </div>
                </div>

                <!-- Authentication -->
                <div style="display: flex; flex-direction: column; gap: 8px">
                    <span style="display: flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 700; color: #0E1A24">{{ t('kouventa.Authentication', 'Authentication') }}<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5M12 8h.01"></path></svg></span>
                    <div style="display: grid; grid-template-columns: minmax(0, 1fr) 24px 120px 24px minmax(0, 1fr); gap: 8px; align-items: center">
                        <input type="number" min="0" inputmode="numeric" aria-label="Authentication conversations per month" x-model.number="au" style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; padding: 0 12px">
                        <span style="text-align: center; color: #6B7A88; font-weight: 700">×</span>
                        <span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #6B7A88; display: flex; align-items: center; justify-content: center">375</span>
                        <span style="text-align: center; color: #6B7A88; font-weight: 700">=</span>
                        <span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #0E1A24; display: flex; align-items: center; justify-content: flex-end; padding: 0 16px" x-text="fmtNum(subAu)">0</span>
                    </div>
                </div>

                <!-- Service -->
                <div style="display: flex; flex-direction: column; gap: 8px">
                    <span style="display: flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 700; color: #0E1A24">{{ t('kouventa.Service', 'Service') }}<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5M12 8h.01"></path></svg></span>
                    <div style="display: grid; grid-template-columns: minmax(0, 1fr) 24px 120px 24px minmax(0, 1fr); gap: 8px; align-items: center">
                        <input type="number" min="0" inputmode="numeric" aria-label="Service conversations per month" x-model.number="sv" style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; padding: 0 12px">
                        <span style="text-align: center; color: #6B7A88; font-weight: 700">×</span>
                        <span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #6B7A88; display: flex; align-items: center; justify-content: center">375</span>
                        <span style="text-align: center; color: #6B7A88; font-weight: 700">=</span>
                        <span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #0E1A24; display: flex; align-items: center; justify-content: flex-end; padding: 0 16px" x-text="fmtNum(subSv)">0</span>
                    </div>
                </div>

                <!-- MAU row -->
                <div style="display: flex; flex-direction: column; gap: 8px; padding-top: 16px; border-top: 1px dashed #C9D2CE">
                    <span style="display: flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 700; color: #0E1A24">{{ t('kouventa.Monthly active users', 'Monthly active users') }}<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5M12 8h.01"></path></svg></span>
                    <div style="display: grid; grid-template-columns: minmax(0, 1fr) 24px minmax(0, 1fr) 24px minmax(0, 1fr); gap: 8px; align-items: end">
                        <label style="display: flex; flex-direction: column; gap: 6px; font-size: 12px; font-weight: 600; color: #6B7A88">{{ t('kouventa.Unique numbers broadcast to', 'Unique numbers broadcast to') }}<input type="number" min="0" inputmode="numeric" x-model.number="bc" style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; padding: 0 12px"></label>
                        <span style="height: 52px; display: flex; align-items: center; justify-content: center; color: #6B7A88; font-weight: 700">+</span>
                        <label style="display: flex; flex-direction: column; gap: 6px; font-size: 12px; font-weight: 600; color: #6B7A88">{{ t('kouventa.Unique inbound numbers', 'Unique inbound numbers') }}<input type="number" min="0" inputmode="numeric" x-model.number="ib" style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; border: 1.5px solid #C9D2CE; background: #FFFFFF; color: #0E1A24; padding: 0 12px"></label>
                        <span style="height: 52px; display: flex; align-items: center; justify-content: center; color: #6B7A88; font-weight: 700">=</span>
                        <span style="height: 52px; box-sizing: border-box; border-radius: 12px; font-size: 16px; font-weight: 700; text-align: center; font-family: inherit; background: #F4F6F5; color: #0E1A24; display: flex; align-items: center; justify-content: flex-end; padding: 0 16px" x-text="fmtNum(mau)">0</span>
                    </div>
                    <span style="font-size: 13px; color: #6B7A88">{{ t('kouventa.Up to 3,000 users: Starter. 3,001 to 15,000: Professional. Above 15,000: custom plan.', 'Up to 3,000 users: Starter. 3,001 to 15,000: Professional. Above 15,000: custom plan.') }}</span>
                </div>
                <span style="font-size: 13px; color: #6B7A88">{{ t("kouventa.Rates are per conversation and follow Meta's WhatsApp pricing, which may change.", "Rates are per conversation and follow Meta's WhatsApp pricing, which may change.") }}</span>
            </div>

            <!-- Estimate Summary Card -->
            <div style="background: #071C33; color: #FFFFFF; border-radius: 20px; padding: 32px; display: flex; flex-direction: column; gap: 18px">
                <span style="font-size: 13px; font-weight: 800; letter-spacing: 0.08em; color: #2BD46A">{{ t('kouventa.YOUR ESTIMATE', 'YOUR ESTIMATE') }}</span>
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; padding-bottom: 14px; border-bottom: 1px solid rgba(255,255,255,0.14)">
                    <span style="font-size: 15px; color: #B9C6D3">{{ t('kouventa.Your plan', 'Your plan') }}</span>
                    <span style="font-size: 13px; font-weight: 800; padding: 6px 12px; border-radius: 999px; background: rgba(43,212,106,0.16); color: #2BD46A" x-text="plan">Starter</span>
                </div>

                <!-- In range estimate -->
                <template x-if="!isOver">
                    <div style="display: flex; flex-direction: column; gap: 18px">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 12px; padding-bottom: 14px; border-bottom: 1px solid rgba(255,255,255,0.14)"><span style="font-size: 15px; color: #B9C6D3">{{ t('kouventa.Platform fee / month', 'Platform fee / month') }}</span><span style="font-size: 18px; font-weight: 800" x-text="fmt(platformFee)">Rp 1.499.000</span></div>
                        <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 12px; padding-bottom: 14px; border-bottom: 1px solid rgba(255,255,255,0.14)"><span style="font-size: 15px; color: #B9C6D3">{{ t('kouventa.Conversation fee / month', 'Conversation fee / month') }}</span><span style="font-size: 18px; font-weight: 800" x-text="fmt(convTotal)">Rp 0</span></div>
                        <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 12px; padding-bottom: 14px; border-bottom: 1px solid rgba(255,255,255,0.14)"><span style="font-size: 15px; color: #B9C6D3">{{ t('kouventa.Platform + conversation / month', 'Platform + conversation / month') }}</span><span style="font-size: 18px; font-weight: 800" x-text="fmt(monthlyTotal)">Rp 1.499.000</span></div>
                        <div style="display: flex; flex-direction: column; gap: 4px; padding-top: 4px"><span style="font-size: 15px; color: #B9C6D3">{{ t('kouventa.Grand total / year', 'Grand total / year') }}</span><span style="font-size: 40px; line-height: 1.1; font-weight: 800; letter-spacing: -0.02em; color: #2BD46A" x-text="fmt(yearlyTotal)">Rp 17.988.000</span></div>
                        <a href="{{ localized_url('/book-demo') }}" class="kv-btn-action kv-btn-primary" style="margin-top: 6px; width: 100%; box-sizing: border-box; background: #2BD46A; color: #06251A; font-size: 16px; font-weight: 700; padding: 16px 24px; border-radius: 12px; text-align: center; text-decoration: none">{{ t('kouventa.Book a demo', 'Book a demo') }}</a>
                    </div>
                </template>

                <!-- Over 15,000 active users custom enterprise CTA -->
                <template x-if="isOver">
                    <div style="display: flex; flex-direction: column; gap: 18px">
                        <div style="display: flex; flex-direction: column; gap: 10px; padding: 8px 0 4px">
                            <span style="font-size: 26px; line-height: 1.25; font-weight: 800">{{ t('kouventa.More than 15,000 active users?', 'More than 15,000 active users?') }}</span>
                            <span style="font-size: 15px; line-height: 1.6; color: #B9C6D3">{{ t("kouventa.You're ready for an enterprise setup. Talk to our team for a plan tailored to your volume, channels and integrations.", "You're ready for an enterprise setup. Talk to our team for a plan tailored to your volume, channels and integrations.") }}</span>
                        </div>
                        <a href="#whatsapp" @click.prevent="$dispatch('open-whatsapp-modal')" data-open-whatsapp class="kv-btn-action kv-btn-primary" style="margin-top: 6px; width: 100%; box-sizing: border-box; background: #2BD46A; color: #06251A; font-size: 16px; font-weight: 700; padding: 16px 24px; border-radius: 12px; text-align: center; text-decoration: none; cursor: pointer">{{ t('kouventa.Talk to sales', 'Talk to sales') }}</a>
                    </div>
                </template>
            </div>
        </div>
    </div>
</section>

<!-- ============ CLOSING CTA ============ -->
<section style="padding: 0 80px 104px; background: #F4F6F5">
    <div style="background: #2BD46A; border-radius: 28px; padding: 56px 64px; display: flex; justify-content: space-between; align-items: center; gap: 48px; color: #06251A">
        <div style="display: flex; flex-direction: column; gap: 12px; max-width: 720px">
            <h2 style="margin: 0; font-size: 40px; line-height: 1.12; font-weight: 800; letter-spacing: -0.02em">{{ $ctaTitle }}</h2>
            <p style="margin: 0; font-size: 18px; line-height: 1.55">{!! nl2br(e($ctaSubtitle)) !!}</p>
        </div>
        <div style="display: flex; gap: 12px; flex-shrink: 0">
            <a href="{{ localized_url('/book-demo') }}" class="kv-btn-action kv-btn-navy" style="background: #071C33; color: #FFFFFF; font-size: 17px; font-weight: 700; padding: 18px 28px; border-radius: 12px; text-decoration: none">{{ t('kouventa.Book a working session', 'Book a working session') }}</a>
            <a href="#whatsapp" @click.prevent="$dispatch('open-whatsapp-modal')" data-open-whatsapp class="kv-btn-action kv-btn-outline" style="color: #06251A; font-size: 17px; font-weight: 700; padding: 17px 26px; border-radius: 12px; border: 1.5px solid #06251A; text-decoration: none; cursor: pointer">{{ t('kouventa.Contact sales', 'Contact sales') }}</a>
        </div>
    </div>
</section>

</div>

@push('scripts')
<script>
function whatsappCalculator() {
    return {
        mk: 0, ut: 0, au: 0, sv: 0, bc: 0, ib: 0,
        get mau() { return (Number(this.bc) || 0) + (Number(this.ib) || 0); },
        get isOver() { return this.mau > 15000; },
        get plan() { return this.isOver ? 'Enterprise' : (this.mau > 3000 ? 'Professional' : 'Starter'); },
        get platformFee() { return this.isOver ? 0 : (this.mau > 3000 ? 4499000 : 1499000); },
        get subMk() { return (Number(this.mk) || 0) * 616; },
        get subUt() { return (Number(this.ut) || 0) * 375; },
        get subAu() { return (Number(this.au) || 0) * 375; },
        get subSv() { return (Number(this.sv) || 0) * 375; },
        get convTotal() { return this.subMk + this.subUt + this.subAu + this.subSv; },
        get monthlyTotal() { return this.platformFee + this.convTotal; },
        get yearlyTotal() { return this.monthlyTotal * 12; },
        fmt(num) { return 'Rp ' + Math.round(num).toLocaleString('id-ID'); },
        fmtNum(num) { return Math.round(num).toLocaleString('id-ID'); }
    };
}
</script>
@endpush
@endsection
