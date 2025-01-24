<nav class="sidebar">
        <div class="text-center mb-4">
            <a href="admin_dashboard.php">
                <img src="images/new_kitalogo2.png" alt="Kita Logo" style="max-width: 140px; height: auto;">
            </a>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="admin_dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_lost_reports.php">
                    <i class="fa-solid fa-clipboard-list"></i> Lost Item Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_reports.php">
                    <i class="fa-solid fa-inbox"></i> Unclaimed Items
                </a>
            </li>
            <li class="nav-item">
                <a href="claim_requests.php" class="nav-link">
                    <i class="fas fa-hand-paper"></i>
                    <span>Claim Requests</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_claimed_items.php">
                    <i class="fa-solid fa-box-archive"></i> Claimed Items
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_users.php">
                    <i class="fa-solid fa-users"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile.php">
                    <i class="fas fa-user"></i> Profile
                </a>
            </li>
            <li>
                <a class="nav-link" href="message.php">
                    <i class="fas fa-envelope"></i> Messages
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <ul class="dropdown-menu" aria-labelledby="settingsDropdown">
                    <li>
                        <a class="dropdown-item" href="new_admin.php">
                            <i class="fas fa-user-plus"></i> Manage Users
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" id="manageCollegesLink">
                            <i class="fas fa-university"></i> Manage Colleges
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="about.php">
                            <i class="fas fa-info-circle"></i> About
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="logout.php" id="logoutLink">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="collegeManagementModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Manage Colleges</h5>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <!-- Add New College Form -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Add New College</h6>
                </div>
                <div class="card-body">
                    <form id="addCollegeForm">
                        <div class="input-group">
                            <input type="text" id="newCollegeName" class="form-control" placeholder="Enter college name" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Existing Colleges List -->
            <div class="card">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Existing Colleges</h6>
                    <span id="collegeCount" class="badge bg-light text-dark">0 colleges</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="px-3">College Name</th>
                                    <th class="px-3 text-center">Status</th>
                                    <th class="px-3 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="collegesList">
                                <!-- Colleges will be loaded here dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Logout Overlay Container -->
<div id="logoutModal" class="modal-overlay-1">
    <div class="modal-content-1">
        <div class="modal-header-1">
            <h5 class="modal-title-1">Confirm Logout</h5>
            <span class="close-modal-1">&times;</span>
        </div>
        <div class="modal-body-1">
            <div class="text-center mb-4">
                <h5>Are you sure you want to logout?</h5>
                <p class="text-muted">You will be redirected to the login page.</p>
            </div>
            <div class="d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-secondary cancel-logout">Cancel</button>
                <button type="button" class="btn btn-danger confirm-logout">Confirm</button>
            </div>
        </div>
    </div>
</div>
<script src="main.js"></script>

