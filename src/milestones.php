<?php
include('D:\xampp\htdocs\Logistics 1\dbconn\db.php');

if (isset($_POST['create_milestone'])) {
    $taskName = $_POST['taskName'];
    $milestoneName = $_POST['milestoneName'];
    $dueDate = $_POST['dueDate'];
    $status = 'Not Started';  // dito mo na nilagay ang default status

    if (empty($taskName) || empty($milestoneName) || empty($dueDate)) {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Fields',
                    text: 'Please fill in all the required fields.',
                    confirmButtonText: 'OK'
                });
            };
        </script>";
    } else {
        // Check for duplicates
        $check_sql = "SELECT * FROM milestones WHERE taskName = '$taskName' AND milestoneName = '$milestoneName'";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) {
            echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Duplicate Milestone',
                        text: 'Milestone already exists for this task.',
                        confirmButtonText: 'OK'
                    });
                };
            </script>";
        } else {
            $defaultProjectID = 1;

            $sql = "INSERT INTO milestones (projectID, taskName, milestoneName, dueDate, status, createdAt, updatedAt)
                    VALUES ('$defaultProjectID', '$taskName', '$milestoneName', '$dueDate', '$status', NOW(), NOW())";

            if ($conn->query($sql) === TRUE) {
                echo "<script>
                    window.onload = function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Milestone Created',
                            text: 'New milestone added successfully.',
                            confirmButtonText: 'OK'
                        });
                    };
                </script>";
            } else {
                echo "<script>
                    window.onload = function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Database Error',
                            text: 'Something went wrong. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    };
                </script>";
            }
        }
    }
}

// i update mo siya sa buhay mo
if (isset($_POST['update_milestone'])) {
    $milestoneID = $_POST['milestoneID'];
    $taskName = $_POST['taskName'];
    $milestoneName = $_POST['milestoneName'];
    $dueDate = $_POST['dueDate'];
    $status = $_POST['status'];

    // check kung duplicates (excluding current milestone)
    $check_sql = "SELECT * FROM milestones 
                  WHERE taskName = '$taskName' AND milestoneName = '$milestoneName' 
                  AND milestoneID != '$milestoneID'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Duplicate Milestone',
                    text: 'Another milestone with the same name already exists for this task.',
                    confirmButtonText: 'OK'
                });
            };
        </script>";
    } else {
        $sql = "UPDATE milestones 
                SET taskName='$taskName', milestoneName='$milestoneName', 
                    dueDate='$dueDate', status='$status', updatedAt=NOW()
                WHERE milestoneID='$milestoneID'";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    window.onload = function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Milestone Updated',
                            text: 'Milestone updated successfully.',
                            confirmButtonText: 'OK'
                        });
                    };
                  </script>";
        }
    }
}


