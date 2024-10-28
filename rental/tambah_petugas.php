<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo '<script>alert("Anda harus login sebagai Admin untuk mengakses halaman ini."); location.href="login.php";</script>';
    exit();
}

// Database connection
include 'koneksi.php';

// Handle form submission for adding or editing petugas
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_POST['id_user'] ?? null;
    $user = $_POST['user'];
    $lvl = $_POST['lvl'];
    $action = $_POST['action'];

    if ($action == 'add') {
        // Ensure the username is unique
        $checkQuery = "SELECT * FROM tbl_user WHERE user='$user'";
        $checkResult = mysqli_query($conn, $checkQuery);
        if (mysqli_num_rows($checkResult) > 0) {
            echo '<script>alert("Username sudah terdaftar. Silakan gunakan username lain.");</script>';
        } else {
            $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
            // Add new user
            $query = "INSERT INTO tbl_user (user, pass, lvl) VALUES ('$user', '$pass', '$lvl')";
            if (mysqli_query($conn, $query)) {
                header('Location: tambah_petugas.php');
                exit();
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
    } else {
        // Edit user
        $query = "UPDATE tbl_user SET user='$user', lvl='$lvl' WHERE id_user='$id_user'";
        if (!empty($_POST['pass'])) {
            $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
            $query = "UPDATE tbl_user SET user='$user', pass='$pass', lvl='$lvl' WHERE id_user='$id_user'";
        }

        if (mysqli_query($conn, $query)) {
            header('Location: tambah_petugas.php');
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// Fetch all users
$result = mysqli_query($conn, "SELECT * FROM tbl_user");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Petugas</title>
    <style>
    /* Global Styles */
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f6f9;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 1200px;
        margin: 50px auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    /* Heading Styles */
    h1 {
        text-align: center;
        color: #0056b3;
        margin-bottom: 30px;
        font-size: 2.2em;
        font-weight: bold;
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table,
    th,
    td {
        border: 1px solid #ddd;
    }

    th,
    td {
        padding: 15px;
        text-align: left;
    }

    th {
        background-color: #0056b3;
        color: white;
        font-weight: bold;
    }

    td {
        background-color: #f9fafb;
    }

    tr:nth-child(even) {
        background-color: #f2f4f6;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
    }

    .modal-content {
        background-color: white;
        margin: 10% auto;
        padding: 30px;
        border-radius: 12px;
        width: 50%;
        max-width: 600px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        animation: showModal 0.3s ease;
    }

    @keyframes showModal {
        from {
            opacity: 0;
            transform: scale(0.8);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    /* Form inside Modal */
    .modal form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .modal form label {
        font-size: 1em;
        font-weight: bold;
        color: #333;
    }

    .modal form input,
    .modal form select {
        padding: 12px;
        font-size: 1em;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 100%;
        box-sizing: border-box;
        transition: border-color 0.3s;
    }

    .modal form input:focus,
    .modal form select:focus {
        border-color: #0056b3;
        outline: none;
    }

    /* Button Styles */
    .btn {
        background-color: #28a745;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 5px;
        font-size: 1em;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.2s;
    }

    .btn:hover {
        background-color: #218838;
        transform: translateY(-2px);
    }

    .btn-add {
        float: right;
        margin-bottom: 20px;
        background-color: #007bff;
    }

    .btn-add:hover {
        background-color: #0069d9;
    }

    /* Responsive Modal */
    @media screen and (max-width: 768px) {
        .modal-content {
            width: 80%;
        }
    }

    @media screen and (max-width: 480px) {
        .modal-content {
            width: 95%;
            margin-top: 20%;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Kelola Petugas</h1>

        <!-- Add User Button -->
        <button class="btn btn-add" onclick="openModal('add')">Tambah Petugas</button>

        <!-- Users Table -->
        <table>
            <thead>
                <tr>
                    <th>ID User</th>
                    <th>Username</th>
                    <th>Level</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['id_user']; ?></td>
                    <td><?= $row['user']; ?></td>
                    <td><?= $row['lvl']; ?></td>
                    <td>
                        <button class="btn"
                            onclick="openModal('edit', <?= htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Modal (used for both Add and Edit) -->
    <div id="petugasModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Tambah Petugas</h2>
            <form method="POST" id="petugasForm">
                <input type="hidden" name="action" id="action" value="add">
                <input type="hidden" name="id_user" id="id_user">

                <label for="user">Username:</label>
                <input type="text" name="user" id="user" required>

                <label for="pass" id="passLabel">Password:</label>
                <input type="password" name="pass" id="pass" required>

                <label for="lvl">Level:</label>
                <select name="lvl" id="lvl" required>
                    <option value="admin">Admin</option>
                    <option value="petugas">Petugas</option>
                </select>

                <button type="submit" class="btn">Simpan</button>
            </form>
        </div>
    </div>

    <script>
    function openModal(action, user = null) {
        document.getElementById('petugasModal').style.display = 'block';
        document.getElementById('action').value = action;

        if (action === 'edit' && user) {
            document.getElementById('modalTitle').innerText = 'Edit Petugas';
            document.getElementById('id_user').value = user.id_user;
            document.getElementById('user').value = user.user;
            document.getElementById('lvl').value = user.lvl;

            // Hide password field for editing
            document.getElementById('passLabel').style.display = 'none';
            document.getElementById('pass').style.display = 'none';
        } else {
            document.getElementById('modalTitle').innerText = 'Tambah Petugas';
            document.getElementById('id_user').value = '';
            document.getElementById('user').value = '';
            document.getElementById('lvl').value = 'petugas';

            // Show password field for adding
            document.getElementById('passLabel').style.display = 'block';
            document.getElementById('pass').style.display = 'block';
        }
    }

    function closeModal() {
        document.getElementById('petugasModal').style.display = 'none';
        // Reset the form after closing the modal
        document.getElementById('petugasForm').reset();
        document.getElementById('passLabel').style.display = 'block'; // Show password label again
        document.getElementById('pass').style.display = 'block'; // Show password input again
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        if (event.target === document.getElementById('petugasModal')) {
            closeModal();
        }
    }
    </script>
</body>

</html>