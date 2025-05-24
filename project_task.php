<?php
include('db.php');

if (isset($_POST['create_task'])) {
    $taskName = $_POST['taskName'];
    $employeeName = $_POST['employeeName'];
    $startDate = $_POST['startDate'];
    $dueDate = $_POST['dueDate'];
    // $status = $_POST['status'];  // Tanggalin ito kasi default na 'Pending' sa DB

    // check kung may duplicates (same task name and employee)
    $check_sql = "SELECT * FROM projecttask WHERE taskName = '$taskName' AND employeeName = '$employeeName'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // duplicate task
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Duplicate Task',
                    text: 'This task already exists for the same employee.',
                    confirmButtonText: 'OK'
                });
            };
        </script>";
    } else {
        // wala duplicates ikaw lang talaga
        $sql = "INSERT INTO projecttask (taskName, employeeName, startDate, dueDate)
                VALUES ('$taskName', '$employeeName', '$startDate', '$dueDate')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Task Created',
                        text: 'New task added successfully.',
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

// i update mo siya
if (isset($_POST['update_task'])) {
    $taskID = $_POST['taskID'];
    $taskName = $_POST['taskName'];
    $employeeName = $_POST['employeeName'];
    $startDate = $_POST['startDate'];
    $dueDate = $_POST['dueDate'];
    $status = $_POST['status'];

    // check if may ibang task with same name and employee pero ibang ID
    $check_sql = "SELECT * FROM projecttask 
                  WHERE taskName = '$taskName' 
                  AND employeeName = '$employeeName' 
                  AND taskID != '$taskID'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Duplicate Task',
                    text: 'Another task with the same name and employee already exists.',
                    confirmButtonText: 'OK'
                });
            };
        </script>";
    } else {
        $sql = "UPDATE projecttask 
                SET taskName='$taskName', employeeName='$employeeName',
                    startDate='$startDate', dueDate='$dueDate', status='$status' 
                WHERE taskID='$taskID'";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Task Updated',
                        text: 'Task updated successfully.',
                        confirmButtonText: 'OK'
                    });
                };
            </script>";
        }
    }
}


// i delete mo siya sa buhay mo
if (isset($_POST['delete_task'])) {
    $taskID = $_POST['taskID'];

    $sql = "DELETE FROM projecttask WHERE taskID='$taskID'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Task Deleted',
                    text: 'Task deleted successfully.',
                    confirmButtonText: 'OK'
                });
            };
        </script>";
    }
}

