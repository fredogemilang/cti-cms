<?php

use App\Mcp\Servers\CmsServer;
use Laravel\Mcp\Facades\Mcp;

/*
|--------------------------------------------------------------------------
| AI / MCP Routes
|--------------------------------------------------------------------------
|
| MCP (Model Context Protocol) server endpoints. These routes expose CMS
| functionality to AI clients (Cursor, Windsurf, Antigravity, etc.) via
| the standardized MCP protocol.
|
| Authentication: Bearer token with `mcp.connect` ability required.
| Rate limiting: Enforced per-token via ApiToken tiers (60/120/300 req/min).
|
*/

Mcp::web('/mcp/cms', CmsServer::class)
    ->middleware(['api.auth:mcp.connect']);
