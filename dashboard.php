<?php
include 'db.php';

$sqlTotal = "SELECT COUNT(*) as total FROM milestones";
$resultTotal = mysqli_query($conn, $sqlTotal);
$totalMilestones = mysqli_fetch_assoc($resultTotal)['total'];

$sqlCompleted = "SELECT COUNT(*) as completed FROM milestones WHERE status = 'Achieved'";
$resultCompleted = mysqli_query($conn, $sqlCompleted);
$completedTasks = mysqli_fetch_assoc($resultCompleted)['completed'];

$sqlInProgress = "SELECT COUNT(*) as inprogress FROM milestones WHERE status = 'In Progress'";
$resultInProgress = mysqli_query($conn, $sqlInProgress);
$inProgressTasks = mysqli_fetch_assoc($resultInProgress)['inprogress'];

$sqlNotStarted = "SELECT COUNT(*) as notstarted FROM milestones WHERE status = 'Not Started'";
$resultNotStarted = mysqli_query($conn, $sqlNotStarted);
$notStartedTasks = mysqli_fetch_assoc($resultNotStarted)['notstarted'];

$sqlUpcoming = "SELECT COUNT(*) as upcoming FROM milestones WHERE dueDate >= CURDATE() AND status IN ('Not Started', 'In Progress')";
$resultUpcoming = mysqli_query($conn, $sqlUpcoming);
$upcomingTasks = mysqli_fetch_assoc($resultUpcoming)['upcoming'];

$sqlOpenMilestones = "
    SELECT m.milestoneID, p.projectName, m.taskName, m.milestoneName, m.dueDate, m.status
    FROM milestones m
    JOIN projects p ON m.projectID = p.projectID
    WHERE m.status != 'Achived'
    ORDER BY m.dueDate ASC
";

$openMilestonesResult = mysqli_query($conn, $sqlOpenMilestones);

if (!$openMilestonesResult) {
    die("Query error: " . mysqli_error($conn));
}
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
                 <!-- Dashboard Summary Cards -->
                 <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow text-center flex flex-col items-center space-y-2">
                       <i class="fa-solid fa-tasks text-4xl text-[#4E3B2A]"></i>
                       <p class="text-gray-600">Total Milestones</p>
                       <h2 class="text-3xl font-bold text-[#4E3B2A]"><?php echo $totalMilestones; ?></h2>
                    </div>
                <div class="bg-white p-6 rounded-lg shadow text-center flex flex-col items-center space-y-2">

                    <i class="fa-solid fa-check-circle text-4xl text-[#4E3B2A]"></i>
                    <p class="text-gray-600">Completed Tasks</p>
                    <h2 class="text-3xl font-bold text-[#4E3B2A]"><?php echo $completedTasks; ?></h2>
                </div>
                <div class="bg-white p-6 rounded-lg shadow text-center flex flex-col items-center space-y-2">
                    <i class="fa-solid fa-hourglass-half text-4xl text-[#4E3B2A]"></i>
                    <p class="text-gray-600">Upcoming Deadlines</p>
                    <h2 class="text-3xl font-bold text-[#4E3B2A]"><?php echo $upcomingTasks; ?></h2>
                </div>
                <div class="bg-white p-6 rounded-lg shadow text-center flex flex-col items-center space-y-2">
                    <i class="fa-solid fa-pause-circle text-4xl text-[#4E3B2A]"></i>
                    <p class="text-gray-600">Not Started</p>
                    <h2 class="text-3xl font-bold text-[#4E3B2A]"><?php echo $notStartedTasks; ?></h2>
                </div>
                <div class="bg-white p-6 rounded-lg shadow text-center flex flex-col items-center space-y-2">
                   <i class="fa-solid fa-exclamation-triangle text-4xl text-[#4E3B2A]"></i>
                   <p class="text-gray-600">In Progress</p>
                   <h2 class="text-3xl font-bold text-[#4E3B2A]"><?php echo $inProgressTasks; ?></h2>
                </div>
            </div>

    <!-- Open Milestones Table -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
      <h3 class="text-xl font-bold mb-4">Open Milestones</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
          <thead class="bg-[#F7E6CA] text-[#4E3B2A]">
            <tr>
              <th class="px-4 py-3">Milestone ID</th>
              <th class="px-4 py-3">Project</th>
              <th class="px-4 py-3">Task</th>
              <th class="px-4 py-3">Milestone</th>
              <th class="px-4 py-3">Due Date</th>
              <th class="px-4 py-3">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = mysqli_fetch_assoc($openMilestonesResult)): ?>
               <tr class="border-b hover:bg-gray-50">
               <td class="px-4 py-2"><?= $row['milestoneID'] ?></td>
               <td class="px-4 py-2"><?= $row['projectName'] ?></td>
               <td class="px-4 py-2"><?= $row['taskName'] ?></td>
               <td class="px-4 py-2"><?= $row['milestoneName'] ?></td>
               <td class="px-4 py-2"><?= $row['dueDate'] ?></td>
               <td class="px-4 py-2">

                <?php

                $status = $row['status'];
                $badgeClass = match($status) {
                    'Not Started' => 'bg-gray-200 text-gray-800',
                    'In Progress' => 'bg-yellow-200 text-yellow-800',
                    'Achived' => 'bg-green-200 text-green-800',
                    default => 'bg-gray-200 text-gray-800'
                };
                ?>
      <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badgeClass ?>">
        <?= $status ?>
      </span>
    </td>
  </tr>
  <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

                
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