// Kunin lahat ng task
$tasks = $conn->query("SELECT * FROM projecttask");
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
        .modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%);
        }
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
                <h2 class="text-xl font-bold mb-4">Create Task</h2>
                <button onclick="openTaskModal()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Create Task</button>
                    <!-- Modal Container -->
                     <div id="taskModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
                        <div id="taskModalContent" 
                        class="bg-white p-6 rounded shadow-lg w-full max-w-md relative transform scale-95 opacity-0 transition duration-300">
                        <button onclick="closeTaskModal()"
                        class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl">&times;</button>
                        <form method="POST" class="flex flex-col gap-3">
                            <h2 class="text-lg font-semibold mb-2">Create Task</h2>
                             <div class="flex flex-col">
                                <label for="taskName" class="mb-1 font-medium text-gray-700">Task Name</label>
                                <input type="text" id="taskName" name="taskName" placeholder="Task Name"
                                class="p-2 rounded border border-gray-300" required>
                             </div>
                             <div class="flex flex-col">
                                <label for="employeeName" class="mb-1 font-medium text-gray-700">Employee Name</label>
                                <input type="text" id="employeeName" name="employeeName" placeholder="Employee Name"
                                class="p-2 rounded border border-gray-300" required>
                             </div>
                             <div class="flex flex-col">
                                <label for="startDate" class="mb-1 font-medium text-gray-700">Start Date</label>
                                <input type="date" id="startDate" name="startDate"
                                class="p-2 rounded border border-gray-300" required>
                             </div>
                             <div class="flex flex-col">
                                <label for="dueDate" class="mb-1 font-medium text-gray-700">Due Date</label>
                                <input type="date" id="dueDate" name="dueDate"
                                class="p-2 rounded border border-gray-300" required>
                             </div>
                            <button type="submit" name="create_task"
                            class="bg-[#4E3B2A] text-white px-4 py-2 rounded hover:bg-[#594423]">Create Task</button>
                        </form>
                        </div>
                     </div>
                <h2 class="text-xl font-bold mb-4">Task List</h2>

                <table class="min-w-full table-auto border-collapse">

                    <thead>
                       <tr class="bg-[#F7E6CA]">
                       <th class="border px-4 py-2">Task Name</th>
                       <th class="border px-4 py-2">Employee Name</th>
                       <th class="border px-4 py-2">Start Date</th>
                       <th class="border px-4 py-2">Due Date</th>
                       <th class="border px-4 py-2">Status</th>
                       <th class="border px-4 py-2">Actions</th>
                       </tr>
                     </thead>
                     <tbody>
                        <?php while ($task = $tasks->fetch_assoc()): ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo $task['taskName']; ?></td>
                                <td class="border px-4 py-2"><?php echo $task['employeeName']; ?></td>
                                <td class="border px-4 py-2"><?php echo $task['startDate']; ?></td>
                                <td class="border px-4 py-2"><?php echo $task['dueDate']; ?></td>
                                <td class="border px-4 py-2"><?php echo $task['status']; ?></td>
                                <td class="border px-4 py-2">
                                    <div class="relative inline-block text-left">
                                        <button type="button" onclick="toggleTaskActionDropdown(this)"
                                            class="flex items-center gap-2 bg-[#4E3B2A] text-white px-4 py-2 rounded hover:bg-[#594423] focus:outline-none shadow-lg">
                                            <img src="Logo/PNG/Logo.png" alt="Logo" class="h-6 w-6 rounded-full border border-white shadow" />
                                            <span class="font-semibold">Actions</span>
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <div class="task-action-dropdown-menu absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-xl z-50 hidden border border-[#F7E6CA]">
                                            <button
                                                class="w-full flex items-center gap-2 px-4 py-3 text-[#4E3B2A] hover:bg-[#F7E6CA] transition rounded-t-lg"
                                                onclick="openEditModal(
                                                    '<?php echo $task['taskID']; ?>',
                                                    '<?php echo addslashes($task['taskName']); ?>',
                                                    '<?php echo addslashes($task['employeeName']); ?>',
                                                    '<?php echo $task['startDate']; ?>',
                                                    '<?php echo $task['dueDate']; ?>',
                                                    '<?php echo $task['status']; ?>'
                                                ); closeAllTaskActionDropdowns();"
                                                type="button"
                                            >
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                Edit Task
                                            </button>
                                            <form method="POST" class="w-full" onsubmit="return confirmDeleteTask(this, '<?php echo addslashes($task['taskName']); ?>');">
                                                <input type="hidden" name="taskID" value="<?php echo $task['taskID']; ?>">
                                                <button type="submit" name="delete_task"
                                                    class="w-full flex items-center gap-2 px-4 py-3 text-red-600 hover:bg-red-50 transition rounded-b-lg"
                                                    onclick="closeAllTaskActionDropdowns();"
                                                >
                                                    <i class="fa-solid fa-trash"></i>
                                                    Delete Task
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
            <div id="editModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
                  <div class="bg-white rounded-lg p-6 max-w-lg w-full">
                    <form method="POST">
                         <input type="hidden" name="taskID" id="editTaskID">
                         <div class="mb-4">
                            <label class="block">Task Name:</label>
                            <input type="text" name="taskName" id="editTaskName" class="w-full p-2 border border-gray-300 rounded" required>
                         </div>
                         <div class="mb-4">
                            <label class="block">Employee Name:</label>
                            <input type="text" name="employeeName" id="editEmployeeName" class="w-full p-2 border border-gray-300 rounded" required>
                         </div>
                         <div class="mb-4">
                            <label class="block">Start Date:</label>
                            <input type="date" name="startDate" id="editStartDate" class="w-full p-2 border border-gray-300 rounded" required>
                         </div>
                         <div class="mb-4">
                            <label class="block">Due Date:</label>
                            <input type="date" name="dueDate" id="editDueDate" class="w-full p-2 border border-gray-300 rounded" required>
                         </div>
                         <div class="mb-4">
                             <label class="block">Status:</label>
                             <select name="status" id="editStatus" class="w-full p-2 border border-gray-300 rounded">
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                             </select>
                         </div>
                         <div class="flex justify-end space-x-2">
                            <button type="button" onclick="closeEditModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                            <button type="submit" name="update_task" class="bg-green-500 text-white px-4 py-2 rounded">Save</button>
                         </div>
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
        function openEditModal(taskID, taskName, employeeName, startDate, dueDate, status) {
            document.getElementById('editTaskID').value = taskID;
            document.getElementById('editTaskName').value = taskName;
            document.getElementById('editEmployeeName').value = employeeName;
            document.getElementById('editStartDate').value = startDate;
            document.getElementById('editDueDate').value = dueDate;
            document.getElementById('editStatus').value = status;
            document.getElementById('editModal').classList.remove('hidden');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
        // modal animation script
        function openTaskModal() {
            const modal = document.getElementById('taskModal');
            const content = document.getElementById('taskModalContent');
            modal.classList.remove('hidden');
            
            // delay to trigger animation
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
        function closeTaskModal() {
            const modal = document.getElementById('taskModal');
            const content = document.getElementById('taskModalContent');

            // start reverse animation
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            // Wait for animation before hiding modal
            setTimeout(() => {
            modal.classList.add('hidden');
            }, 300);
        }

        function toggleTaskActionDropdown(button) {
            const dropdownMenu = button.nextElementSibling;
            const allDropdowns = document.querySelectorAll('.task-action-dropdown-menu');

            // Close all other dropdowns
            allDropdowns.forEach(menu => {
                if (menu !== dropdownMenu) {
                    menu.classList.add('hidden');
                }
            });

            // Toggle the clicked dropdown
            dropdownMenu.classList.toggle('hidden');
        }

        function closeAllTaskActionDropdowns() {
            const allDropdowns = document.querySelectorAll('.task-action-dropdown-menu');
            allDropdowns.forEach(menu => {
                menu.classList.add('hidden');
            });
        }

        window.addEventListener('click', function(event) {
            const isDropdownButton = event.target.matches('[onclick^="toggleTaskActionDropdown"]');
            const isInsideDropdown = event.target.closest('.task-action-dropdown-menu');

            if (!isDropdownButton && !isInsideDropdown) {
                closeAllTaskActionDropdowns();
            }
        });

        function confirmDeleteTask(form, taskName) {
            return confirm('Are you sure you want to delete the task: "' + taskName + '"?');
        }
        function closeAllTaskActionDropdowns() {
            document.querySelectorAll('.task-action-dropdown-menu').forEach(menu => menu.classList.add('hidden'));
     }
    </script>
</body>
</html>
