#!/usr/bin/env bash
# verify-trailing-slash.sh — Verifikasi Trailing Slash & Deteksi Loop
# Penggunaan: bash scripts/verify-trailing-slash.sh [BASE_URL]
# Contoh: bash scripts/verify-trailing-slash.sh https://cdt.devs

BASE="${1:-https://cdt.devs}"
echo "========================================================"
echo "Verifikasi Trailing Slash & Web Server untuk: $BASE"
echo "========================================================"

FAILED=0

check_status() {
    local url="$1"
    local expected="$2"
    local desc="$3"
    local code
    code=$(curl -k -s -o /dev/null -w "%{http_code}" "$url")
    if [ "$code" = "$expected" ]; then
        echo " [OK] $desc: $url -> $code (expected $expected)"
    else
        echo " [FAIL] $desc: $url -> $code (expected $expected)"
        FAILED=$((FAILED + 1))
    fi
}

check_redirect_target() {
    local url="$1"
    local expected_substr="$2"
    local desc="$3"
    local loc
    loc=$(curl -k -sI "$url" | grep -i "^location:" | head -1 | tr -d '\r\n')
    if [[ "$loc" == *"$expected_substr"* ]]; then
        echo " [OK] $desc redirect target matches: $loc"
    else
        echo " [FAIL] $desc redirect target: $loc (expected substring: $expected_substr)"
        FAILED=$((FAILED + 1))
    fi
}

check_no_loop() {
    local url="$1"
    local desc="$2"
    curl -k -s -o /dev/null --max-redirs 5 -L "$url"
    local ec=$?
    if [ $ec -eq 47 ]; then
        echo " [FAIL] REDIRECT LOOP DETECTED on $url (curl exit code 47)"
        FAILED=$((FAILED + 1))
    elif [ $ec -eq 0 ]; then
        echo " [OK] No loop on $url (resolved cleanly)"
    else
        echo " [WARN] $url exited with code $ec"
    fi
}

echo ""
echo "--- 1. Testing Trailing Slash Enforcement (Non-slash -> Slash 301) ---"
check_status "$BASE/about-us" "301" "About Us without slash"
check_redirect_target "$BASE/about-us" "/about-us/" "About Us redirect"
check_no_loop "$BASE/about-us" "About Us without slash"

check_status "$BASE/id/tentang-kami" "301" "ID Tentang Kami without slash"
check_redirect_target "$BASE/id/tentang-kami" "/id/tentang-kami/" "ID Tentang Kami redirect"
check_no_loop "$BASE/id/tentang-kami" "ID Tentang Kami without slash"

check_status "$BASE/akamai" "301" "Akamai without slash"
check_redirect_target "$BASE/akamai" "/akamai/" "Akamai redirect"

echo ""
echo "--- 2. Testing Trailing Slash URLs (Must return 200 OK) ---"
check_status "$BASE/" "200" "Homepage EN"
check_status "$BASE/id/" "200" "Homepage ID"
check_status "$BASE/about-us/" "200" "About Us EN"
check_status "$BASE/id/tentang-kami/" "200" "About Us ID"
check_status "$BASE/akamai/" "200" "Akamai Root Alliance"
check_status "$BASE/id/akamai/" "200" "ID Akamai Root Alliance"
check_status "$BASE/knowledgetedy/" "200" "KnowledgeTedy AI Catalog EN"
check_status "$BASE/id/knowledgetedy/" "200" "KnowledgeTedy AI Catalog ID"
check_status "$BASE/thank-you-submission/" "200" "Thank You Submission"
check_status "$BASE/thank-you-for-subscribing/" "200" "Thank You Subscribing"
check_status "$BASE/id/terima-kasih/" "200" "ID Terima Kasih"
check_status "$BASE/whatsapp-privacy-policy/" "200" "WhatsApp Privacy Policy"

echo ""
echo "--- 3. Testing System Endpoints (Must NOT force trailing slash) ---"
check_status "$BASE/robots.txt" "200" "robots.txt"
check_status "$BASE/sitemap.xml" "200" "sitemap.xml index"

echo ""
echo "--- 4. Testing 301 Legacy Redirects ---"
check_status "$BASE/careers/cloud-engineer" "301" "Career cloud-engineer redirect"
check_redirect_target "$BASE/careers/cloud-engineer" "/careers/" "Career to modal listing"

check_status "$BASE/netgain-security-analytics-siem" "301" "Legacy NetGain flat URL"
check_redirect_target "$BASE/netgain-security-analytics-siem" "/netgain-systems/netgain-security-analytics-siem/" "NetGain redirect"

echo ""
echo "--- 5. Checking Canonical Tag Trailing Slashes ---"
CANONICAL=$(curl -k -sL "$BASE/about-us/" | grep -i '<link rel="canonical"' | head -1)
if [[ "$CANONICAL" == *"/about-us/\""* ]]; then
    echo " [OK] Canonical has trailing slash: $CANONICAL"
else
    echo " [FAIL] Canonical missing trailing slash: $CANONICAL"
    FAILED=$((FAILED + 1))
fi

echo ""
echo "========================================================"
if [ $FAILED -eq 0 ]; then
    echo " RESULT: ALL TESTS PASSED! (0 failures)"
    exit 0
else
    echo " RESULT: $FAILED TEST(S) FAILED!"
    exit 1
fi
