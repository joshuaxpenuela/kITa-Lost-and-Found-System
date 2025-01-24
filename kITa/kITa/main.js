/* Adding Image For Updating or Adding New admin */
function previewImage(input) {
            const previewImg = document.getElementById('preview-image');
            const imageInfo = document.getElementById('image-info');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.classList.add('upload-success');
                    
                    // Show file information
                    const fileSize = (file.size / (1024 * 1024)).toFixed(2);
                    imageInfo.innerHTML = `Selected: ${file.name} (${fileSize} MB)`;
                    
                    // Remove animation class after it completes
                    setTimeout(() => {
                        previewImg.classList.remove('upload-success');
                    }, 1500);
                }
                
                reader.readAsDataURL(file);
            }
        }

        // Add hover effect for touch devices
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.profile-image-container');
            
            container.addEventListener('touchstart', function() {
                this.classList.add('hover');
            });
            
            container.addEventListener('touchend', function() {
                this.classList.remove('hover');
            });
        });

/* Dropdown Items */
document.addEventListener("DOMContentLoaded", function() {
        const links = document.querySelectorAll('.nav-link, .dropdown-item');
        const currentPath = window.location.pathname.split("/").pop();

        links.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    });

/* College Management */
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const modal = document.getElementById('collegeManagementModal');
    const manageCollegesLink = document.getElementById('manageCollegesLink');
    const closeModal = document.querySelector('.close-modal');
    const addCollegeForm = document.getElementById('addCollegeForm');
    const collegesList = document.getElementById('collegesList');
    const collegeCount = document.getElementById('collegeCount');
    const newCollegeName = document.getElementById('newCollegeName');
    const searchCollegeInput = document.getElementById('searchCollege');

    // Create notification container if it doesn't exist
    let notificationContainer = document.getElementById('notificationContainer');
    if (!notificationContainer) {
        notificationContainer = document.createElement('div');
        notificationContainer.id = 'notificationContainer';
        notificationContainer.style.position = 'fixed';
        notificationContainer.style.top = '20px';
        notificationContainer.style.left = '50%';
        notificationContainer.style.transform = 'translateX(-50%)';
        notificationContainer.style.zIndex = '9999';
        document.body.appendChild(notificationContainer);
    }

    // Store colleges data globally for searching/filtering
    let collegesData = [];

    // Modal Management
    function initializeModal() {
        // Open modal
        manageCollegesLink.addEventListener('click', function(e) {
            e.preventDefault();
            modal.style.display = 'flex';
            loadColleges();
            newCollegeName.focus();
        });

        // Close modal
        closeModal.addEventListener('click', function() {
            modal.style.display = 'none';
            resetForm();
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
                resetForm();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.style.display === 'flex') {
                modal.style.display = 'none';
                resetForm();
            }
        });
    }

    // Form Management
    function initializeForm() {
        // Add input validation for college name
        newCollegeName.addEventListener('input', function(e) {
            const value = e.target.value;
            if (!/^[a-zA-Z\s]*$/.test(value)) {
                e.target.value = value.replace(/[^a-zA-Z\s]/g, '');
                showNotification('Only letters and spaces are allowed', 'error');
            }
        });

        // Handle form submission
        addCollegeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const collegeName = newCollegeName.value.trim();

            if (collegeName.length < 2) {
                showNotification('College name must be at least 2 characters long', 'error');
                return;
            }

            submitCollege(collegeName);
        });
    }

    // Search Functionality
    function initializeSearch() {
        if (searchCollegeInput) {
            searchCollegeInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                filterColleges(searchTerm);
            });
        }
    }

    function filterColleges(searchTerm) {
        const filteredColleges = collegesData.filter(college => 
            college.college.toLowerCase().includes(searchTerm)
        );
        renderColleges(filteredColleges);
    }

    // College Management Functions
    function submitCollege(collegeName) {
        fetch('manage_colleges.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=add&college_name=${encodeURIComponent(collegeName)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                resetForm();
                loadColleges();
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to add college', 'error');
        });
    }

    function loadColleges() {
        fetch('manage_colleges.php')
        .then(response => response.json())
        .then(colleges => {
            collegesData = colleges; // Store colleges data globally
            renderColleges(colleges);
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to load colleges', 'error');
        });
    }

    function renderColleges(colleges) {
        collegesList.innerHTML = '';
        let count = 0;

        colleges.forEach(college => {
            if (college.college !== 'Select College') {
                count++;
                const row = document.createElement('tr');
                const statusBadgeClass = college.status === 'enabled' ? 'badge bg-success' : 'badge bg-danger';
                const buttonClass = college.status === 'enabled' ? 'btn-danger' : 'btn-success';
                const buttonText = college.status === 'enabled' ? 'Disable' : 'Enable';
                const buttonIcon = college.status === 'enabled' ? 'fas fa-times-circle' : 'fa-check';
                
                row.innerHTML = `
                    <td class="px-3 align-middle">${college.college}</td>
                    <td class="px-3 text-center align-middle">
                        <span class="${statusBadgeClass}">${college.status}</span>
                    </td>
                    <td class="px-3 text-end align-middle">
                        <button class="btn ${buttonClass} btn-sm toggle-status-btn" 
                                data-id="${college.college_id}" 
                                data-status="${college.status}">
                            <i class="fas ${buttonIcon}"></i> ${buttonText}
                        </button>
                    </td>
                `;
                collegesList.appendChild(row);
            }
        });

        collegeCount.textContent = `${count} college${count !== 1 ? 's' : ''}`;
        attachToggleStatusListeners();
    }

    function attachToggleStatusListeners() {
        document.querySelectorAll('.toggle-status-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const collegeId = this.dataset.id;
                const currentStatus = this.dataset.status;
                const action = currentStatus === 'enabled' ? 'disable' : 'enable';
                
                if (confirm(`Are you sure you want to ${action} this college?`)) {
                    toggleCollegeStatus(collegeId, currentStatus);
                }
            });
        });
    }

    function toggleCollegeStatus(collegeId, currentStatus) {
        fetch('manage_colleges.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=toggle_status&college_id=${collegeId}&status=${currentStatus}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                loadColleges();
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to update college status', 'error');
        });
    }

    // Utility Functions
    function resetForm() {
        addCollegeForm.reset();
        if (searchCollegeInput) {
            searchCollegeInput.value = '';
        }
    }

    function showNotification(message, type) {
        const notificationDiv = document.createElement('div');
        
        notificationDiv.className = `notification alert ${type === 'success' ? 'alert-success' : 'alert-danger'}`;

        Object.assign(notificationDiv.style, {
            padding: '1rem 2rem',
            borderRadius: '4px',
            marginBottom: '10px',
            textAlign: 'center',
            minWidth: '300px',
            maxWidth: '600px',
            animation: 'slideDown 0.5s ease-out',
            boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center'
        });

        notificationDiv.textContent = message;
        notificationContainer.appendChild(notificationDiv);

        setTimeout(() => {
            notificationDiv.style.animation = 'slideUp 0.5s ease-out';
            setTimeout(() => {
                notificationDiv.remove();
            }, 450);
        }, 3000);
    }

    // Add CSS animations
    function addAnimationStyles() {
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideDown {
                from {
                    transform: translateY(-100%);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            @keyframes slideUp {
                from {
                    transform: translateY(0);
                    opacity: 1;
                }
                to {
                    transform: translateY(-100%);
                    opacity: 0;
                }
            }

            .notification {
                position: relative;
                margin-bottom: 10px;
            }

            .toggle-status-btn {
                transition: all 0.3s ease;
            }

            .toggle-status-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
        `;
        document.head.appendChild(style);
    }

    // Initialize everything
    function initialize() {
        initializeModal();
        initializeForm();
        initializeSearch();
        addAnimationStyles();
        loadColleges();
    }

    // Start the application
    initialize();
});

// Logout Function
document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const logoutLink = document.getElementById('logoutLink');
    const logoutModal = document.getElementById('logoutModal');
    const closeBtn = logoutModal.querySelector('.close-modal-1');
    const cancelBtn = logoutModal.querySelector('.cancel-logout');
    const confirmBtn = logoutModal.querySelector('.confirm-logout');

    // Show modal
    function showModal() {
        logoutModal.classList.add('active');
    }

    // Hide modal
    function hideModal() {
        logoutModal.classList.remove('active');
    }

    // Event listeners
    logoutLink.addEventListener('click', function(e) {
        e.preventDefault();
        showModal();
    });

    closeBtn.addEventListener('click', hideModal);
    cancelBtn.addEventListener('click', hideModal);

    // Close modal when clicking outside
    logoutModal.addEventListener('click', function(e) {
        if (e.target === logoutModal) {
            hideModal();
        }
    });

    // Handle logout confirmation
    confirmBtn.addEventListener('click', function() {
        // Show loading state
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Logging out...';

        // Perform logout
        fetch('logout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'confirm_logout=1'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            }
        })
        .catch(error => {
            console.error('Logout error:', error);
            // Reset button state
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = 'Yes, Logout';
            // Show error message if needed
            alert('An error occurred during logout. Please try again.');
        });
    });
});
// Notification 
document.addEventListener('DOMContentLoaded', function () {
    // Store notification states globally
    let notificationStates = {
        messages: false,
        unclaimed: false,
        lastFetchTime: Date.now()
    };

    // Configuration for notification elements
    const notificationConfig = [
        {
            selector: '.dropdown-item i.fas.fa-envelope',
            type: 'messages',
            parentSelector: '.dropdown-item',
            targetPath: 'message.php',
            updatePath: 'update_notification_status.php'
        },
        {
            selector: '.nav-link i.fa-solid.fa-inbox',
            type: 'unclaimed',
            parentSelector: '.nav-link',
            targetPath: 'manage_reports.php',
            updatePath: 'update_notification_status.php'
        }
    ];

    // Function to check if current page matches notification target
    function isOnNotificationPage(targetPath) {
        const currentPath = window.location.pathname;
        return currentPath.endsWith(targetPath);
    }

    // Function to update notification badges
    function updateNotificationBadges(data) {
        // Update timestamp of last fetch
        notificationStates.lastFetchTime = Date.now();

        // Update notification states with new data
        notificationStates = { ...notificationStates, ...data };

        notificationConfig.forEach(({ selector, type, targetPath }) => {
            const icon = document.querySelector(selector);
            if (!icon) return;

            const element = icon.closest(selector.split(' ')[0]);
            if (!element) return;

            // Only show badge if there are new notifications and we are not on the target page
            const shouldShowBadge = data[type] && !isOnNotificationPage(targetPath);
            if (shouldShowBadge) {
                updateBadge(element, true, type);
            }
        });
    }

    // Function to update a single notification badge
    function updateBadge(element, hasNew, type) {
        // Always remove any existing badge before potentially adding a new one
        removeExistingBadge(element);

        // If there are new notifications, add the badge
        if (hasNew) {
            const badge = document.createElement('span');
            badge.className = 'badge bg-danger rounded-pill ms-2 notification-badge notification-badge-animate';
            badge.textContent = 'New';
            badge.setAttribute('data-type', type);
            element.appendChild(badge);
        }
    }

    // Function to remove existing badge
    function removeExistingBadge(element) {
        const existingBadge = element.querySelector('.notification-badge');
        if (existingBadge) {
            existingBadge.remove();
        }
    }

    // Function to fetch notifications from the server
    function fetchNotifications(force = false) {
        // Add timestamp to prevent caching
        const timestamp = Date.now();
        fetch('get_notifications.php?t=' + timestamp)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                updateNotificationBadges(data);
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
            });
    }

    // Function to handle notification click
    function handleNotificationClick(config, element) {
        const { type, updatePath } = config;

        // Check if there is an existing badge
        const existingBadge = element.querySelector('.notification-badge');
        if (!existingBadge) return;

        // Remove the badge immediately
        removeExistingBadge(element);
        
        // Update the notification state
        notificationStates[type] = false;

        // Mark the notification as read on the server
        const formData = new FormData();
        formData.append('type', type);

        fetch(updatePath, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Failed to update notification status');
                }
            })
            .catch(error => {
                console.error('Error updating notification status:', error);
            });
    }

    // Set up click event listeners for each notification
    notificationConfig.forEach((config) => {
        const elements = document.querySelectorAll(config.selector);
        elements.forEach(icon => {
            const element = icon.closest(config.parentSelector);
            if (element) {
                element.addEventListener('click', function (e) {
                    handleNotificationClick(config, this);
                });
            }
        });
    });

    // Add CSS for badge animations
    const style = document.createElement('style');
    style.textContent = `
        .notification-badge {
            opacity: 1;
            transform: scale(1);
            transition: opacity 0.2s ease-out;
            position: relative;
            display: inline-block;
        }

        .notification-badge-animate {
            animation: notificationPop 0.3s ease-in-out;
        }

        @keyframes notificationPop {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.8;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }`;
    document.head.appendChild(style);

    // Initial fetch to load current notifications
    fetchNotifications();

    // Periodic updates (every 15 seconds) to check for new notifications
    setInterval(() => {
        fetchNotifications();
    }, 15000);
});

// Messaging Functionality
$(document).ready(function() {
            let currentUserId = null;

            $('.user-select').click(function() {
                $('.user-select').removeClass('active');
                $(this).addClass('active');
                currentUserId = $(this).data('user-id');
                loadMessages(currentUserId);
            });

            $('#messageForm').submit(function(e) {
                e.preventDefault();
                if (currentUserId) {
                    const message = $('#messageInput').val();
                    if (message.trim() !== '') {
                        $.post('send_message.php', {
                            user_id: currentUserId,
                            message: message
                        }, function(response) {
                            $('#messageInput').val('');
                            loadMessages(currentUserId);
                        });
                    }
                }
            });

            function loadMessages(userId) {
                $.get('get_messages.php', { user_id: userId }, function(data) {
                    $('#messageContainer').html(data);
                    $('#messageContainer').scrollTop($('#messageContainer')[0].scrollHeight);
                });
            }

            // Periodically refresh messages
            setInterval(function() {
                if (currentUserId) {
                    loadMessages(currentUserId);
                }
            }, 5000);
        });

        // Scroll to bottom of message container
        function scrollToBottom() {
            var messageContainer = document.querySelector('.message-container');
            messageContainer.scrollTop = messageContainer.scrollHeight;
        }

        // Call scrollToBottom when page loads and after sending a message
        window.onload = scrollToBottom;
        document.querySelector('#messageForm').addEventListener('submit', function() {
            setTimeout(scrollToBottom, 100);
        });
