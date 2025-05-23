<?php
include('db.php');

if (isset($_POST['create_team'])) {
    $projectID = $_POST['projectID'];
    $teamName = $_POST['teamName'];
    $leadID = $_POST['leadID'];
    $employeeName = $_POST['employeeName'];

    $sql = "INSERT INTO projectteam (projectID, teamName, leadID, employeeName, createdAt, updatedAt) 
            VALUES ('$projectID', '$teamName', '$leadID', '$employeeName', NOW(), NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Team Created Successfully',
                        text: 'Team was created successfully.',
                        confirmButtonText: 'OK'
                    });
                };
              </script>";
    } else {
        echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to create team. Try again!',
                        confirmButtonText: 'OK'
                    });
                };
              </script>";
    }
}

if (isset($_POST['update_team'])) {
    $teamID = $_POST['teamID'];
    $projectID = $_POST['projectID'];
    $teamName = $_POST['teamName'];
    $leadID = $_POST['leadID'];
    $employeeName = $_POST['employeeName'];

    $sql = "UPDATE projectteam 
            SET projectID='$projectID', teamName='$teamName', leadID='$leadID', 
                employeeName='$employeeName', updatedAt=NOW() 
            WHERE teamID='$teamID'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Team Updated Successfully',
                        text: 'Team was updated successfully.',
                        confirmButtonText: 'OK'
                    });
                };
              </script>";
    } else {
        echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update team. Try again!',
                        confirmButtonText: 'OK'
                    });
                };
              </script>";
    }
}

