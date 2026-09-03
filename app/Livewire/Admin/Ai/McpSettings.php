<?php

namespace App\Livewire\Admin\Ai;

use App\Models\Activity;
use App\Models\ApiToken;
use Livewire\Component;

class McpSettings extends Component
{
    // Token creation form
    public string $tokenName = '';

    public string $tokenTier = 'editor';

    public string $allowedIps = '';

    public int $rateLimit = 120;

    public ?string $newPlaintextToken = null;

    // MCP status
    public bool $mcpEnabled = true;

    // Tabs
    public string $activeTab = 'tokens';

    public function getTiersProperty(): array
    {
        return ApiToken::mcpTiers();
    }

    public function updatedTokenTier(string $value): void
    {
        $tiers = ApiToken::mcpTiers();
        if (isset($tiers[$value])) {
            $this->rateLimit = $tiers[$value]['rate_limit'];
        }
    }

    public function createToken(): void
    {
        abort_unless(auth()->user()?->hasPermission('api-tokens.create'), 403);

        $tiers = ApiToken::mcpTiers();

        $this->validate([
            'tokenName' => ['required', 'string', 'max:100'],
            'tokenTier' => ['required', 'string', 'in:'.implode(',', array_keys($tiers))],
            'rateLimit' => ['required', 'integer', 'min:1', 'max:6000'],
        ]);

        $chosenTier = $this->tokenTier;
        $tier = $tiers[$chosenTier];
        $ips = array_filter(array_map('trim', explode(',', $this->allowedIps)));

        $result = ApiToken::generateFor(
            auth()->user(),
            "[MCP] {$this->tokenName}",
            $tier['abilities'],
            $ips,
            $this->rateLimit,
        );

        $this->newPlaintextToken = $result['plaintext'];
        $this->reset(['tokenName', 'allowedIps']);
        $this->tokenTier = 'editor';
        $this->rateLimit = 120;

        activity()->log('mcp-token.created', auth()->user(), "MCP token created: {$result['model']->name} (tier: {$chosenTier})");
    }

    public function revokeToken(int $id): void
    {
        abort_unless(auth()->user()?->hasPermission('api-tokens.revoke'), 403);

        $query = ApiToken::where('name', 'like', '[MCP]%');

        // Prevent IDOR: non-superadmins can only revoke their own tokens
        if (! auth()->user()->isSuperAdmin()) {
            $query->where('tokenable_type', auth()->user()->getMorphClass())
                ->where('tokenable_id', auth()->id());
        }

        $token = $query->findOrFail($id);
        $name = $token->name;
        $token->delete();

        activity()->log('mcp-token.revoked', auth()->user(), "MCP token revoked: {$name}");
        session()->flash('success', "Token \"{$name}\" revoked.");
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        // MCP tokens only (prefixed with [MCP])
        $tokens = ApiToken::where('name', 'like', '[MCP]%')
            ->latest()
            ->get();

        // High-performance SQL aggregates for 30-day activity stats
        $baseQuery = Activity::whereJsonContains('properties->source', 'mcp')
            ->where('created_at', '>=', now()->subDays(30));

        $totalOps = (clone $baseQuery)->count();
        $byAction = (clone $baseQuery)->groupBy('action')
            ->selectRaw('action, count(*) as total')
            ->pluck('total', 'action')
            ->toArray();

        $popularTools = (clone $baseQuery)->whereNotNull('description')
            ->groupBy('description')
            ->selectRaw('description, count(*) as total')
            ->orderByDesc('total')
            ->limit(10)
            ->pluck('total', 'description')
            ->toArray();

        $stats = [
            'total_tokens' => $tokens->count(),
            'active_tokens' => $tokens->filter(fn ($t) => $t->last_used_at && $t->last_used_at->isAfter(now()->subDays(7)))->count(),
            'total_operations_30d' => $totalOps,
            'operations_by_action' => $byAction,
            'popular_tools' => $popularTools,
        ];

        // Recent activity
        $recentActivity = Activity::whereJsonContains('properties->source', 'mcp')
            ->with('user')
            ->latest('created_at')
            ->take(20)
            ->get();

        return view('livewire.admin.ai.mcp-settings', [
            'tokens' => $tokens,
            'tiers' => ApiToken::mcpTiers(),
            'stats' => $stats,
            'recentActivity' => $recentActivity,
        ]);
    }
}
