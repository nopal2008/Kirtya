#!/bin/bash

###############################################################################
# SECURITY CHECK SCRIPT
# Script ini untuk memverifikasi konfigurasi keamanan aplikasi
###############################################################################

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}=========================================="
echo "🔒 SECURITY CHECK SCRIPT"
echo "==========================================${NC}"
echo ""

ERRORS=0
WARNINGS=0
SUCCESS=0

# Function untuk print hasil
print_check() {
    local status=$1
    local message=$2

    if [ "$status" == "OK" ]; then
        echo -e "${GREEN}✓${NC} $message"
        ((SUCCESS++))
    elif [ "$status" == "WARN" ]; then
        echo -e "${YELLOW}⚠${NC} $message"
        ((WARNINGS++))
    else
        echo -e "${RED}✗${NC} $message"
        ((ERRORS++))
    fi
}

echo "Checking application security configuration..."
echo ""

# ==========================================
# 1. CHECK .ENV FILE
# ==========================================

echo -e "${BLUE}[1] Checking .env file...${NC}"

if [ -f .env ]; then
    print_check "OK" ".env file exists"

    # Check APP_ENV
    APP_ENV=$(grep "^APP_ENV=" .env | cut -d '=' -f2)
    if [ "$APP_ENV" == "production" ]; then
        print_check "OK" "APP_ENV is set to production"
    else
        print_check "WARN" "APP_ENV is '$APP_ENV' (should be 'production' for public server)"
    fi

    # Check APP_DEBUG
    APP_DEBUG=$(grep "^APP_DEBUG=" .env | cut -d '=' -f2)
    if [ "$APP_DEBUG" == "false" ]; then
        print_check "OK" "APP_DEBUG is disabled"
    else
        print_check "ERROR" "APP_DEBUG is enabled! MUST BE false in production"
    fi

    # Check APP_KEY
    APP_KEY=$(grep "^APP_KEY=" .env | cut -d '=' -f2)
    if [ -n "$APP_KEY" ] && [ "$APP_KEY" != "" ]; then
        print_check "OK" "APP_KEY is generated"
    else
        print_check "ERROR" "APP_KEY is not set! Run: php artisan key:generate"
    fi

    # Check APP_URL
    APP_URL=$(grep "^APP_URL=" .env | cut -d '=' -f2)
    if [[ "$APP_URL" == https://* ]]; then
        print_check "OK" "APP_URL uses HTTPS"
    elif [[ "$APP_URL" == http://localhost* ]] || [[ "$APP_URL" == http://127.0.0.1* ]]; then
        print_check "WARN" "APP_URL is localhost (OK for development)"
    else
        print_check "WARN" "APP_URL should use HTTPS in production"
    fi

    # Check Security Settings
    SQL_PROTECTION=$(grep "^SECURITY_SQL_INJECTION_PROTECTION=" .env 2>/dev/null | cut -d '=' -f2)
    if [ "$SQL_PROTECTION" == "true" ]; then
        print_check "OK" "SQL Injection Protection enabled"
    else
        print_check "WARN" "SQL Injection Protection not explicitly enabled"
    fi

    XSS_PROTECTION=$(grep "^SECURITY_XSS_PROTECTION=" .env 2>/dev/null | cut -d '=' -f2)
    if [ "$XSS_PROTECTION" == "true" ]; then
        print_check "OK" "XSS Protection enabled"
    else
        print_check "WARN" "XSS Protection not explicitly enabled"
    fi

    # Check Log Level
    LOG_LEVEL=$(grep "^LOG_LEVEL=" .env | cut -d '=' -f2)
    if [ "$LOG_LEVEL" == "error" ] || [ "$LOG_LEVEL" == "warning" ]; then
        print_check "OK" "LOG_LEVEL is set to $LOG_LEVEL (production safe)"
    else
        print_check "WARN" "LOG_LEVEL is '$LOG_LEVEL' (consider 'error' for production)"
    fi

else
    print_check "ERROR" ".env file not found!"
fi

echo ""

# ==========================================
# 2. CHECK FILE PERMISSIONS
# ==========================================

echo -e "${BLUE}[2] Checking file permissions...${NC}"

# Check .env permission
if [ -f .env ]; then
    ENV_PERM=$(stat -c %a .env 2>/dev/null || stat -f %A .env 2>/dev/null)
    if [ "$ENV_PERM" == "600" ]; then
        print_check "OK" ".env permissions are 600"
    else
        print_check "WARN" ".env permissions are $ENV_PERM (should be 600)"
        echo "      Run: chmod 600 .env"
    fi
fi

# Check storage permission
if [ -d storage ]; then
    if [ -w storage ]; then
        print_check "OK" "storage directory is writable"
    else
        print_check "ERROR" "storage directory is not writable"
        echo "      Run: chmod -R 775 storage"
    fi
fi

# Check bootstrap/cache permission
if [ -d bootstrap/cache ]; then
    if [ -w bootstrap/cache ]; then
        print_check "OK" "bootstrap/cache directory is writable"
    else
        print_check "ERROR" "bootstrap/cache directory is not writable"
        echo "      Run: chmod -R 775 bootstrap/cache"
    fi
fi

echo ""

# ==========================================
# 3. CHECK SECURITY FILES
# ==========================================

echo -e "${BLUE}[3] Checking security files...${NC}"

# Check Exception Handler
if [ -f app/Exceptions/Handler.php ]; then
    print_check "OK" "Custom Exception Handler exists"
else
    print_check "WARN" "Custom Exception Handler not found"
fi

# Check Security Middleware
if [ -f app/Http/Middleware/SqlInjectionProtection.php ]; then
    print_check "OK" "SQL Injection Protection middleware exists"
else
    print_check "ERROR" "SQL Injection Protection middleware not found"
fi

if [ -f app/Http/Middleware/XssProtection.php ]; then
    print_check "OK" "XSS Protection middleware exists"
else
    print_check "ERROR" "XSS Protection middleware not found"
fi

if [ -f app/Http/Middleware/SecurityHeaders.php ]; then
    print_check "OK" "Security Headers middleware exists"
else
    print_check "ERROR" "Security Headers middleware not found"
fi

if [ -f app/Http/Middleware/ForceHttps.php ]; then
    print_check "OK" "Force HTTPS middleware exists"
else
    print_check "ERROR" "Force HTTPS middleware not found"
fi

# Check security config
if [ -f config/security.php ]; then
    print_check "OK" "Security configuration file exists"
else
    print_check "WARN" "Security configuration file not found"
fi

echo ""

# ==========================================
# 4. CHECK .GITIGNORE
# ==========================================

echo -e "${BLUE}[4] Checking .gitignore...${NC}"

if [ -f .gitignore ]; then
    if grep -q "^\.env$" .gitignore; then
        print_check "OK" ".env is in .gitignore"
    else
        print_check "ERROR" ".env is NOT in .gitignore!"
    fi

    if grep -q "\.sqlite" .gitignore; then
        print_check "OK" "Database files are in .gitignore"
    else
        print_check "WARN" "Database files may not be in .gitignore"
    fi

    if grep -q "\.sql" .gitignore; then
        print_check "OK" "SQL dumps are in .gitignore"
    else
        print_check "WARN" "SQL dumps may not be in .gitignore"
    fi
else
    print_check "ERROR" ".gitignore file not found"
fi

echo ""

# ==========================================
# 5. CHECK DEPENDENCIES
# ==========================================

echo -e "${BLUE}[5] Checking dependencies...${NC}"

if [ -d vendor ]; then
    print_check "OK" "Composer dependencies installed"
else
    print_check "ERROR" "Composer dependencies not installed"
    echo "      Run: composer install --no-dev --optimize-autoloader"
fi

if [ -d node_modules ]; then
    print_check "OK" "NPM dependencies installed"
else
    print_check "WARN" "NPM dependencies not installed"
    echo "      Run: npm install && npm run build"
fi

if [ -d public/build ] || [ -f public/build/manifest.json ]; then
    print_check "OK" "Frontend assets compiled"
else
    print_check "WARN" "Frontend assets not compiled"
    echo "      Run: npm run build"
fi

echo ""

# ==========================================
# 6. CHECK CACHE & OPTIMIZATION
# ==========================================

echo -e "${BLUE}[6] Checking optimization...${NC}"

if [ -f bootstrap/cache/config.php ]; then
    print_check "OK" "Configuration cached"
else
    print_check "WARN" "Configuration not cached (run in production)"
    echo "      Run: php artisan config:cache"
fi

if [ -f bootstrap/cache/routes-v7.php ] || [ -f bootstrap/cache/routes.php ]; then
    print_check "OK" "Routes cached"
else
    print_check "WARN" "Routes not cached (run in production)"
    echo "      Run: php artisan route:cache"
fi

echo ""

# ==========================================
# 7. CHECK SENSITIVE FILES
# ==========================================

echo -e "${BLUE}[7] Checking for sensitive files in git...${NC}"

if [ -d .git ]; then
    # Check if .env is tracked
    if git ls-files --error-unmatch .env >/dev/null 2>&1; then
        print_check "ERROR" ".env is tracked by git! Remove it immediately"
        echo "      Run: git rm --cached .env && git commit -m 'Remove .env from git'"
    else
        print_check "OK" ".env is not tracked by git"
    fi

    # Check for SQL files
    if git ls-files | grep -q "\.sql$"; then
        print_check "WARN" "SQL files found in git (may contain sensitive data)"
    else
        print_check "OK" "No SQL files in git"
    fi

    # Check for database files
    if git ls-files | grep -q "\.sqlite$"; then
        print_check "ERROR" "SQLite database files found in git!"
    else
        print_check "OK" "No database files in git"
    fi
else
    print_check "WARN" "Not a git repository"
fi

echo ""

# ==========================================
# SUMMARY
# ==========================================

echo -e "${BLUE}=========================================="
echo "SECURITY CHECK SUMMARY"
echo "==========================================${NC}"
echo ""

echo -e "${GREEN}✓ Passed:${NC} $SUCCESS"
echo -e "${YELLOW}⚠ Warnings:${NC} $WARNINGS"
echo -e "${RED}✗ Errors:${NC} $ERRORS"
echo ""

if [ $ERRORS -gt 0 ]; then
    echo -e "${RED}⚠️  CRITICAL: Fix all errors before deploying to production!${NC}"
    exit 1
elif [ $WARNINGS -gt 0 ]; then
    echo -e "${YELLOW}⚠️  WARNING: Review warnings and fix if necessary${NC}"
    exit 0
else
    echo -e "${GREEN}✅ All security checks passed!${NC}"
    echo -e "${GREEN}Application is ready for production deployment.${NC}"
    exit 0
fi
