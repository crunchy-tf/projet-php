<?php
// Handle form submission to add tasks
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $tasks = json_decode(file_get_contents('tasks.json'), true);
    $tasks[] = [
        'task' => $_POST['task'],
        'completed' => false
    ];
    file_put_contents('tasks.json', json_encode($tasks));
}

// Handle CRUD operations
if (isset($_GET['action']) && isset($_GET['index'])) {
    $action = $_GET['action'];
    $index = $_GET['index'];
    $tasks = json_decode(file_get_contents('tasks.json'), true);
    switch ($action) {
        case 'delete':
            array_splice($tasks, $index, 1);
            break;
        case 'update':
            $tasks[$index]['completed'] = !$tasks[$index]['completed'];
            break;
    }
    file_put_contents('tasks.json', json_encode($tasks));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple To-Do List with CRUD</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"] {
            padding: 10px;
            width: 70%;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .task-list {
            list-style-type: none;
            padding: 0;
        }
        .task-item {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f2f2f2;
            border-radius: 5px;
        }
        .task-item input[type="checkbox"] {
            margin-right: 10px;
        }
        .task-item.completed {
            background-color: #dff0d8;
            text-decoration: line-through;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            float: right;
        }
    </style>
</head>
<body>
    <h2>Simple To-Do List with CRUD</h2>
    <form method="post" action="">
        <input type="text" name="task" placeholder="Enter task">
        <input type="submit" name="submit" value="Add Task">
    </form>
    <ul class="task-list">
        <?php
        $tasks = json_decode(file_get_contents('tasks.json'), true);

        foreach ($tasks as $index => $task) {
            $class = $task['completed'] ? 'completed' : '';
            echo "<li class='task-item $class'>
                        <input type='checkbox' data-index='$index' " . ($task['completed'] ? 'checked' : '') . "> {$task['task']}
                        <button class='delete-btn' onclick='deleteTask($index)'>Delete</button>
                    </li>";
        }
        ?>
    </ul>
    <script>
        function deleteTask(index) {
            if (confirm("Are you sure you want to delete this task?")) {
                window.location.href = `?action=delete&index=${index}`;
            }
        }

        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const index = this.getAttribute('data-index');
                fetch(`?action=update&index=${index}`);
                this.parentNode.classList.toggle('completed');
            });
        });
    </script>
</body>
</html>
