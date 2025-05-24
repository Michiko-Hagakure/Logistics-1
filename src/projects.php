<?php
include('D:\xampp\htdocs\Logistics 1\dbconn\db.php');

if (isset($_POST['create_project'])) {
    $project_name = $_POST['project_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $budget = $_POST['budget'];

    // Default status for new projects is 'pending'
    $status = 'pending';

    // check kung existing na ba siya sa iba
    $check_sql = "SELECT * FROM projects WHERE projectName = '$project_name'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Duplicate Project',
                        text: 'Project name already exists!',
                        confirmButtonText: 'OK'
                    });
                };
              </script>";
    } else {
        $sql = "INSERT INTO projects (projectName, startDate, endDate, status, budget) 
                VALUES ('$project_name', '$start_date', '$end_date', '$status', '$budget')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    window.onload = function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Project Created Successfully',
                            text: 'Project was created successfully.',
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
                            text: 'Failed to create project. Try again!',
                            confirmButtonText: 'OK'
                        });
                    };
                  </script>";
        }
    }
}
if (isset($_POST['update_project'])) {
    $projectID = $_POST['projectID'];  
    $project_name = $_POST['project_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = strtolower($_POST['status']); // lowercase + validation
    $budget = $_POST['budget'];

    $valid_statuses = ['pending', 'approve', 'decline'];
    if (!in_array($status, $valid_statuses)) {
        $status = 'pending';
    }

    // check duplicate if meron
    $check_sql = "SELECT * FROM projects WHERE projectName = '$project_name' AND projectID != '$projectID'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Duplicate Project Name',
                        text: 'Another project already uses this name!',
                        confirmButtonText: 'OK'
                    });
                };
              </script>";
    } else {
        $sql = "UPDATE projects SET projectName='$project_name', startDate='$start_date', endDate='$end_date', 
                status='$status', budget='$budget' WHERE projectID='$projectID'";  

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    window.onload = function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Project Updated Successfully',
                            text: 'Project was updated successfully.',
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
                            text: 'Failed to update project. Try again!',
                            confirmButtonText: 'OK'
                        });
                    };
                  </script>";
        }
    }
}


