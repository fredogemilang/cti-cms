<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<xsl:stylesheet version="2.0"
    xmlns:html="http://www.w3.org/TR/REC-html40"
    xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
    <xsl:template match="/">
        <html xmlns="http://www.w3.org/1999/xhtml" lang="en">
        <head>
            <title>XML Sitemap - CTI CMS Interactive AI Suite</title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <style type="text/css">
                :root {
                    --bg-color: #f8fafc;
                    --card-bg: #ffffff;
                    --text-main: #0f172a;
                    --text-muted: #64748b;
                    --border-color: #e2e8f0;
                    --primary-color: #2563eb;
                    --primary-light: #eff6ff;
                    --accent-color: #059669;
                    --table-hover: #f1f5f9;
                }

                @media (prefers-color-scheme: dark) {
                    :root {
                        --bg-color: #0b0f19;
                        --card-bg: #111827;
                        --text-main: #f9fafb;
                        --text-muted: #9ca3af;
                        --border-color: #1f2937;
                        --primary-color: #3b82f6;
                        --primary-light: #1e293b;
                        --accent-color: #10b981;
                        --table-hover: #1f2937;
                    }
                }

                * { box-sizing: border-box; margin: 0; padding: 0; }
                body {
                    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background-color: var(--bg-color);
                    color: var(--text-main);
                    padding: 2rem 1rem;
                    line-height: 1.5;
                }

                .container { max-width: 1100px; margin: 0 auto; }

                /* Top AI Resources Hub Navbar */
                .ai-navbar {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    flex-wrap: wrap;
                    background: var(--card-bg);
                    padding: 0.75rem 1.25rem;
                    border-radius: 1rem;
                    border: 1px solid var(--border-color);
                    margin-bottom: 1.5rem;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                }
                .ai-navbar-title { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-right: 0.5rem; }
                .ai-link {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.35rem;
                    font-size: 0.8rem;
                    font-weight: 600;
                    color: var(--primary-color);
                    text-decoration: none;
                    padding: 0.35rem 0.75rem;
                    border-radius: 0.5rem;
                    background: var(--primary-light);
                    transition: all 0.2s;
                }
                .ai-link:hover { opacity: 0.85; }

                /* Header Card */
                .header-card {
                    background: var(--card-bg);
                    border-radius: 1.25rem;
                    padding: 1.75rem;
                    border: 1px solid var(--border-color);
                    margin-bottom: 1.5rem;
                    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
                }
                .header-title-row { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem; }
                .main-title { font-size: 1.5rem; font-weight: 800; }
                .sub-title { font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem; }

                /* Metrics Grid */
                .metrics-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 1rem;
                    margin-top: 1.25rem;
                    padding-top: 1.25rem;
                    border-top: 1px solid var(--border-color);
                }
                .metric-box {
                    background: var(--bg-color);
                    padding: 1rem;
                    border-radius: 0.75rem;
                }
                .metric-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); }
                .metric-val { font-size: 1.1rem; font-weight: 800; margin-top: 0.2rem; color: var(--primary-color); }

                /* Health Badges */
                .health-badges { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.75rem; }
                .badge {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.25rem;
                    font-size: 0.7rem;
                    font-weight: 700;
                    padding: 0.25rem 0.6rem;
                    border-radius: 9999px;
                    background: #dcfce7;
                    color: #15803d;
                }

                /* Search Bar */
                .search-bar {
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                    margin-bottom: 1.5rem;
                }
                .search-input {
                    flex: 1;
                    height: 2.75rem;
                    border-radius: 0.75rem;
                    border: 1px solid var(--border-color);
                    background: var(--card-bg);
                    color: var(--text-main);
                    padding: 0 1rem;
                    font-size: 0.85rem;
                    outline: none;
                }
                .search-input:focus { border-color: var(--primary-color); }

                /* Table Styling */
                .table-container {
                    background: var(--card-bg);
                    border-radius: 1.25rem;
                    border: 1px solid var(--border-color);
                    overflow: hidden;
                    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
                }
                table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem; }
                th { background: var(--bg-color); padding: 0.85rem 1.25rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); border-bottom: 1px solid var(--border-color); }
                td { padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--border-color); }
                tr:hover td { background-color: var(--table-hover); }
                tr:last-child td { border-bottom: none; }

                .url-link { color: var(--primary-color); text-decoration: none; font-family: monospace; font-weight: 600; }
                .url-link:hover { text-decoration: underline; }

                .copy-btn {
                    background: transparent;
                    border: none;
                    color: var(--text-muted);
                    cursor: pointer;
                    font-size: 0.85rem;
                    padding: 0.2rem 0.4rem;
                    border-radius: 0.4rem;
                    transition: all 0.2s;
                }
                .copy-btn:hover { color: var(--primary-color); background: var(--primary-light); }
            </style>
            <script type="text/javascript">
                function searchSitemap() {
                    var input = document.getElementById('sitemap-search');
                    var filter = input.value.toLowerCase();
                    var tbody = document.getElementById('sitemap-tbody');
                    var rows = tbody.getElementsByTagName('tr');

                    for (var i = 0; i &lt; rows.length; i++) {
                        var text = rows[i].textContent || rows[i].innerText;
                        if (text.toLowerCase().indexOf(filter) &gt; -1) {
                            rows[i].style.display = "";
                        } else {
                            rows[i].style.display = "none";
                        }
                    }
                }

                function copyUrl(text) {
                    navigator.clipboard.writeText(text).then(function() {
                        alert('URL copied to clipboard!');
                    });
                }
            </script>
        </head>
        <body>
        <div class="container">
            {{-- AI & Open Web Resources Hub --}}
            <div class="ai-navbar">
                <span class="ai-navbar-title">AI &amp; Open Web Hub:</span>
                <a href="<?php echo url('/sitemap.xml'); ?>" class="ai-link">🗺️ XML Sitemap</a>
                <a href="<?php echo url('/schema-manifest.json'); ?>" class="ai-link">📂 Schema Manifest</a>
                <a href="<?php echo url('/schema.json'); ?>" class="ai-link">🧠 Schema Graph</a>
                <a href="<?php echo url('/llms.txt'); ?>" class="ai-link">🤖 LLMs.txt</a>
                <a href="<?php echo url('/robots.txt'); ?>" class="ai-link">🔒 Robots.txt</a>
            </div>

            {{-- Header Summary Card --}}
            <div class="header-card">
                <div class="header-title-row">
                    <div>
                        <h1 class="main-title">CTI CMS Interactive XML Sitemap</h1>
                        <p class="sub-title">Automated 3-Layer XML Sitemap with Real-Time XSLT Transformation &amp; AI Discovery Integration</p>
                    </div>
                </div>

                <div class="health-badges">
                    <span class="badge">✓ Valid XML 0.9</span>
                    <span class="badge">✓ UTF-8 Encoding</span>
                    <span class="badge">✓ HTTPS Protocol</span>
                    <span class="badge">✓ Atom Lastmod</span>
                </div>

                <div class="metrics-grid">
                    <div class="metric-box">
                        <div class="metric-label">Total Entries</div>
                        <div class="metric-val">
                            <xsl:if test="count(sitemap:sitemapindex/sitemap:sitemap) &gt; 0">
                                <xsl:value-of select="count(sitemap:sitemapindex/sitemap:sitemap)"/> Sub-Sitemaps
                            </xsl:if>
                            <xsl:if test="count(sitemap:sitemapindex/sitemap:sitemap) &lt; 1">
                                <xsl:value-of select="count(sitemap:urlset/sitemap:url)"/> URLs
                            </xsl:if>
                        </div>
                    </div>
                    <div class="metric-box">
                        <div class="metric-label">Engine Generator</div>
                        <div class="metric-val" style="font-size:0.95rem;">CTI CMS 2.0</div>
                    </div>
                </div>
            </div>

            {{-- Realtime Search Input --}}
            <div class="search-bar">
                <input type="text" id="sitemap-search" class="search-input" onkeyup="searchSitemap()" placeholder="Type to filter URLs or sub-sitemaps in real-time..." />
            </div>

            {{-- Table Content --}}
            <div class="table-container">
                {{-- SITEMAP INDEX TABLE --}}
                <xsl:if test="count(sitemap:sitemapindex/sitemap:sitemap) &gt; 0">
                    <table>
                        <thead>
                            <tr>
                                <th width="70%">Sub-Sitemap Location</th>
                                <th width="20%">Last Modified</th>
                                <th width="10%" style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sitemap-tbody">
                            <xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
                                <xsl:variable name="sitemapURL" select="sitemap:loc"/>
                                <tr>
                                    <td>
                                        <a href="{$sitemapURL}" class="url-link"><xsl:value-of select="sitemap:loc"/></a>
                                    </td>
                                    <td><xsl:value-of select="sitemap:lastmod"/></td>
                                    <td style="text-align:right;">
                                        <button class="copy-btn" onclick="copyUrl('{$sitemapURL}')" title="Copy URL">📋</button>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </tbody>
                    </table>
                </xsl:if>

                {{-- URLSET TABLE --}}
                <xsl:if test="count(sitemap:sitemapindex/sitemap:sitemap) &lt; 1">
                    <table>
                        <thead>
                            <tr>
                                <th width="60%">URL Location</th>
                                <th width="15%">Change Frequency</th>
                                <th width="15%">Last Modified</th>
                                <th width="10%" style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sitemap-tbody">
                            <xsl:for-each select="sitemap:urlset/sitemap:url">
                                <xsl:variable name="itemURL" select="sitemap:loc"/>
                                <tr>
                                    <td>
                                        <a href="{$itemURL}" class="url-link"><xsl:value-of select="sitemap:loc"/></a>
                                    </td>
                                    <td><xsl:value-of select="sitemap:changefreq"/></td>
                                    <td><xsl:value-of select="sitemap:lastmod"/></td>
                                    <td style="text-align:right;">
                                        <button class="copy-btn" onclick="copyUrl('{$itemURL}')" title="Copy URL">📋</button>
                                    </td>
                                </tr>
                            </xsl:for-each>
                        </tbody>
                    </table>
                </xsl:if>
            </div>
        </div>
        </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