if (isset($_POST['delete_team'])) {
    $teamID = $_POST['teamID'];

    if (isset($teamID) && is_numeric($teamID)) {
        $sql = "DELETE FROM projectteam WHERE teamID='$teamID'";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    window.onload = function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Team Deleted Successfully',
                            text: 'Team was deleted successfully.',
                            confirmButtonText: 'OK'
                        });
                    };
                  </script>";
        } else {
            echo "<script>
                    window.onload = function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete team. Try again!',
                            confirmButtonText: 'OK'
                        });
                    };
                  </script>";
        }
    } else {
        echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid team ID',
                        text: 'Team ID is invalid or not set.',
                        confirmButtonText: 'OK'
                    });
                };
              </script>";
    }
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $safeSearch = $conn->real_escape_string($search);
    
    // ✅ Palitan ito ng mga column na existing sa projectteam table mo
    $teams = $conn->query("SELECT * FROM projectteam 
                           WHERE teamID LIKE '%$safeSearch%' 
                           OR teamName LIKE '%$safeSearch%' 
                           OR projectID LIKE '%$safeSearch%'");
} else {
    $teams = $conn->query("SELECT * FROM projectteam");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.2/dist/sweetalert2.all.min.js"></script>

    <style>
        .sidebar-collapsed { width: 85px; }
        .sidebar-expanded { width: 320px; }
        .sidebar-overlay { background-color: rgba(0, 0, 0, 0.5); position: fixed; inset: 0; z-index: 40; display: none; }
        .sidebar-overlay.active { display: block; }
        .menu-name::after { content: ''; position: absolute; left: 0; bottom: 0; height: 2px; width: 0; background-color: #4E3B2A; transition: width 0.3s ease; }
        .menu-name:hover::after { width: 100%; }
    </style>
</head>
<body>
    <div class="flex min-h-screen w-full">
        <!-- Sidebar -->
        <div class="sidebar sidebar-expanded fixed z-50 overflow-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col">
            <div class="h-16 border-b border-[#F7E6CA] flex items-center px-2 space-x-2">
                <h1 class="text-xl font-bold text-black p-2 rounded-xl"><img src="Logo\PNG\Logo.png" alt="Logo" class="h-12 w-auto"></h1>
                <h1 class="text-xl font-bold text-[#4E3B2A]"><img src="Logo\PNG\Logo-Name.png" alt="Logo" class="h-12 w-auto"></h1>
            </div>
            <div class="side-menu px-4 py-6">
                <ul class="space-y-4">
                    <div class="menu-option">
                        <a href="dashboard.php" class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg">
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
                                <span class="text-sm font-medium">Project Management</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="project-management-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-5 space-y-3 mt-3">
                            <ul class="space-y-2">
                                <li><a href="projects.php" class="flex items-center gap-3 text-sm text-gray-800 hover:text-blue-600"><i class="fas fa-briefcase text-3xl"></i> Projects</a></li>
                                <li><a href="project_task.php" class="flex items-center gap-3 text-sm text-gray-800 hover:text-blue-600"><i class="fas fa-tasks text-3xl"></i> Tasks</a></li>
                                <li><a href="project_team.php" class="flex items-center gap-3 text-sm text-gray-800 hover:text-blue-600"><i class="fas fa-users text-3xl"></i> Teams</a></li>
                                <li><a href="milestones.php" class="flex items-center gap-3 text-sm text-gray-800 hover:text-blue-600"><i class="fas fa-flag-checkered text-3xl"></i> Milestones</a></li>
                                <li><a href="timesheets.php" class="flex items-center gap-3 text-sm text-gray-800 hover:text-blue-600"><i class="fas fa-clock text-3xl"></i> Timesheets</a></li>
                                <li><a href="project_management_logs.php" class="flex items-center gap-3 text-sm text-gray-800 hover:text-blue-600"><i class="fas fa-history text-3xl"></i> Logs</a></li>
                            </ul>
                        </div>
                    </div>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
            <nav class="h-16 w-full bg-white border-b border-[#F7E6CA] flex justify-between items-center px-6 py-4">
                <div class="left-nav flex items-center space-x-4 max-w-96 w-full">
                    <button aria-label="Toggle menu" class="menu-btn text-[#4E3B2A] focus:outline-none hover:bg-[#F7E6CA] hover:rounded-full">
                        <i class="fa-solid fa-bars text-[#594423] text-xl w-11 py-2"></i>
                    </button>
                    <form method="GET" class="relative w-full flex pr-2">
                    <i class="fa-solid fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-[#4E3B2A] z-10"></i>
                        <input 
                        type="text",
                        name="search",
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>",
                        placeholder="Search something...",
                        arial-label="Search Input"class="bg-white h-10 w-full rounded-full pl-10 pr-4 shadow-sm border border-gray-300 focus:ring-2 focus:ring-[#F7E6CA] focus:outline-none text-sm"
                        />
                    </form>
                </div>
            </nav>
            <main class="px-8 py-8">
                <h2 class="text-xl font-bold mb-4">Create Team</h2>
                <form method="POST" class="mb-4">
                    <input type="number" name="projectID" placeholder="Project ID" class="p-2 rounded border border-gray-300" required>
                    <input type="text" name="teamName" placeholder="Team Name" class="p-2 rounded border border-gray-300" required>
                    <input type="text" name="leadID" placeholder="Lead ID" class="p-2 rounded border border-gray-300" required>
                    <input type="text" name="employeeName" placeholder="Employee Name" class="p-2 rounded border border-gray-300" required>
                     <button type="submit" name="create_team" class="bg-[#4E3B2A] text-white px-4 py-2 rounded hover:bg-[#594423]">Create Team</button>
                </form>
                <h2 class="text-xl font-bold mb-4">Team List</h2>

                <table class="min-w-full table-auto border-collapse">

                    <thead>
                       <tr class="bg-[#F7E6CA]">
                       <th class="border px-4 py-2">project ID</th>
                       <th class="border px-4 py-2">Team Name</th>
                       <th class="border px-4 py-2">Lead ID</th>
                       <th class="border px-4 py-2">Employee Name</th>
                       <th class="border px-4 py-2">Actions</th>
                       </tr>
                     </thead>
                     <tbody>
                        <?php while ($row = $teams->fetch_assoc()): ?>
                            <tr>
                                <form method="POST">
                                    <input type="hidden" name="teamID" value="<?php echo $row['teamID']; ?>">
                                    <td class="border px-4 py-2">
                                        <input type="text" name="projectID" value="<?php echo $row['projectID']; ?>" class="editable" disabled>
                                    </td>
                                    <td class="border px-4 py-2">
                                        <input type="text" name="teamName" value="<?php echo $row['teamName']; ?>" class="editable" disabled>
                                    </td>
                                    <td class="border px-4 py-2">
                                        <input type="text" name="leadID" value="<?php echo $row['leadID']; ?>" class="editable" disabled>
                                    </td>
                                    <td class="border px-4 py-2">
                                        <input type="text" name="employeeName" value="<?php echo $row['employeeName']; ?>" class="editable" disabled>
                                    </td>
                                   <td class="border px-4 py-2">
                                         <button type="button" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 edit-btn" onclick="toggleEdit(this)">Edit</button>
                                         <button type="submit" name="update_team" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 save-btn" style="display:none;">Save</button>
                                         <button type="submit" name="delete_team" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600" onclick="return confirm('Delete this team?')">Delete</button>
                                    </td>
                                </form>
                            </tr>
                        <?php endwhile; ?>
                     </tbody>
                </table>
            </main>
        </div>
    </div>

    <script>
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


        function toggleEdit(btn) {
            let row = btn.closest('tr');
            let inputs = row.querySelectorAll('.editable');
            let saveBtn = row.querySelector('.save-btn');
            let editBtn = row.querySelector('.edit-btn');
            
            editBtn.style.display = editBtn.style.display === 'none' ? 'block' : 'none';
            saveBtn.style.display = saveBtn.style.display === 'none' ? 'block' : 'none';
            
            inputs.forEach(input => {
                input.disabled = !input.disabled;
            });
        }
    </script>
</body>
</html>
