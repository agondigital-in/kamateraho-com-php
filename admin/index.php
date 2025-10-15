<?php
$page_title = "Dashboard";
include '../config/db.php';
include 'includes/admin_layout.php'; // This includes auth check

// Fetch pending withdraw requests
try {
    $stmt = $pdo->query("SELECT wr.*, u.name, u.email, u.id as user_id FROM withdraw_requests wr 
                         JOIN users u ON wr.user_id = u.id 
                         WHERE wr.status = 'pending' 
                         ORDER BY wr.created_at DESC");
    $pending_withdraw_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching pending withdraw requests: " . $e->getMessage();
    $pending_withdraw_requests = [];
}

// Fetch pending contact messages
try {
    $stmt = $pdo->query("SELECT COUNT(*) as pending_messages FROM contact_messages WHERE status = 'pending'");
    $pending_messages_count = $stmt->fetch(PDO::FETCH_ASSOC)['pending_messages'] ?? 0;
} catch(PDOException $e) {
    $pending_messages_count = 0;
}

// Calculate total wallet balance across all users
try {
    $stmt = $pdo->query("SELECT SUM(wallet_balance) as total_balance FROM users");
    $total_wallet_balance = $stmt->fetch(PDO::FETCH_ASSOC)['total_balance'] ?? 0;
} catch(PDOException $e) {
    $total_wallet_balance = 0;
}

// Calculate total users
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
    $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'] ?? 0;
} catch(PDOException $e) {
    $total_users = 0;
}

// Calculate total categories
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total_categories FROM categories");
    $total_categories = $stmt->fetch(PDO::FETCH_ASSOC)['total_categories'] ?? 0;
} catch(PDOException $e) {
    $total_categories = 0;
}

// Calculate total offers
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total_offers FROM offers");
    $total_offers = $stmt->fetch(PDO::FETCH_ASSOC)['total_offers'] ?? 0;
} catch(PDOException $e) {
    $total_offers = 0;
}

// Fetch daily revenue data (last 7 days)
try {
    $stmt = $pdo->query("SELECT DATE(created_at) as date, SUM(amount) as revenue 
                         FROM wallet_history 
                         WHERE type = 'credit' AND status = 'approved' 
                         AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
                         GROUP BY DATE(created_at) 
                         ORDER BY date ASC");
    $daily_revenue = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $daily_revenue = [];
}

// Fetch monthly revenue data (last 6 months)
try {
    $stmt = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as revenue 
                         FROM wallet_history 
                         WHERE type = 'credit' AND status = 'approved' 
                         AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) 
                         GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
                         ORDER BY month ASC");
    $monthly_revenue = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $monthly_revenue = [];
}

// Fetch yearly revenue data (last 3 years)
try {
    $stmt = $pdo->query("SELECT YEAR(created_at) as year, SUM(amount) as revenue 
                         FROM wallet_history 
                         WHERE type = 'credit' AND status = 'approved' 
                         AND created_at >= DATE_SUB(NOW(), INTERVAL 3 YEAR) 
                         GROUP BY YEAR(created_at) 
                         ORDER BY year ASC");
    $yearly_revenue = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $yearly_revenue = [];
}

// Prepare data for charts
// Ensure we have data for all days in the last 7 days
$daily_labels = [];
$daily_data = [];
$dates = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dates[$date] = 0;
}

foreach ($daily_revenue as $row) {
    $dates[$row['date']] = $row['revenue'];
}

foreach ($dates as $date => $revenue) {
    $daily_labels[] = date('d M', strtotime($date));
    $daily_data[] = $revenue;
}

// Prepare monthly data
$monthly_labels = [];
$monthly_data = [];
foreach ($monthly_revenue as $row) {
    $monthly_labels[] = $row['month'];
    $monthly_data[] = $row['revenue'];
}

// Prepare yearly data
$yearly_labels = [];
$yearly_data = [];
foreach ($yearly_revenue as $row) {
    $yearly_labels[] = $row['year'];
    $yearly_data[] = $row['revenue'];
}

// Calculate total revenue
$total_revenue = array_sum($daily_data) + array_sum($monthly_data) + array_sum($yearly_data);

// Debug data (remove this in production)
/*
echo "<!-- DEBUG DATA:
Daily Labels: " . json_encode($daily_labels) . "
Daily Data: " . json_encode($daily_data) . "
Monthly Labels: " . json_encode($monthly_labels) . "
Monthly Data: " . json_encode($monthly_data) . "
Yearly Labels: " . json_encode($yearly_labels) . "
Yearly Data: " . json_encode($yearly_data) . "
-->";
*/
?>

