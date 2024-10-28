<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo '<script>alert("Anda harus login sebagai Admin untuk mengakses halaman ini."); location.href="login.php";</script>';
    exit();
}

// Database connection
include 'koneksi.php';

// Handle form submission for adding, editing, or deleting members
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nik = $_POST['nik'];
    $nama = $_POST['nama'];
    $jk = $_POST['jk'];
    $telp = $_POST['telp'];
    $alamat = $_POST['alamat'];
    $user = $_POST['user'];
    $pass = isset($_POST['pass']) && !empty($_POST['pass']) ? password_hash($_POST['pass'], PASSWORD_DEFAULT) : null;
    $action = $_POST['action'];

    if ($action == 'add') {
        // Add new member
        $query = "INSERT INTO tbl_member (nik, nama, jk, telp, alamat, user, pass, role) 
                  VALUES ('$nik', '$nama', '$jk', '$telp', '$alamat', '$user', '$pass', 'user')";
    } elseif ($action == 'edit') {
        // Edit member
        if ($pass) {
            // If a new password is provided
            $query = "UPDATE tbl_member SET nama='$nama', jk='$jk', telp='$telp', alamat='$alamat', user='$user', pass='$pass' 
                      WHERE nik='$nik'";
        } else {
            // If no new password is provided, keep the existing password
            $query = "UPDATE tbl_member SET nama='$nama', jk='$jk', telp='$telp', alamat='$alamat', user='$user' 
                      WHERE nik='$nik'";
        }
    } elseif ($action == 'delete') {
        // Delete member
        $query = "DELETE FROM tbl_member WHERE nik='$nik'";
    }

    // Execute the query
    if (mysqli_query($conn, $query)) {
        header('Location: tambah_member.php');
        exit();
    } else {
        echo 'Error: ' . mysqli_error($conn);
    }
}

// Fetch all members
$result = mysqli_query($conn, "SELECT * FROM tbl_member");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Member</title>
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
    .modal form select,
    .modal form textarea {
        padding: 12px;
        font-size: 1em;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 100%;
        box-sizing: border-box;
        transition: border-color 0.3s;
    }

    .modal form input:focus,
    .modal form select:focus,
    .modal form textarea:focus {
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
        <h1>Kelola Member</h1>

        <!-- Add Member Button -->
        <button class="btn btn-add" onclick="openModal('add')">Tambah Member</button>

        <!-- Members Table -->
        <table>
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Jenis Kelamin</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                    <th>User</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['nik']; ?></td>
                    <td><?= $row['nama']; ?></td>
                    <td><?= $row['jk']; ?></td>
                    <td><?= $row['telp']; ?></td>
                    <td><?= $row['alamat']; ?></td>
                    <td><?= $row['user']; ?></td>
                    <td>
                        <button class="btn"
                            onclick="openModal('edit', <?= htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="nik" value="<?= $row['nik']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus member ini?');">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for Adding/Editing Member -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <form id="memberForm" method="POST">
                <input type="hidden" name="nik" id="nik">
                <input type="hidden" name="action" id="action">
                <label for="nama">Nama:</label>
                <input type="text" name="nama" id="nama" required>
                <label for="jk">Jenis Kelamin:</label>
                <select name="jk" id="jk" required>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
                <label for="telp">Telepon:</label>
                <input type="text" name="telp" id="telp" required>
                <label for="alamat">Alamat:</label>
                <textarea name="alamat" id="alamat" required></textarea>
                <label for="user">User:</label>
                <input type="text" name="user" id="user" required>
                <label for="pass">Password (Kosongkan jika tidak ingin mengubah):</label>
                <input type="password" name="pass" id="pass">
                <button type="submit" class="btn">Simpan</button>
            </form>
        </div>
    </div>

    <script>
    function openModal(mode, member = null) {
        const modal = document.getElementById('modal');
        const form = document.getElementById('memberForm');
        if (mode === 'add') {
            document.getElementById('action').value = 'add';
            form.reset();
        } else if (mode === 'edit') {
            document.getElementById('action').value = 'edit';
            document.getElementById('nik').value = member.nik;
            document.getElementById('nama').value = member.nama;
            document.getElementById('jk').value = member.jk;
            document.getElementById('telp').value = member.telp;
            document.getElementById('alamat').value = member.alamat;
            document.getElementById('user').value = member.user;
        }
        modal.style.display = 'block';
    }

    function closeModal() {
        document.getElementById('modal').style.display = 'none';
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('modal');
        if (event.target === modal) {
            closeModal();
        }
    };
    </script>
</body>

</html>