if (isset($_POST['delete_project'])) {
    $projectID = $_POST['projectID'];

    if (isset($projectID) && is_numeric($projectID)) {
        $sql = "DELETE FROM projects WHERE projectID='$projectID'";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    window.onload = function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Project Deleted Successfully',
                            text: 'Project was deleted successfully.',
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
                            text: 'Failed to delete project. Try again!',
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
                        title: 'Invalid project ID',
                        text: 'Project ID is invalid or not set.',
                        confirmButtonText: 'OK'
                    });
                };
              </script>";
    }
}
// if magrequest na ng budget papunta sa ibang dept
if (isset($_POST['request_budget'])) {
    $projectID = $_POST['projectID'];

    try {
        $sql = "INSERT INTO budget_requests (projectID, requestDate, status) VALUES ('$projectID', NOW(), 'Pending')";
        $conn->query($sql);

        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Budget Request Sent',
                    text: 'Budget request for project has been sent successfully.',
                    confirmButtonText: 'OK'
                });
            };
        </script>";
    } catch (mysqli_sql_exception $e) {
        error_log("Budget request insert failed: " . $e->getMessage());
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Budget request feature is not available yet. Please try again later.',
                    confirmButtonText: 'OK'
                });
            };
        </script>";
    }
}
$projects = $conn->query("SELECT * FROM projects");
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

            <main class="px-8 py-8">
                <h2 class="text-xl font-bold mb-4">Create Project</h2>
                <button onclick="openProjectModal()"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create Project</button>
                <!-- Modal Container -->
                 <div id="projectModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
                    <div id="projectModalContent"
                    class="bg-white p-6 rounded shadow-lg w-full max-w-md relative transform scale-95 opacity-0 transition duration-300">
                        <button onclick="closeProjectModal()"
                        class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl">&times;</button>
                        <!--Modal Form-->
                        <form method="POST" class="flex flex-col gap-3">
                            <h2 class="text-lg font-semibold mb-2">Create Project</h2>
                            <div class="flex flex-col">
                                <label for="project_name" class="mb-1 font-medium text-gray-700">Project Name</label>
                                <input type="text" id="project_name" name="project_name" placeholder="Project Name"
                                class="p-2 rounded border border-gray-300" required>
                                <div class="flex flex-col">
                                <label for="start_date" class="mb-1 font-medium text-gray-700">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="p-2 rounded border border-gray-300" required>
                                </div>
                                <div class="flex flex-col">
                                <label for="end_date" class="mb-1 font-medium text-gray-700">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="p-2 rounded border border-gray-300">
                                </div>
                                <div class="flex flex-col">
                                <label for="budget" class="mb-1 font-medium text-gray-700">Budget</label>
                                <input type="number" id="budget" name="budget" placeholder="Budget"
                                class="p-2 rounded border border-gray-300" step="0.01">
                                </div>
                            </div>
                            <button type="submit" name="create_project"
                            class="bg-[#4E3B2A] text-white px-4 py-2 rounded hover:bg-[#594423]">Create Project</button>
                        </form>
                    </div>
                 </div>

                <h2 class="text-xl font-bold mb-4">Projects List</h2>

                <table class="min-w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-[#F7E6CA]">
                            <th class="border px-4 py-2">Project Name</th>
                            <th class="border px-4 py-2">Start Date</th>
                            <th class="border px-4 py-2">End Date</th>
                            <th class="border px-4 py-2">Status</th>
                            <th class="border px-4 py-2">Budget</th>
                            <th class="border px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($project = $projects->fetch_assoc()): ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($project['projectName']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($project['startDate']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($project['endDate']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($project['status']); ?></td>
                                <td class="border px-4 py-2">₱<?php echo htmlspecialchars($project['budget']); ?></td>
                                <td class="border px-4 py-2">
                                    <div class="relative inline-block text-left">
                                        <button type="button" onclick="toggleActionDropdown(this)"
                                            class="flex items-center gap-2 bg-[#4E3B2A] text-white px-4 py-2 rounded hover:bg-[#594423] focus:outline-none shadow-lg">
                                            <img src="Logo/PNG/Logo.png" alt="Logo" class="h-6 w-6 rounded-full border border-white shadow" />
                                            <span class="font-semibold">Actions</span>
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <div class="action-dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl z-50 hidden border border-[#F7E6CA]">
                                            <button
                                                class="w-full flex items-center gap-2 px-4 py-3 text-[#4E3B2A] hover:bg-[#F7E6CA] transition rounded-t-lg"
                                                onclick="openEditModal(
                                                    '<?php echo $project['projectID']; ?>',
                                                    '<?php echo addslashes($project['projectName']); ?>',
                                                    '<?php echo $project['startDate']; ?>',
                                                    '<?php echo $project['endDate']; ?>',
                                                    '<?php echo $project['status']; ?>',
                                                    '<?php echo $project['budget']; ?>'
                                                ); closeAllActionDropdowns();"
                                                type="button"
                                            >
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                Edit Project
                                            </button>
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this project?')" class="w-full">
                                                <input type="hidden" name="projectID" value="<?php echo $project['projectID']; ?>">
                                                <button type="submit" name="delete_project"
                                                    class="w-full flex items-center gap-2 px-4 py-3 text-red-600 hover:bg-red-50 transition"
                                                    onclick="closeAllActionDropdowns();"
                                                >
                                                    <i class="fa-solid fa-trash"></i>
                                                    Delete Project
                                                </button>
                                            </form>
                                            <form method="POST" class="w-full">
                                                <input type="hidden" name="projectID" value="<?= $project['projectID'] ?>">
                                                <button type="submit" name="request_budget"
                                                    class="w-full flex items-center gap-2 px-4 py-3 text-green-700 hover:bg-green-50 transition rounded-b-lg"
                                                    onclick="closeAllActionDropdowns();"
                                                >
                                                    <i class="fa-solid fa-coins"></i>
                                                    Request Budget
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded shadow-lg w-[90%] max-w-lg">
        <h2 class="text-xl font-bold mb-4">Edit Project</h2>
        <form method="POST">
            <input type="hidden" name="projectID" id="edit_projectID">
            <input type="text" name="project_name" id="edit_projectName" placeholder="Project Name" class="w-full mb-2 p-2 rounded border border-gray-300" required>
            <input type="date" name="start_date" id="edit_startDate" class="w-full mb-2 p-2 rounded border border-gray-300" required>
            <input type="date" name="end_date" id="edit_endDate" class="w-full mb-2 p-2 rounded border border-gray-300">
            <select name="status" id="edit_status" class="w-full mb-2 p-2 rounded border border-gray-300">
                <option value="pending" <?= (isset($current_status) && $current_status == 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="approve" <?= (isset($current_status) && $current_status == 'approve') ? 'selected' : '' ?>>Approve</option>
                <option value="decline" <?= (isset($current_status) && $current_status == 'decline') ? 'selected' : '' ?>>Decline</option>
            </select>
            <input type="number" name="budget" id="edit_budget" placeholder="Budget" class="w-full mb-4 p-2 rounded border border-gray-300" step="0.01">
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeEditModal()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Cancel</button>
                <button type="submit" name="update_project" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Save</button>
            </div>
        </form>
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
        function openEditModal(id, name, startDate, endDate, status, budget) {
            document.getElementById('edit_projectID').value = id;
            document.getElementById('edit_projectName').value = name;
            document.getElementById('edit_startDate').value = startDate;
            document.getElementById('edit_endDate').value = endDate;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_budget').value = budget;
            document.getElementById('editModal').classList.remove('hidden');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
        // modal animation script
        function openProjectModal() {
            const modal = document.getElementById('projectModal');
            const content = document.getElementById('projectModalContent');
            modal.classList.remove('hidden');
            
            // delay to trigger animation
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
        function closeProjectModal() {
            const modal = document.getElementById('projectModal');
            const content = document.getElementById('projectModalContent');

            // start reverse animation
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            // Wait for animation before hiding modal
            setTimeout(() => {
            modal.classList.add('hidden');
            }, 300);
        }
        function toggleActionDropdown(btn) {
            closeAllActionDropdowns();
            const menu = btn.parentElement.querySelector('.action-dropdown-menu');
            menu.classList.toggle('hidden');
            document.addEventListener('click', function handler(e) {

               if (!btn.parentElement.contains(e.target)) {
                menu.classList.add('hidden');
                document.removeEventListener('click', handler);
        }
    });
        }
        function closeAllActionDropdowns() {
             document.querySelectorAll('.action-dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        }
    </script>
</body>
</html>
