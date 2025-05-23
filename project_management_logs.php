<?php
include('db.php');
$sql = "
    SELECT 'Project' AS category, projectName AS name, 'created' AS action, createdAt AS timestamp
    FROM projects
    UNION ALL
    SELECT 'Project' AS category, projectName AS name, 'updated' AS action, updatedAt AS timestamp
    FROM projects
    WHERE updatedAt != createdAt

    UNION ALL
    SELECT 'Task' AS category, taskName AS name, 'created' AS action, createdAt AS timestamp
    FROM projecttask
    UNION ALL
    SELECT 'Task' AS category, taskName AS name, 'updated' AS action, updatedAt AS timestamp
    FROM projecttask
    WHERE updatedAt != createdAt

    UNION ALL
    SELECT 'Timesheet' AS category, taskName AS name, 'created' AS action, createdAt AS timestamp
    FROM timesheets
    UNION ALL
    SELECT 'Timesheet' AS category, taskName AS name, 'updated' AS action, updatedAt AS timestamp
    FROM timesheets
    WHERE updatedAt != createdAt

    UNION ALL
    SELECT 'Milestone' AS category, milestoneName AS name, 'created' AS action, createdAt AS timestamp
    FROM milestones
    UNION ALL
    SELECT 'Milestone' AS category, milestoneName AS name, 'updated' AS action, updatedAt AS timestamp
    FROM milestones
    WHERE updatedAt != createdAt

    ORDER BY timestamp DESC
    LIMIT 15
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Your existing sidebar styles */
        .sidebar-collapsed {
            width: 85px;
        }
        .sidebar-expanded {
            width: 320px;
        }

        .sidebar-collapsed .menu-name span,
        .sidebar-collapsed .menu-name .arrow {
            display: none;
        }

        .sidebar-collapsed .menu-name i {
            margin-right: 0;
        }

        .sidebar-collapsed .menu-drop {
            display: none;
        }

        .sidebar-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            position: fixed;
            inset: 0;
            z-index: 40;
            display: none;
        }

        .sidebar-overlay.active {
            display: block;
        }
        .close-sidebar-btn {
            display: none;
        }

        @media (max-width: 968px) {
            .sidebar {
                position: fixed;
                left: -100%;
                transition: left 0.3s ease-in-out;
            }

            .sidebar.mobile-active {
                left: 0;
            }

            .main {
                margin-left: 0 !important;
            }

            .close-sidebar-btn {
                display: block;
            }
        }

        .menu-name {
            position: relative;
            overflow: hidden;
        }

        .menu-name::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 2px;
            width: 0;
            background-color: #4E3B2A;
            transition: width 0.3s ease;
        }

        .menu-name:hover::after {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="flex min-h-screen w-full">
        <!-- Overlay -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Sidebar -->
        <div class="sidebar sidebar-expanded fixed z-50 overflow-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col">
            <div class="h-16 border-b border-[#F7E6CA] flex items-center px-2 space-x-2">
                <h1 class="text-xl font-bold text-black p-2 rounded-xl"><img src="Logo\PNG\Logo.png" alt="Logo" class="h-12 w-auto "></h1>
                <h1 class="text-xl font-bold text-[#4E3B2A]"><img src="Logo\PNG\Logo-Name.png" alt="Logo" class="h-12 w-auto  "></h1>
                <!--Close Button-->
                <i id="close-sidebar-btn" class="fa-solid fa-x close-sidebar-btn transform translate-x-20 font-bold text-xl"></i>
            </div>
            <div class="side-menu px-4 py-6">
                <ul class="space-y-4">
                    <!-- Dashboard Item -->
                    <div class="menu-option">
                        <a href="dashboard.php" class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-house text-lg pr-4"></i>
                                <span class="text-sm font-medium">Dashboard</span>
                            </div>
                        </a>
                    </div>
                    <!-- Project Management -->
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('project-management-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-project-diagram text-lg pr-4"></i>
                                <span class="text-sm font-medium ">Project Management</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="project-management-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-5 space-y-3 mt-3">
                            <ul class="space-y-2">
                                <li><a href="projects.php" class="flex items-center gap-3 text-sm text-gray-800 hover:text-blue-600"><i class="fas fa-briefcase text-3xl"></i> Projects</a></li>
                                <li><a href="project_task.php" class="flex items-center gap-3 text-sm text-gray-800 hover:text-blue-600"><i class="fas fa-tasks text-3xl"></i> Tasks</a></li>
                                <li><a href="milestones.php" class="flex items-center gap-3 text-sm text-gray-800 hover:text-blue-600"><i class="fas fa-flag-checkered text-3xl"></i> Milestones</a></li>
                                <li><a href="timesheets.php" class="flex items-center gap-3 text-sm text-gray-800 hover:text-blue-600"><i class="fas fa-clock text-3xl"></i> Timesheets</a></li>
                                <li><a href="project_management_logs.php" class="flex items-center gap-3 text-sm text-gray-800 hover:text-blue-600"><i class="fas fa-history text-3xl"></i> Logs</a></li>
                            </ul>
                        </div>
                    </div>
                </ul>
            </div>
        </div>

        <!-- Main + Navbar -->
        <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
            <!-- Navbar -->
            <nav class="h-16 w-full bg-white border-b border-[#F7E6CA] flex justify-between items-center px-6 py-4">
                <div class="left-nav flex items-center space-x-4 max-w-96 w-full">
                </div>
                <div>
                   <i class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg cursor-pointer text-lg lg:hidden" aria-label="User profile"></i>
                </div>
                <!-- Right Navigation Section -->
                <div class="right-nav  items-center space-x-6 hidden lg:flex">
                    <button aria-label="Notifications" class="text-[#4E3B2A] focus:outline-none border-r border-[#F7E6CA] pr-6 relative">
                        <i class="fa-regular fa-bell text-xl"></i>
                        <span class="absolute top-0.5 right-5 block w-2.5 h-2.5 bg-[#594423] rounded-full"></span>
                    </button>

                    <div class="flex items-center space-x-2">
                        <i class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg text-lg" aria-label="User profile"></i>
                        <div class="info flex flex-col py-2">
                            <h1 class="text-[#4E3B2A] font-semibold font-serif text-sm">Madelyn Cline</h1>
                            <p class="text-[#594423] text-sm pl-2">Administrator</p>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="px-8 py-8">
                <!-- Add Content Here -->
                 <h3 class="mb-6 text-2xl font-semibold text-blue-700">Project Management Logs</h3>
                 <table class="min-w-full table-auto border-collapse shadow-md rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-white hover:bg-blue-50">
                            <th class="border px-5 py-3">Category</th>
                            <th class="border px-5 py-3">Name</th>
                            <th class="border px-5 py-3">Action</th>
                            <th class="border px-5 py-3">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="border px-5 py-3 text-center font-semibold"><?php echo htmlspecialchars($row['category']); ?></td>
                                    <td class="border px-5 py-3 text-center font-semibold"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td class="border px-5 py-3 text-center">
                                <?php
                                $action = strtolower($row['action']);
                                $badgeClass = 'bg-gray-400 text-gray-800';
                                if ($action === 'created') $badgeClass = 'bg-green-400 text-green-900';
                                else if ($action === 'updated') $badgeClass = 'bg-yellow-300 text-yellow-900';
                                echo '<span class="px-3 py-1 rounded-full text-sm font-semibold ' . $badgeClass . '">' . ucfirst($row['action']) . '</span>';
                                ?>
                                </td>
                                <td class="border px-5 py-3 text-center"><?php echo htmlspecialchars($row['timestamp']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="border px-5 py-4 text-center italic text-gray-500">No logs found.</td>
                            </tr>
                            <?php endif; ?>
                    </tbody>
                 </table>
            </main>
        </div>
    </div>
    

    <script>
        // Sidebar and dropdown toggle functionality
        const menu = document.querySelector('.menu-btn');
        const sidebar = document.querySelector('.sidebar');
        const main = document.querySelector('.main');
        const overlay = document.getElementById('sidebar-overlay');
        const close = document.getElementById('close-sidebar-btn');

        function closeSidebar() {
            sidebar.classList.remove('mobile-active');
            overlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function openSidebar() {
            sidebar.classList.add('mobile-active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function toggleSidebar() {
            if (window.innerWidth <= 968) {
                sidebar.classList.add('sidebar-expanded'); 
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.contains('mobile-active') ? closeSidebar() : openSidebar();
            } else {
                sidebar.classList.toggle('sidebar-collapsed');
                sidebar.classList.toggle('sidebar-expanded');
                main.classList.toggle('md:ml-[85px]');
                main.classList.toggle('md:ml-[360px]');
            }
        }

        menu.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', closeSidebar);
        close.addEventListener('click', closeSidebar);

        window.addEventListener('resize', () => {
            if (window.innerWidth > 968) {
                closeSidebar();
                sidebar.classList.remove('mobile-active');
                overlay.classList.remove('active');
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded'); 
            } else {
                sidebar.classList.add('sidebar-expanded'); 
                sidebar.classList.remove('sidebar-collapsed');
            }
        });

         function toggleDropdown(dropdownId, element) {
            const dropdown = document.getElementById(dropdownId);
            const icon = element.querySelector('.arrow-icon');
            const allDropdowns = document.querySelectorAll('.menu-drop');
            const allIcons = document.querySelectorAll('.arrow-icon');

            allDropdowns.forEach(d => {
                if (d !== dropdown) d.classList.add('hidden');
            });

            allIcons.forEach(i => {
                if (i !== icon) {
                    i.classList.remove('bx-chevron-down');
                    i.classList.add('bx-chevron-right');
                }
            });

            dropdown.classList.toggle('hidden');
            icon.classList.toggle('bx-chevron-right');
            icon.classList.toggle('bx-chevron-down');
        }
    </script>
</body>
</html>
