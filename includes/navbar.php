<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include_once 'config/db.php';

// Initialize wallet balance
$wallet_balance = 0.00;

// Fetch wallet balance if user is logged in
if (isset($_SESSION['user_id']) && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT wallet_balance, name FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $wallet_balance = $user['wallet_balance'];
            $user_name = $user['name'];
        }
    } catch (PDOException $e) {
        // Handle error silently
        $wallet_balance = 0.00;
    }
}
?>

<style>
    /* Scoped navbar styles - light white/sky-blue mix */
    .ek-navbar {
        background: linear-gradient(90deg, #ffffff 0%, #e6f4ff 100%);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
    }
    .ek-navbar .navbar-brand img { height: 40px; width: auto; }
    .ek-navbar .nav-link, .ek-navbar .navbar-brand { color: #0f2e46; }
    .ek-navbar .nav-link { font-size: .95rem; font-weight: 600; letter-spacing: .2px; padding: .5rem .75rem; }
    .ek-navbar .nav-link:hover { color: #0a58ca; }
    .ek-divider { color: rgba(0,0,0,.25); margin: 0 .75rem; }

    /* Rounded pill search */
    .ek-search .form-control { border-radius: 999px 0 0 999px; padding-left: 1rem; height: 44px; }
    .ek-search .btn { border-radius: 0 999px 999px 0; height: 44px; background: #0ea5e9; color: #fff; border-color: #0ea5e9; }
    .ek-search .form-control:focus { box-shadow: 0 0 0 .2rem rgba(14,165,233,.25); border-color: #0ea5e9; }

    /* Right icon links */
    .ek-right a { color: #0f2e46; text-decoration: none; font-weight: 800; font-size: .95rem; letter-spacing: .2px; }
    .ek-right a:hover { color: #0a58ca; }

    /* Wallet chip */
    .wallet-chip { background: #e9f6ff; color: #0f2e46; border-radius: 999px; padding: .25rem .6rem; font-weight: 800; letter-spacing: .2px; }

    /* Unique button styles */
    .btn-neo {
        background: linear-gradient(135deg, #38bdf8, #0ea5e9);
        border: 0;
        color: #fff;
        border-radius: 999px;
        padding: .35rem .9rem;
        font-weight: 800;
        letter-spacing: .3px;
        box-shadow: 0 6px 14px rgba(14,165,233,.25);
        transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
    }
    .btn-neo:hover { transform: translateY(-1px); filter: brightness(1.03); box-shadow: 0 10px 18px rgba(14,165,233,.35); }
    .btn-neo:active { transform: translateY(0); }

    .btn-ghost-sky {
        background: #ffffff;
        color: #0f2e46;
        border: 2px solid #0ea5e9;
        border-radius: 999px;
        padding: .33rem .85rem;
        font-weight: 800;
        letter-spacing: .3px;
        transition: background .15s ease, color .15s ease, box-shadow .15s ease;
    }
    .btn-ghost-sky:hover { background: #e6f7ff; color: #0a58ca; box-shadow: 0 4px 10px rgba(14,165,233,.2); }

    @media (max-width: 991.98px) {
        /* Tablet and below */
        .ek-search { order: 3; width: 100%; margin-top: .75rem; }
        .ek-search .input-group { width: 100%; }
        .ek-right { gap: .75rem !important; }
        .ek-right a { font-size: .9rem; }
        .wallet-chip { display: none !important; }
    }

    @media (max-width: 767.98px) {
        /* Mobile */
        .ek-navbar .nav-link { font-size: .9rem; padding: .45rem .6rem; }
        .btn-neo, .btn-ghost-sky { padding: .35rem .7rem; font-weight: 700; }
        .ek-subbar .profit-label { font-size: .9rem; }
        .ek-subbar .profit-amount { font-size: 1rem; }
        .ek-subbar .btn-learn { padding: .4rem .8rem; font-size: .9rem; }
        .ek-subbar .announce { font-size: .95rem; }
        .ek-subbar .container { gap: .75rem !important; }
        .ek-subbar .links-small a { font-size: .9rem; }
    }

    @media (max-width: 575.98px) {
        /* Small mobile */
        .ek-subbar .container { flex-direction: column; align-items: flex-start; }
        .ek-search { margin-top: .5rem; }
        .ek-right { width: 100%; justify-content: flex-start; flex-wrap: wrap; }
        .ek-subbar .links-small a { font-size: .8rem; }
    }
    /* Subbar styles */
    .ek-subbar { background: #ffffff; border-bottom: 1px solid #eef2f0; }
    .ek-subbar .profit-label { color: #5b6b7a; font-weight: 600; font-size: .95rem; }
    .ek-subbar .profit-amount { color: #0f2e46; font-weight: 800; font-size: 1.1rem; }
    .ek-subbar .btn-learn { border: 2px solid #0ea5e9; background: #ffffff; color: #0f2e46; border-radius: 999px; padding: .45rem 1rem; font-weight: 800; letter-spacing: .2px; }
    .ek-subbar .btn-learn:hover { background: #e6f7ff; color: #0a58ca; }
    .ek-subbar .announce { color: #0f2e46; font-weight: 800; display: inline-flex; align-items: center; gap: .35rem; letter-spacing: .2px; }
    .ek-subbar .btn-eligibility { border: 2px solid #42b883; color: #0f3d2e; background: #fff; border-radius: 999px; padding: .35rem .8rem; font-weight: 700; }
    .ek-subbar .btn-eligibility:hover { background: #e8f6ef; }
</style>

<nav class="navbar navbar-expand-lg navbar-light ek-navbar sticky-top">
    <div class="container">
        <!-- Left: Hamburger + Brand -->
         <a class="navbar-brand fw-bold" href="index.php">
            <img src="kamateraho/img/logo.png" alt="KamateRaho Logo" style="height: 50px; width: auto;">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#ekNav" aria-controls="ekNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Center: Search -->
        <div class="ek-search flex-grow-1 d-none d-lg-block mx-3" style="max-width: 620px;">
            <form action="search.php" method="get">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search for partners or deals" aria-label="Search">
                    <button class="btn btn-dark" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>

        <!-- Right: Icon links -->
        <div class="collapse navbar-collapse" id="ekNav">
            <ul class="navbar-nav me-auto d-lg-none mt-2">
                <!-- Mobile menu items -->
                <li class="nav-item"><a class="nav-link" href="index.php">Offers</a></li>
                <li class="nav-item">
                    <!-- Mobile search -->
                    <div class="ek-search w-100">
                        <form action="search.php" method="get">
                            <div class="input-group">
                                <input type="text" name="q" class="form-control" placeholder="Search for partners or deals" aria-label="Search">
                                <button class="btn btn-dark" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </li>
            </ul>

            <div class="ms-auto d-flex align-items-center gap-3 ek-right">
                <a href="index.php" class="d-flex align-items-center"><i class="fas fa-gift"></i>Offers</a>
                <span class="ek-divider d-none d-lg-inline">|</span>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="wallet-chip d-none d-md-inline d-flex align-items-center">
                        <i class="fas fa-wallet me-1"></i>₹<?php echo number_format($wallet_balance, 2); ?>
                    </a>
                    <a href="withdraw.php" class="btn btn-neo btn-sm">Withdraw</a>
                    <div class="dropdown">
                        <a class="btn btn-ghost-sky btn-sm dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($user_name ?? 'My Profile'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="reset_password.php"><i class="fas fa-key me-2"></i>Reset Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-ghost-sky btn-sm">Login</a>
                    <a href="register.php" class="btn btn-neo btn-sm">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Secondary info strip under navbar -->
<div class="ek-subbar py-2">
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-baseline gap-2">
            <span class="profit-label">Your Total Profit</span>
            <span class="profit-amount">₹<?php echo number_format($wallet_balance, 2); ?></span>
        </div>

        <a href="how-to-earn.php" class="btn btn-learn">
            Learn How To Earn More <i class="fas fa-arrow-right ms-2"></i>
        </a>

        <div class="d-flex align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="contact.php" class="text-decoration-none" style="color:#0f3d2e;font-weight:700;"><i class="fas fa-phone me-1"></i>Contact</a>
                <a href="user_messages.php" class="text-decoration-none" style="color:#0f3d2e;font-weight:700;"><i class="fas fa-envelope me-1"></i>Messages</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="#" class="text-decoration-none" style="color:#0f3d2e;font-weight:700;" data-bs-toggle="modal" data-bs-target="#referralModal"><i class="fas fa-user-friends me-1"></i>Refer Friend & Earn</a>
                <?php else: ?>
                    <a href="register.php" class="text-decoration-none" style="color:#0f3d2e;font-weight:700;"><i class="fas fa-user-friends me-1"></i>Refer Friend & Earn</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>



