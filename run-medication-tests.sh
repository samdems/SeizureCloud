#!/bin/bash

# Medication Test Runner Script
# This script runs all medication-related tests for the diary application

echo "🧪 Running Medication Tests"
echo "=========================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to run a test and check result
run_test() {
    local test_file=$1
    local test_name=$2

    echo -e "\n${BLUE}📋 Running: ${test_name}${NC}"
    echo "----------------------------------------"

    if php artisan test --filter="$test_file" --colors; then
        echo -e "${GREEN}✅ $test_name passed${NC}"
        return 0
    else
        echo -e "${RED}❌ $test_name failed${NC}"
        return 1
    fi
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: Please run this script from the Laravel root directory${NC}"
    exit 1
fi

# Initialize counters
TOTAL_TESTS=0
PASSED_TESTS=0
FAILED_TESTS=0

echo -e "${YELLOW}🔧 Setting up test environment...${NC}"

# Refresh test database
php artisan migrate:fresh --env=testing --seed

echo -e "\n${YELLOW}🧪 Running medication test suites...${NC}"

# Test suites to run
declare -A test_suites=(
    ["MedicationTest"]="Core Medication Functionality"
    ["MedicationTimingTest"]="Medication Timing & Intended Time Features"
    ["AsNeededMedicationTest"]="As-Needed Medication Improvements"
)

# Run each test suite
for test_file in "${!test_suites[@]}"; do
    TOTAL_TESTS=$((TOTAL_TESTS + 1))

    if run_test "$test_file" "${test_suites[$test_file]}"; then
        PASSED_TESTS=$((PASSED_TESTS + 1))
    else
        FAILED_TESTS=$((FAILED_TESTS + 1))
    fi
done

# Summary
echo ""
echo "=========================================="
echo "🎯 TEST SUMMARY"
echo "=========================================="
echo -e "Total test suites: ${BLUE}$TOTAL_TESTS${NC}"
echo -e "Passed: ${GREEN}$PASSED_TESTS${NC}"
echo -e "Failed: ${RED}$FAILED_TESTS${NC}"

if [ $FAILED_TESTS -eq 0 ]; then
    echo -e "\n${GREEN}🎉 All medication tests passed successfully!${NC}"
    echo -e "${GREEN}✅ Your medication tracking improvements are working correctly${NC}"
    exit 0
else
    echo -e "\n${RED}⚠️  Some tests failed. Please review the output above.${NC}"
    exit 1
fi

# Optional: Run specific test methods if provided as arguments
if [ $# -gt 0 ]; then
    echo -e "\n${YELLOW}🎯 Running specific tests...${NC}"
    for test_method in "$@"; do
        echo -e "\n${BLUE}Running: $test_method${NC}"
        php artisan test --filter="$test_method" --colors
    done
fi
