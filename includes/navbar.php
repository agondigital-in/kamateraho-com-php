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
    
    /* Spin & Earn Button */
    .spin-btn {
        background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
        border: none;
        color: #fff;
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(255, 154, 158, 0.4);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .spin-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 154, 158, 0.6);
    }
    
    .spin-btn:active {
        transform: translateY(0);
    }
    
    /* Enhanced Wheel Styles */
    .wheel-container {
        position: relative;
        width: 300px;
        height: 300px;
        margin: 0 auto;
        /* Add celebratory animation preference */
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }
    
    .wheel {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        position: relative;
        overflow: hidden;
        border: 8px solid #333;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        transition: transform 4s cubic-bezier(0.17, 0.67, 0.12, 0.99);
        background: #fff;
    }
    
    .wheel-section {
        position: absolute;
        width: 50%;
        height: 50%;
        transform-origin: bottom right;
        left: 0;
        top: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    
    .wheel-section-content {
        transform: rotate(30deg);
        width: 100px;
        text-align: right;
        padding-right: 20px;
        font-weight: bold;
        font-size: 14px;
        color: #333;
        text-shadow: 1px 1px 2px rgba(255,255,255,0.7);
    }
    
    .wheel-pointer {
        position: absolute;
        top: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 40px;
        height: 40px;
        background: #fff;
        clip-path: polygon(50% 100%, 0 0, 100% 0);
        z-index: 10;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        /* Add pointer animation */
        animation: bounce 1s infinite;
    }
    
    @keyframes bounce {
        0%, 100% { transform: translateX(-50%) translateY(0); }
        50% { transform: translateX(-50%) translateY(-5px); }
    }
    
    .wheel-center {
        position: absolute;
        width: 50px;
        height: 50px;
        background: #333;
        border-radius: 50%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 5;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    }
    
    .spin-result {
        text-align: center;
        margin-top: 20px;
        font-size: 24px;
        font-weight: bold;
        min-height: 40px;
        /* Add celebratory effect for wins */
        transition: all 0.3s ease;
        padding: 15px;
        border-radius: 10px;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .spin-result.win {
        color: #28a745;
        text-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
        animation: winPulse 0.5s ease-in-out;
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
    }
    
    @keyframes winPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .spins-left {
        text-align: center;
        margin-top: 10px;
        font-weight: bold;
        color: #666;
        font-size: 18px;
    }
    
    /* Enhanced Spin Button */
    #spinWheelBtn {
        background: linear-gradient(135deg, #ff6b6b, #ffa502);
        border: none;
        color: white;
        padding: 15px 35px;
        border-radius: 50px;
        font-weight: bold;
        font-size: 20px;
        box-shadow: 0 6px 20px rgba(255, 107, 107, 0.5);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        overflow: hidden;
    }
    
    #spinWheelBtn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: 0.5s;
    }
    
    #spinWheelBtn:hover:not(:disabled)::before {
        left: 100%;
    }
    
    #spinWheelBtn:hover:not(:disabled) {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(255, 107, 107, 0.7);
    }
    
    #spinWheelBtn:active:not(:disabled) {
        transform: translateY(1px);
    }
    
    #spinWheelBtn:disabled {
        background: #cccccc;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    
    /* Modal enhancements */
    .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .modal-header {
        background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
        border-radius: 15px 15px 0 0;
        border: none;
        color: white;
    }
    
    .modal-title {
        font-weight: bold;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    }
    
    @media (max-width: 576px) {
        .wheel-container {
            width: 250px;
            height: 250px;
        }
    }
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
                <a href="contact.php" class="text-decoration-none" style="color:#0f3d2e;font-weight:700;"><i class="fas fa-phone me-1"></i>Support 24/7</a>
                <a href="user_messages.php" class="text-decoration-none" style="color:#0f3d2e;font-weight:700;"><i class="fas fa-envelope me-1"></i>Messages</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="#" class="text-decoration-none" style="color:#0f3d2e;font-weight:700;" data-bs-toggle="modal" data-bs-target="#referralModal"><i class="fas fa-user-friends me-1"></i>Refer Friend & Earn</a>
                    <a href="#" class="text-decoration-none" style="color:#0f3d2e;font-weight:700;" data-bs-toggle="modal" data-bs-target="#spinModal"><i class="fas fa-sync-alt me-1"></i>Spin & Earn</a>
                <?php else: ?>
                    <a href="register.php" class="text-decoration-none" style="color:#0f3d2e;font-weight:700;"><i class="fas fa-user-friends me-1"></i>Refer Friend & Earn</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Spin & Earn Modal -->
