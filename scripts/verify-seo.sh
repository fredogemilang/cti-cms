#!/usr/bin/env bash
# verify-seo.sh — Verifikasi Metadata SEO & Paritas Produksi
# Penggunaan: bash scripts/verify-seo.sh [BASE_URL]
# Contoh: bash scripts/verify-seo.sh https://cdt.devs

BASE="${1:-https://cdt.devs}"
echo "========================================================"
echo "Verifikasi SEO & Paritas Metadata untuk: $BASE"
echo "========================================================"

FAILED=0

echo ""
echo "--- 1. Testing SEO Tag Single-Source-of-Truth (Zero Duplicates) ---"
HTML_ABOUT=$(curl -k -sL "$BASE/about-us/")
DESC_COUNT=$(printf '%s' "$HTML_ABOUT" | grep -c 'name="description"')
CANON_COUNT=$(printf '%s' "$HTML_ABOUT" | grep -c 'rel="canonical"')
OG_TITLE_COUNT=$(printf '%s' "$HTML_ABOUT" | grep -c 'property="og:title"')

if [ "$DESC_COUNT" -eq 1 ]; then
    echo " [OK] Meta description rendered exactly once (count: 1)"
else
    echo " [FAIL] Meta description duplicate count: $DESC_COUNT (expected 1)"
    FAILED=$((FAILED + 1))
fi

if [ "$CANON_COUNT" -eq 1 ]; then
    echo " [OK] Canonical tag rendered exactly once (count: 1)"
else
    echo " [FAIL] Canonical tag duplicate count: $CANON_COUNT (expected 1)"
    FAILED=$((FAILED + 1))
fi

if [ "$OG_TITLE_COUNT" -eq 1 ]; then
    echo " [OK] OG Title rendered exactly once (count: 1)"
else
    echo " [FAIL] OG Title duplicate count: $OG_TITLE_COUNT (expected 1)"
    FAILED=$((FAILED + 1))
fi

echo ""
echo "--- 2. Verifying Homepage Title Parity ---"
HOME_TITLE=$(curl -k -sL "$BASE/" | grep -oP '(?<=<title>).*?(?=</title>)' | head -1)
EXPECTED_TITLE="Trusted IT Consultant for Scalable and Secure Growth - Central Data Technology"
if [ "$HOME_TITLE" = "$EXPECTED_TITLE" ]; then
    echo " [OK] Homepage title matches production: $HOME_TITLE"
else
    echo " [FAIL] Homepage title: '$HOME_TITLE' (expected: '$EXPECTED_TITLE')"
    FAILED=$((FAILED + 1))
fi

echo ""
echo "--- 3. Verifying Commercial Meta Descriptions on Key Pages ---"
check_desc() {
    local path="$1"
    local expected_substr="$2"
    local html
    html=$(curl -k -sL "$BASE$path")
    local desc
    desc=$(printf '%s' "$html" | grep -oP '(?<=<meta name="description" content=")[^"]*' | head -1)
    if [[ "$desc" == *"$expected_substr"* ]]; then
        echo " [OK] Description on $path: '$desc'"
    else
        echo " [FAIL] Description on $path: '$desc' (expected to contain: '$expected_substr')"
        FAILED=$((FAILED + 1))
    fi
}

check_desc "/" "Drive business growth faster"
check_desc "/akamai/" "Enhance performance and security"
check_desc "/zscaler/" "Delivery Services Authorized"
check_desc "/hitachi-vantara/" "authorized distributor"
check_desc "/amazon-web-services/" "Advanced AWS Partner"
check_desc "/knowledgetedy/" "Comprehensive enterprise IT solutions catalog"

echo ""
echo "--- 4. Checking for Staging Leaks (ctizen.id) ---"
LEAK_COUNT=$(curl -k -sL "$BASE/" | grep -c "ctizen\.id")
if [ "$LEAK_COUNT" -eq 0 ]; then
    echo " [OK] Zero leaks of ctizen.id in frontend responses"
else
    echo " [FAIL] Found $LEAK_COUNT occurrences of ctizen.id in homepage!"
    FAILED=$((FAILED + 1))
fi

echo ""
echo "========================================================"
if [ $FAILED -eq 0 ]; then
    echo " RESULT: ALL SEO PARITY TESTS PASSED! (0 failures)"
    exit 0
else
    echo " RESULT: $FAILED TEST(S) FAILED!"
    exit 1
fi