<div class="container-fluid">
    <h2 class="mb-4">Admin Dashboard</h2>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 col-sm-12 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="number">₹<?php echo number_format($total_wallet_balance, 2); ?></div>
                            <div class="label">Total Wallet Balance</div>
                        </div>
                        <i class="bi bi-wallet fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-12 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="number"><?php echo $total_users; ?></div>
                            <div class="label">Total Users</div>
                        </div>
                        <i class="bi bi-people fs-1"></i>
                    </div>
                    <div class="mt-2 text-center">
                        <a href="all_users.php" class="btn btn-sm btn-light">View All Users</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-12 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="number"><?php echo $total_categories; ?></div>
                            <div class="label">Categories</div>
                        </div>
                        <i class="bi bi-tags fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-12 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="number"><?php echo $total_offers; ?></div>
                            <div class="label">Total Offers</div>
                        </div>
                        <i class="bi bi-gift fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add a new row for pending messages -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="number"><?php echo $pending_messages_count; ?></div>
                            <div class="label">Pending Messages</div>
                        </div>
                        <i class="bi bi-envelope fs-1"></i>
                        <a href="contact_messages.php" class="btn btn-light">View Messages</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Revenue Charts -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Revenue Overview</h5>
                    <div class="card-header-actions">
                        <span class="badge bg-primary">Total Revenue: ₹<?php echo number_format($total_revenue, 2); ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="revenueTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily" type="button" role="tab">Daily (Last 7 Days)</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly" type="button" role="tab">Monthly (Last 6 Months)</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="yearly-tab" data-bs-toggle="tab" data-bs-target="#yearly" type="button" role="tab">Yearly (Last 3 Years)</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="revenueTabsContent">
                        <div class="tab-pane fade show active" id="daily" role="tabpanel">
                            <canvas id="dailyChart" height="100"></canvas>
                        </div>
                        <div class="tab-pane fade" id="monthly" role="tabpanel">
                            <canvas id="monthlyChart" height="100"></canvas>
                        </div>
                        <div class="tab-pane fade" id="yearly" role="tabpanel">
                            <canvas id="yearlyChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Pending Withdraw Requests -->
        <div class="col-lg-12 col-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Pending Withdraw Requests</h5>
                    <a href="pending_withdraw_requests.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($pending_withdraw_requests)): ?>
                        <p>No pending withdraw requests.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>Email</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>Details</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pending_withdraw_requests as $request): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($request['user_id']); ?></td>
                                            <td><?php echo htmlspecialchars($request['email']); ?></td>
                                            <td>₹<?php echo number_format($request['amount'], 2); ?></td>
                                            <td>
                                                <?php if (strpos($request['upi_id'], 'purchase@') === 0): ?>
                                                    <span class="badge bg-success">Purchase Request</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary">Withdrawal</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($request['offer_title'])): ?>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($request['offer_title']); ?></strong><br>
                                                        <div class="text-truncate-slider" style="max-width: 200px; overflow: hidden; position: relative;">
                                                            <div class="slider-text" style="white-space: nowrap; display: inline-block;">
                                                                <?php echo htmlspecialchars($request['offer_description']); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="text-truncate-slider" style="max-width: 200px; overflow: hidden; position: relative;">
                                                        <div class="slider-text" style="white-space: nowrap; display: inline-block;">
                                                            <?php echo htmlspecialchars($request['upi_id']); ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="approve_withdraw.php?id=<?php echo $request['id']; ?>&action=approve" 
                                                       class="btn btn-success">Approve</a>
                                                    <a href="approve_withdraw.php?id=<?php echo $request['id']; ?>&action=reject" 
                                                       class="btn btn-danger">Reject</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Function to initialize charts after DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Daily Revenue Chart
    try {
        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
        const dailyChart = new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($daily_labels); ?>,
                datasets: [{
                    label: 'Daily Revenue (₹)',
                    data: <?php echo json_encode($daily_data); ?>,
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#4361ee',
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ₹' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    } catch (e) {
        console.error('Error initializing daily chart:', e);
    }

    // Monthly Revenue Chart
    try {
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyChart = new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($monthly_labels); ?>,
                datasets: [{
                    label: 'Monthly Revenue (₹)',
                    data: <?php echo json_encode($monthly_data); ?>,
                    backgroundColor: 'rgba(67, 97, 238, 0.7)',
                    borderColor: '#4361ee',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#4361ee',
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ₹' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    } catch (e) {
        console.error('Error initializing monthly chart:', e);
    }

    // Yearly Revenue Chart
    try {
        const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
        const yearlyChart = new Chart(yearlyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($yearly_labels); ?>,
                datasets: [{
                    label: 'Yearly Revenue (₹)',
                    data: <?php echo json_encode($yearly_data); ?>,
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#4361ee',
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ₹' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    } catch (e) {
        console.error('Error initializing yearly chart:', e);
    }
});
</script>

<?php include 'includes/admin_footer.php'; ?>