<div class="modal fade" id="spinModal" tabindex="-1" aria-labelledby="spinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="spinModalLabel">🎡 Spin & Earn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="spins-left" id="spinsLeft">Spins left: <span id="spinsCount">3</span></div>
                
                <div class="wheel-container">
                    <div class="wheel-pointer"></div>
                    <div class="wheel" id="wheel">
                        <!-- Wheel sections will be generated by JavaScript -->
                    </div>
                    <div class="wheel-center"></div>
                </div>
                
                <div class="spin-result" id="spinResult">
                    <i class="fas fa-star"></i> Click SPIN to try your luck! <i class="fas fa-star"></i>
                </div>
                
                <button id="spinWheelBtn" class="spin-btn mt-3" style="padding: 12px 30px;">
                    <i class="fas fa-sync-alt"></i> SPIN
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Spin & Earn functionality
    const spinModal = document.getElementById('spinModal');
    const spinWheelBtn = document.getElementById('spinWheelBtn');
    const wheel = document.getElementById('wheel');
    const spinResult = document.getElementById('spinResult');
    const spinsCount = document.getElementById('spinsCount');
    
    // Create wheel sections
    const rewards = ['₹1', '₹3', '₹5', 'Better Luck', 'Try Again', 'No Win']; // Updated rewards
    const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40']; // Kept same colors
    
    rewards.forEach((reward, index) => {
        const section = document.createElement('div');
        section.className = 'wheel-section';
        section.style.transform = `rotate(${index * 60}deg)`;
        section.style.backgroundColor = colors[index];
        
        const content = document.createElement('div');
        content.className = 'wheel-section-content';
        content.textContent = reward;
        
        section.appendChild(content);
        wheel.appendChild(section);
    });
    
    // Get initial spins count when modal is shown
    spinModal.addEventListener('shown.bs.modal', function () {
        fetch('spin_earn.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    spinsCount.textContent = data.spins_left;
                    if (data.spins_left <= 0) {
                        spinWheelBtn.disabled = true;
                        spinWheelBtn.textContent = 'No Spins Left';
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    });
    
    // Spin the wheel
    spinWheelBtn.addEventListener('click', function() {
        // Disable button during spin
        spinWheelBtn.disabled = true;
        spinWheelBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Spinning...';
        spinResult.innerHTML = '<i class="fas fa-cog fa-spin"></i> Spinning the wheel... <i class="fas fa-cog fa-spin"></i>';
        spinResult.className = 'spin-result'; // Reset classes
        
        // Store the spin count to determine behavior
        const currentSpinCount = 3 - parseInt(spinsCount.textContent);
        
        // Send request to spin
        fetch('spin_earn.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update spins left
                spinsCount.textContent = data.spins_left;
                
                // Calculate rotation based on reward and spin behavior
                // Updated to match new reward values (1, 3, 5)
                let targetPosition = 0;
                switch(data.reward) {
                    case 1:
                        targetPosition = 0; // First section
                        break;
                    case 3:
                        targetPosition = 1; // Second section
                        break;
                    case 5:
                        targetPosition = 2; // Third section
                        break;
                    default:
                        targetPosition = 0; // Default to first section
                }
                
                // Calculate rotation based on spin behavior
                let baseRotation = 0;
                if (data.spin_behavior === 'normal') {
                    // First spin: 3-5 full rotations + position
                    const rotations = 3 + Math.floor(Math.random() * 3);
                    baseRotation = (360 * rotations) + (360 - (targetPosition * 60));
                } else if (data.spin_behavior === 'more_rotations') {
                    // Second spin: 6-8 full rotations + position
                    const rotations = 6 + Math.floor(Math.random() * 3);
                    baseRotation = (360 * rotations) + (360 - (targetPosition * 60));
                } else {
                    // Third spin: 10+ full rotations + position
                    const rotations = 10 + Math.floor(Math.random() * 5);
                    baseRotation = (360 * rotations) + (360 - (targetPosition * 60));
                }
                
                // Apply rotation with appropriate easing
                wheel.style.transform = `rotate(${baseRotation}deg)`;
                wheel.style.transition = 'transform 4s cubic-bezier(0.2, 0.8, 0.3, 1)';
                
                // After animation, show result
                setTimeout(() => {
                    spinResult.innerHTML = data.message;
                    spinWheelBtn.disabled = false;
                    spinWheelBtn.innerHTML = '<i class="fas fa-sync-alt"></i> SPIN';
                    
                    // Add celebratory effect for wins
                    if (data.reward > 0) {
                        spinResult.classList.add('win');
                        
                        // Add celebratory animation preference - fireworks effect
                        createFireworks();
                    }
                    
                    // Check if no spins left
                    if (data.spins_left <= 0) {
                        spinWheelBtn.disabled = true;
                        spinWheelBtn.textContent = 'No Spins Left';
                    }
                }, 4000);
            } else {
                // Error or no spins left
                spinResult.innerHTML = data.message;
                spinWheelBtn.disabled = false;
                spinWheelBtn.innerHTML = '<i class="fas fa-sync-alt"></i> SPIN';
                
                // If no spins left, disable button
                if (data.message.includes('maximum spins')) {
                    spinWheelBtn.disabled = true;
                    spinWheelBtn.textContent = 'No Spins Left';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            spinResult.innerHTML = 'Error occurred. Please try again.';
            spinWheelBtn.disabled = false;
            spinWheelBtn.innerHTML = '<i class="fas fa-sync-alt"></i> SPIN';
        });
    });
    
    // Function to create celebratory fireworks effect
    function createFireworks() {
        // Create a container for fireworks
        const container = document.createElement('div');
        container.style.position = 'fixed';
        container.style.top = '0';
        container.style.left = '0';
        container.style.width = '100%';
        container.style.height = '100%';
        container.style.pointerEvents = 'none';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        
        // Create multiple fireworks
        for (let i = 0; i < 20; i++) {
            setTimeout(() => {
                const firework = document.createElement('div');
                firework.style.position = 'absolute';
                firework.style.width = '10px';
                firework.style.height = '10px';
                firework.style.borderRadius = '50%';
                firework.style.backgroundColor = getRandomColor();
                firework.style.boxShadow = '0 0 10px 2px ' + getRandomColor();
                firework.style.left = Math.random() * 100 + '%';
                firework.style.top = Math.random() * 100 + '%';
                firework.style.opacity = '0';
                firework.style.transition = 'all 1s ease-out';
                
                container.appendChild(firework);
                
                // Animate firework
                setTimeout(() => {
                    firework.style.opacity = '1';
                    firework.style.transform = 'translate(' + (Math.random() * 100 - 50) + 'px, ' + (Math.random() * 100 - 50) + 'px)';
                }, 10);
                
                // Remove firework after animation
                setTimeout(() => {
                    firework.style.opacity = '0';
                    setTimeout(() => {
                        if (firework.parentNode) {
                            firework.parentNode.removeChild(firework);
                        }
                    }, 1000);
                }, 800);
            }, i * 100);
        }
        
        // Remove container after all fireworks are done
        setTimeout(() => {
            if (container.parentNode) {
                container.parentNode.removeChild(container);
            }
        }, 3000);
    }
    
    // Helper function to get random color for fireworks
    function getRandomColor() {
        const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#ffbe0b', '#fb5607', '#ff006e', '#8338ec'];
        return colors[Math.floor(Math.random() * colors.length)];
    }
});
</script>