# Security Audit Report - WMS SMKN 20

**Tanggal:** 27 Agustus 2026  
**Scope:** Full-stack Laravel + Vue.js (Inertia) application  
**Severity:** Critical / High / Medium / Low

---

## Executive Summary

Website WMS SMKN 20 memiliki beberapa masalah keamanan yang perlu diperhatikan. Yang paling kritis adalah **`.env` file yang sudah di-commit ke git** (berisi APP_KEY), serta beberapa celah pada **otorisasi** dan **input validation**.

---

## CRITICAL

### [C1] .env File Berisi APP_KEY Sudah di-Commit ke Git

**Severity:** Critical  
**Location:** `.env:3`, `.env.example:3`

**Evidence:**
```
APP_KEY=base64:RYPsYasfprMf/EyY2bROdA3QUX7TA7IT5GKhtVOuNpI=
```

**Impact:** APP_KEY digunakan untuk enkripsi session, cookie, dan data sensitif lainnya. Jika bocor, attacker bisa:
- Decrypt session cookies
- Forge session cookies
- Akses akun manapun termasuk admin

**Fix:**
1. **Rotate APP_KEY sekarang:**
   ```bash
   php artisan key:generate
   ```
2. **Remove .env dari git history:**
   ```bash
   git rm --cached .env
   git commit -m "Remove .env from tracking"
   ```
3. **Add to .gitignore** (sudah ada, tapi sudah terlambat)

---

## HIGH

### [H1] SQL Injection pada Query Search

**Severity:** High  
**Location:**
- `ItemController.php:19-21`
- `UserController.php:19-20`
- `RiwayatController.php:14-16`

**Evidence:**
```php
$q->where('sku', 'like', "%{$search}%")
  ->orWhere('name', 'like', "%{$search}%");
```

**Impact:** Meskipun Laravel Query Builder secara otomatis menggunakan prepared statements, penggunaan `%{$search}%` tetap berisiko jika search term mengandung wildcard characters yang bisa dimanfaatkan.

**Fix:** Gunakan parameter binding:
```php
$q->where('sku', 'like', '%' . $search . '%')
  ->orWhere('name', 'like', '%' . $search . '%');
```

---

### [H2] Missing Authorization Check pada Beberapa Controller

**Severity:** High  
**Location:**
- `InboundTransactionController.php:15-26` (index)
- `OutboundTransactionController.php:16-32` (index)
- `RiwayatController.php:12` (index)

**Impact:** Semua user yang login bisa mengakses data transaksi, termasuk user dengan role "Staff Picker" yang seharusnya tidak perlu melihat semua data.

**Fix:** Tambahkan middleware role check:
```php
public function __construct()
{
    $this->middleware('role:Admin,Warehouse Manager')->only(['index', 'destroy']);
}
```

---

### [H3] Mass Assignment pada User Creation

**Severity:** High  
**Location:** `UserController.php:58`

**Evidence:**
```php
User::create($validated);
```

**Impact:** Jika attacker bisa manipulate request, mereka mungkin bisa inject field tambahan seperti `email_verified_at` atau field lain yang tidak seharusnya di-set.

**Fix:** Gunakan `$request->only()` atau `$request->except()` untuk filter field:
```php
User::create($request->only(['name', 'email', 'password', 'role', 'status']));
```

---

## MEDIUM

### [M1] SESSION_SECURE_COOKIE=false

**Severity:** Medium  
**Location:** `.env:35`

**Evidence:**
```
SESSION_SECURE_COOKIE=false
```

**Impact:** Session cookies akan dikirim melalui HTTP (tidak hanya HTTPS), memungkinkan session hijacking melalui network sniffing.

**Fix:** Untuk production, set:
```
SESSION_SECURE_COOKIE=true
```

---

### [M2] Missing Rate Limiting pada Auth Endpoints

**Severity:** Medium  
**Location:** `routes/web.php`

**Impact:** Tidak ada rate limiting pada login attempt, memungkinkan brute force attack.

**Fix:** Tambahkan throttle middleware:
```php
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('throttle:5,1');
```

---

### [H4] User Bisa Delete Diri Sendiri (Race Condition)

**Severity:** Medium  
**Location:** `UserController.php:101-110`

**Evidence:**
```php
public function destroy(User $user)
{
    if (auth()->id() === $user->id) {
        return redirect()->route('users.index')->withErrors([...]);
    }
    $user->delete();
}
```

**Impact:** Meskipun ada pengecekan, ini hanya di level application. Jika ada race condition atau bypass, user bisa menghapus diri sendiri.

**Fix:** Tambahkan constraint di database:
```php
Schema::table('users', function (Blueprint $table) {
    $table->unique('id');
});
```

---

## LOW

### [L1] Missing Security Headers (Non-Production)

**Severity:** Low  
**Location:** `SecurityHeaders.php:23`

**Evidence:**
```php
if (app()->environment('production')) {
    $response->headers->set('Content-Security-Policy', "...");
}
```

**Impact:** Security headers hanya aktif di production, development environment tidak terlindungi.

**Fix:** Aktifkan security headers untuk semua environment (kecuali CSP yang mungkin perlu dikecualikan untuk dev).

---

### [L2] Database Credentials为空

**Severity:** Low  
**Location:** `.env:28`

**Evidence:**
```
DB_PASSWORD=
```

**Impact:** MySQL root user tanpa password - berbahaya jika database port ter-expose ke network.

**Fix:** Set password untuk database user:
```
DB_PASSWORD=your_secure_password
```

---

## Summary

| Severity | Count | Status |
|----------|-------|--------|
| Critical | 1 | Perlu segera fix |
| High | 3 | Perlu segera fix |
| Medium | 3 | Perlu diperbaiki |
| Low | 2 | Nice to have |

---

## Recommendations

1. **Immediate:** Rotate APP_KEY dan hapus .env dari git history
2. **Short-term:** Tambahkan authorization checks dan rate limiting
3. **Long-term:** Implementasi security audit logging dan monitoring

---

*Report generated by security-best-practices skill*