if (isset($_POST['delete_milestone'])) {
    $milestoneID = $_POST['milestoneID'];

    $sql = "DELETE FROM milestones WHERE milestoneID='$milestoneID'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Milestone Deleted',
                        text: 'Milestone deleted successfully.',
                        confirmButtonText: 'OK'
                    });
                };
              </script>";
    }
}
$milestones = $conn->query("SELECT * FROM milestones");
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
                <h2 class="text-xl font-bold mb-4">Milestones</h2>
                <button onclick="openMilestoneModal()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Create Milestone</button>
                    <!-- Modal Container -->
                     <div id="milestoneModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
                        <div id="milestoneModalContent" class="bg-white p-6 rounded shadow-lg w-full max-w-md relative transform scale-95 opacity-0 transition duration-300">
                            <button onclick="closeMilestoneModal()"
                             class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl">&times;</button>
                             <form method="POST" class="flex flex-col gap-3">
                                <div class="flex flex-col">
                                    <label for="taskName" class="mb-1 font-medium text-gray-700">Task Name</label>
                                    <input type="text" id="taskName" name="taskName" placeholder="Task Name" class="p-2 rounded border border-gray-300" required>
                                </div>
                                <div class="flex flex-col">
                                    <label for="milestoneName" class="mb-1 font-medium text-gray-700">Milestone Name</label>
                                    <input type="text" id="milestoneName" name="milestoneName" placeholder="Milestone Name" class="p-2 rounded border border-gray-300" required>
                                </div>
                                <div class="flex flex-col">
                                    <label for="dueDate" class="mb-1 font-medium text-gray-700">Due Date</label>
                                    <input type="date" id="dueDate" name="dueDate" class="p-2 rounded border border-gray-300" required>
                                </div>
                                <button type="submit" name="create_milestone"
                                class="bg-[#4E3B2A] text-white px-4 py-2 rounded hover:bg-[#594423]">Create</button>
                             </form>
                        </div>
                     </div>
                <h2 class="text-xl font-bold mb-4">Milestones List</h2>

                <table class="min-w-full table-auto border-collapse">

                    <thead>
                       <tr class="bg-[#F7E6CA]">
                       <th class="border px-4 py-2">Task Name</th>
                       <th class="border px-4 py-2">Milestone Name</th>
                       <th class="border px-4 py-2">Due Date</th>
                       <th class="border px-4 py-2">Status</th>
                       <th class="border px-4 py-2">Actions</th>
                       </tr>
                     </thead>
                     <tbody>
                        <?php while ($row = $milestones->fetch_assoc()): ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($row['taskName']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($row['milestoneName']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($row['dueDate']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($row['status']); ?></td>
                                <td class="border px-4 py-2">
                                    <div class="relative inline-block text-left">
                                        <button type="button" onclick="toggleMilestoneActionDropdown(this)"
                                            class="flex items-center gap-2 bg-[#4E3B2A] text-white px-4 py-2 rounded hover:bg-[#594423] focus:outline-none shadow-lg">
                                            <img src="Logo/PNG/Logo.png" alt="Logo" class="h-6 w-6 rounded-full border border-white shadow" />
                                            <span class="font-semibold">Actions</span>
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <div class="milestone-action-dropdown-menu absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-xl z-50 hidden border border-[#F7E6CA]">
                                            <button
                                                class="w-full flex items-center gap-2 px-4 py-3 text-[#4E3B2A] hover:bg-[#F7E6CA] transition rounded-t-lg edit-btn"
                                                data-id="<?php echo $row['milestoneID']; ?>"
                                                data-task="<?php echo htmlspecialchars($row['taskName']); ?>"
                                                data-milestone="<?php echo htmlspecialchars($row['milestoneName']); ?>"
                                                data-due="<?php echo $row['dueDate']; ?>"
                                                data-status="<?php echo $row['status']; ?>"
                                                onclick="openMilestoneEditModal(this); closeAllMilestoneActionDropdowns();"
                                                type="button"
                                            >
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                Edit Milestone
                                            </button>
                                            <form method="POST" class="w-full" onsubmit="return confirmDeleteMilestone(this, '<?php echo addslashes($row['milestoneName']); ?>');">
                                                <input type="hidden" name="milestoneID" value="<?php echo $row['milestoneID']; ?>">
                                                <button type="submit" name="delete_milestone"
                                                    class="w-full flex items-center gap-2 px-4 py-3 text-red-600 hover:bg-red-50 transition rounded-b-lg"
                                                    onclick="closeAllMilestoneActionDropdowns();"
                                                >
                                                    <i class="fa-solid fa-trash"></i>
                                                    Delete Milestone
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                            </tr>
                        <?php endwhile; ?>
                     </tbody>
                </table>
            </main>
        </div>
    </div>
    <!-- Modal Background -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md relative">
        <h3 class="text-xl font-bold mb-4">Edit Milestone</h3>
        <form method="POST" id="editForm">
            <input type="hidden" name="milestoneID" id="editMilestoneID">
            <div class="mb-3">
                <label for="editTaskName" class="block mb-1 font-medium">Task Name</label>
                <input type="text" name="taskName" id="editTaskName" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label for="editMilestoneName" class="block mb-1 font-medium">Milestone Name</label>
                <input type="text" name="milestoneName" id="editMilestoneName" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label for="editDueDate" class="block mb-1 font-medium">Due Date</label>
                <input type="date" name="dueDate" id="editDueDate" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label for="editStatus" class="block mb-1 font-medium">Status</label>
                <select name="status" id="editStatus" class="w-full border rounded px-3 py-2" required>
                    <option value="Not Started">Not Started</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Achieved">Achieved</option>
                </select>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" id="closeModal" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                <button type="submit" name="update_milestone" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Save</button>
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
        const editModal = document.getElementById('editModal');const closeModalBtn = document.getElementById('closeModal');
        const editForm = document.getElementById('editForm');

    // elements nasa loob ng modal
        const editMilestoneID = document.getElementById('editMilestoneID');
        const editTaskName = document.getElementById('editTaskName');
        const editMilestoneName = document.getElementById('editMilestoneName');
        const editDueDate = document.getElementById('editDueDate');
        const editStatus = document.getElementById('editStatus');

        document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', () => {
            editMilestoneID.value = button.dataset.id;
            editTaskName.value = button.dataset.task;
            editMilestoneName.value = button.dataset.milestone;
            editDueDate.value = button.dataset.due;
            editStatus.value = button.dataset.status;

            editModal.classList.remove('hidden');
        });
    });
    // iclose yung modal
    closeModalBtn.addEventListener('click', () => {
        editModal.classList.add('hidden');
    });

    // Optional: Close modal clicking outside content
    editModal.addEventListener('click', (e) => {
        if (e.target === editModal) {
            editModal.classList.add('hidden');
        }
    });


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
        // modal animation script
        function openMilestoneModal() {
            const modal = document.getElementById('milestoneModal');
            const content = document.getElementById('milestoneModalContent');
            modal.classList.remove('hidden');
            
            // delay to trigger animation
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
        function closeMilestoneModal() {
            const modal = document.getElementById('milestoneModal');
            const content = document.getElementById('milestoneModalContent');

            // start reverse animation
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            // Wait for animation before hiding modal
            setTimeout(() => {
            modal.classList.add('hidden');
            }, 300);
        }

        function toggleMilestoneActionDropdown(button) {
            const dropdownMenu = button.nextElementSibling;
            const allDropdowns = document.querySelectorAll('.milestone-action-dropdown-menu');

            // Close all other dropdowns
            allDropdowns.forEach(menu => {
                if (menu !== dropdownMenu) {
                    menu.classList.add('hidden');
                }
            });

            // Toggle the clicked dropdown
            dropdownMenu.classList.toggle('hidden');
        }

        function closeAllMilestoneActionDropdowns() {
            const allDropdowns = document.querySelectorAll('.milestone-action-dropdown-menu');
            allDropdowns.forEach(menu => {
                menu.classList.add('hidden');
            });
        }

        function confirmDeleteMilestone(form, milestoneName) {
            return confirm("Are you sure you want to delete the milestone: " + milestoneName + "?");
        }
        function closeAllMilestoneActionDropdowns() {
            document.querySelectorAll('.milestone-action-dropdown-menu').forEach(menu => menu.classList.add('hidden'));
      }
    </script>
</body>
</html>
