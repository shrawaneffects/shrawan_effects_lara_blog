<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\SecurityController as AdminSecurityController;
use App\Http\Controllers\Admin\SeoDashboardController as AdminSeoDashboardController;
use App\Http\Controllers\Admin\SeoAnalysisController as AdminSeoAnalysisController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;

/*
|--------------------------------------------------------------------------
| Public Frontend Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [PostController::class, 'show'])->name('blog.show');
Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/author/{id}', [BlogController::class, 'author'])->name('blog.author');

Route::post('/blog/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/captcha', [AuthController::class, 'getCaptchaChallenge'])->name('captcha.refresh');
Route::get('/api/captcha', [AuthController::class, 'getCaptchaChallenge']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Magic Link & Email OTP Direct Login Routes
Route::post('/login/magic-link', [AuthController::class, 'sendMagicLink'])->name('login.magic-link');
Route::get('/login/magic-link/{token}', [AuthController::class, 'verifyMagicLink'])->name('login.magic-link.verify');
Route::post('/login/email-otp', [AuthController::class, 'sendLoginOtp'])->name('login.email-otp');
Route::post('/login/email-otp/verify', [AuthController::class, 'verifyLoginOtpCode'])->name('login.email-otp.verify');

// 2FA Login Email OTP Routes
Route::get('/login/verify-otp', [AuthController::class, 'showLoginOtpForm'])->name('login.verify-otp');
Route::post('/login/verify-otp', [AuthController::class, 'verifyLoginOtp'])->name('login.verify-otp.post');
Route::post('/login/resend-otp', [AuthController::class, 'resendLoginOtp'])->name('login.resend-otp');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Registration Email OTP Routes
Route::get('/register/verify-otp', [AuthController::class, 'showRegisterOtpForm'])->name('register.verify-otp');
Route::post('/register/verify-otp', [AuthController::class, 'verifyRegisterOtp'])->name('register.verify-otp.post');
Route::post('/register/resend-otp', [AuthController::class, 'resendRegisterOtp'])->name('register.resend-otp');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Forgot & Reset Password Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

/*
|--------------------------------------------------------------------------
| Authenticated User Profile & GDPR Privacy Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/export-data', [ProfileController::class, 'exportData'])->name('profile.export-data');
    Route::post('/profile/logout-other-devices', [ProfileController::class, 'logoutOtherDevices'])->name('profile.logout-other-devices');
    Route::delete('/profile/account', [ProfileController::class, 'deleteAccount'])->name('profile.delete-account');
});

/*
|--------------------------------------------------------------------------
| Admin & Author Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Posts Management
    Route::resource('posts', AdminPostController::class);
    Route::post('/posts/{id}/toggle-featured', [AdminPostController::class, 'toggleFeatured'])->name('posts.toggle-featured');
    Route::post('/posts/{id}/toggle-trending', [AdminPostController::class, 'toggleTrending'])->name('posts.toggle-trending');

    // Dynamic Pages Management
    Route::resource('pages', AdminPageController::class)->except(['show']);
    Route::post('/pages/{id}/toggle-status', [AdminPageController::class, 'toggleStatus'])->name('pages.toggle-status');

    // Services Management
    Route::resource('services', AdminServiceController::class)->except(['show']);
    Route::post('/services/{id}/toggle-active', [AdminServiceController::class, 'toggleActive'])->name('services.toggle-active');

    // Centralized Media Library (Images, Videos, Audios, Documents)
    Route::resource('media', AdminMediaController::class)->except(['create', 'edit']);
    Route::get('/media-picker-api', [AdminMediaController::class, 'apiPicker'])->name('media.picker-api');

    // Categories Management
    Route::resource('categories', AdminCategoryController::class)->except(['create', 'show', 'edit']);

    // Tags Management
    Route::resource('tags', AdminTagController::class)->except(['create', 'show', 'edit']);

    // Comments Moderation
    Route::get('/comments', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::post('/comments/{id}/approve', [AdminCommentController::class, 'approve'])->name('comments.approve');
    Route::post('/comments/{id}/spam', [AdminCommentController::class, 'spam'])->name('comments.spam');
    Route::delete('/comments/{id}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{id}/reply', [AdminCommentController::class, 'reply'])->name('comments.reply');

    // Users Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::put('/users/{id}/role', [AdminUserController::class, 'updateRole'])->name('users.update-role');
    Route::post('/users/{id}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Contacts Messages
    Route::get('/contacts', [AdminContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts/{id}/read', [AdminContactController::class, 'markRead'])->name('contacts.read');
    Route::delete('/contacts/{id}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');

    // Website Settings & Logo
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

    // Menu Builder & Drag-and-Drop Arrangement
    Route::get('/menus', [AdminMenuController::class, 'index'])->name('menus.index');
    Route::post('/menus', [AdminMenuController::class, 'store'])->name('menus.store');
    Route::put('/menus/{id}', [AdminMenuController::class, 'update'])->name('menus.update');
    Route::delete('/menus/{id}', [AdminMenuController::class, 'destroy'])->name('menus.destroy');
    Route::post('/menus/reorder', [AdminMenuController::class, 'reorder'])->name('menus.reorder');
    Route::post('/menus/reset-defaults', [AdminMenuController::class, 'resetDefaults'])->name('menus.reset-defaults');
    Route::post('/menus/{id}/toggle-visibility', [AdminMenuController::class, 'toggleVisibility'])->name('menus.toggle-visibility');
    Route::post('/menus/bulk-action', [AdminMenuController::class, 'bulkAction'])->name('menus.bulk-action');

    // Cyber Security Center, Audit Logs & Disaster Recovery Backups
    Route::get('/security', [AdminSecurityController::class, 'index'])->name('security.index');
    Route::get('/security/audit-logs', [AdminSecurityController::class, 'auditLogs'])->name('security.audit-logs');
    Route::post('/security/audit-logs/clear', [AdminSecurityController::class, 'clearAuditLogs'])->name('security.audit-logs.clear');
    Route::get('/security/backups', [AdminSecurityController::class, 'backups'])->name('security.backups');
    Route::post('/security/backups', [AdminSecurityController::class, 'createBackup'])->name('security.backups.create');
    Route::get('/security/backups/{filename}/download', [AdminSecurityController::class, 'downloadBackup'])->name('security.backups.download');
    Route::post('/security/backups/{filename}/restore', [AdminSecurityController::class, 'restoreBackup'])->name('security.backups.restore');
    Route::delete('/security/backups/{filename}', [AdminSecurityController::class, 'deleteBackup'])->name('security.backups.delete');

    // 2026 On-Page SEO Suite, 301 Redirects & Server Config
    Route::get('/seo', [AdminSeoDashboardController::class, 'index'])->name('seo.index');
    Route::get('/seo/grader', [AdminSeoDashboardController::class, 'grader'])->name('seo.grader');
    Route::post('/seo/grader/run', [AdminSeoDashboardController::class, 'runGrader'])->name('seo.grader.run');
    Route::post('/seo/run-batch-audit', [AdminSeoDashboardController::class, 'runBatchAudit'])->name('seo.run-batch-audit');
    Route::get('/seo/redirects', [AdminSeoDashboardController::class, 'redirects'])->name('seo.redirects');
    Route::post('/seo/redirects', [AdminSeoDashboardController::class, 'storeRedirect'])->name('seo.redirects.store');
    Route::delete('/seo/redirects/{id}', [AdminSeoDashboardController::class, 'destroyRedirect'])->name('seo.redirects.destroy');
    Route::get('/seo/server-config', [AdminSeoDashboardController::class, 'serverConfig'])->name('seo.server-config');
    Route::post('/seo/server-config/robots', [AdminSeoDashboardController::class, 'updateRobots'])->name('seo.server-config.robots');
    Route::post('/seo/server-config/htaccess', [AdminSeoDashboardController::class, 'updateHtaccess'])->name('seo.server-config.htaccess');
    Route::post('/seo/server-config/restore-robots', [AdminSeoDashboardController::class, 'restoreRobots'])->name('seo.server-config.restore-robots');
    Route::post('/seo/server-config/restore-htaccess', [AdminSeoDashboardController::class, 'restoreHtaccess'])->name('seo.server-config.restore-htaccess');
    Route::get('/seo/clear-cache', [AdminSeoDashboardController::class, 'clearCache'])->name('seo.clear-cache');
    Route::post('/seo/clear-cache', [AdminSeoDashboardController::class, 'clearCache'])->name('seo.clear-cache.post');
    Route::get('/seo/optimize', [AdminSeoDashboardController::class, 'optimizeSystem'])->name('seo.optimize');
    Route::post('/seo/optimize', [AdminSeoDashboardController::class, 'optimizeSystem'])->name('seo.optimize.post');
    Route::post('/seo/server-config/storage-link', [AdminSeoDashboardController::class, 'linkStorage'])->name('seo.server-config.storage-link');
    Route::post('/seo/analyze', [AdminSeoAnalysisController::class, 'analyze'])->name('seo.analyze');
});

// --------------------------------------------------------------------------
// Storage Fallback Route (ensures images/avatars/media load seamlessly even if symlink is missing on shared hosting)
// --------------------------------------------------------------------------
Route::get('storage/{path}', function (string $path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*')->name('storage.fallback');

