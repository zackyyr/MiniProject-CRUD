<?php
session_start();
include 'config.php'; // Pastikan sudah terhubung dengan database

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Menambahkan data baru
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['edit_id'])) {
    // Ambil data dari form
    $name = $_POST['name'];
    $departure = $_POST['departure'];
    $weight = $_POST['weight'];
    $arrival = $_POST['arrival'];
    $tracking_id = $_POST['tracking_id'];

    // Validasi input
    if (empty($name) || empty($departure) || empty($weight) || empty($arrival) || empty($tracking_id)) {
        echo "All fields are required!";
    } else {
        // Query untuk memasukkan data ke dalam tabel customers
        $query = "INSERT INTO customers (customer_name, departure, weight, arrival, tracking_id, user_id) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("sssssi", $name, $departure, $weight, $arrival, $tracking_id, $user_id); // Binding parameter

        if ($stmt->execute()) {
            echo "New item added successfully!";
            header("Location: index.php"); // Redirect ke halaman dashboard setelah berhasil
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Proses edit data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id'])) {
    // Ambil data dari form edit
    $edit_id = $_POST['edit_id'];
    $edit_name = $_POST['name'];
    $edit_departure = $_POST['departure'];
    $edit_weight = $_POST['weight'];
    $edit_arrival = $_POST['arrival'];
    $edit_tracking_id = $_POST['tracking_id'];

    // Validasi input
    if (empty($edit_name) || empty($edit_departure) || empty($edit_weight) || empty($edit_arrival) || empty($edit_tracking_id)) {
        echo "All fields are required!";
    } else {
        // Query untuk update data
        $query = "UPDATE customers SET customer_name = ?, departure = ?, weight = ?, arrival = ?, tracking_id = ? WHERE id = ? AND user_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssssssi", $edit_name, $edit_departure, $edit_weight, $edit_arrival, $edit_tracking_id, $edit_id, $user_id); // Binding parameter

        if ($stmt->execute()) {
            echo "Item updated successfully!";
            header("Location: index.php"); // Redirect ke halaman dashboard setelah berhasil
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Proses delete data
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $query = "DELETE FROM customers WHERE id = ? AND user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ii", $delete_id, $user_id); 
    if ($stmt->execute()) {
        echo "Item deleted successfully!";
        header("Location: index.php"); // Redirect ke halaman dashboard setelah berhasil
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Query untuk menampilkan data hanya untuk user yang login
$query = "SELECT * FROM customers WHERE user_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Project - CRUD With PHP</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <!-- Remix Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css" integrity="sha512-kJlvECunwXftkPwyvHbclArO8wszgBGisiLeuDFwNM8ws+wKIw0sv1os3ClWZOcrEB2eRXULYUsm8OVRGJKwGA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
        <section class="crud">
        <div class="crud-header">
            <div class="header-text">
                <h1>Welcome, <?php echo htmlspecialchars($username); ?></h1>
                <p>This is a mini project for CRUD practicing purposes</p>
            </div>
            <div class="header-profile">
                <button onclick="openSettings()"><img src="img/pp.png" alt=""></button>

                <div class="profile-settings" id="settings">
                    <ul>
                        <li><a href="#"><i class="ri-settings-4-line"></i>Settings</a></li>
                        <li><a href="#"><i class="ri-logout-box-r-line"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        
        <button class="add-btn" onclick="openModal()">+ Add New Items</button>
        <div class="container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Customer Name</th>
                        <th>Departure</th>
                        <th>Weight</th>
                        <th>Arrival</th>
                        <th>Tracking ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $no++ . '</td>';
                            echo '<td>' . htmlspecialchars($row['customer_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['departure']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['weight']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['arrival']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['tracking_id']) . '</td>';
                            echo '<td>
                                <button class="btn edit-btn" onclick="openEditModal(' . $row['id'] . ', \'' . htmlspecialchars($row['customer_name']) . '\', \'' . htmlspecialchars($row['departure']) . '\', \'' . htmlspecialchars($row['weight']) . '\', \'' . htmlspecialchars($row['arrival']) . '\', \'' . htmlspecialchars($row['tracking_id']) . '\')">Edit</button>
                                <a href="?delete_id=' . $row['id'] . '" class="btn delete-btn">Delete</a>
                            </td>';                    
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="7">No records found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="form-modal" id="modal">
            <div class="modal-container">
                <h3 class="modal-header">Add New Items</h3>
                <form action="" method="POST">
                    <label for="name">Customer Name:</label>
                    <input type="text" id="name" name="name" placeholder="Name"><br><br>
                    <label for="departure">Departure:</label>
                    <input type="text" id="departure" name="departure" placeholder="Departure"><br><br>
                    <label for="weight">Weight:</label>
                    <input type="text" id="weight" name="weight" placeholder="Weight"><br><br>
                    <label for="arrival">Arrival:</label>
                    <input type="text" id="arrival" name="arrival" placeholder="Arrival"><br><br>
                    <label for="tracking_id">Tracking ID:</label>
                    <input type="text" id="tracking_id" name="tracking_id" placeholder="Tracking ID"><br><br>
                    <button class="btn save-btn" type="submit">Save</button>
                    <button class="btn close-btn" type="button" onclick="closeModal()">Cancel</button>
                </form>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="form-modal" id="editModal">
            <div class="modal-container">
                <h3 class="modal-header">Edit Item</h3>
                <form action="" method="POST" id="editForm">
                    <input type="hidden" id="edit_id" name="edit_id">
                    <label for="edit_name">Customer Name:</label>
                    <input type="text" id="edit_name" name="name" placeholder="Name"><br><br>
                    <label for="edit_departure">Departure:</label>
                    <input type="text" id="edit_departure" name="departure" placeholder="Departure"><br><br>
                    <label for="edit_weight">Weight:</label>
                    <input type="text" id="edit_weight" name="weight" placeholder="Weight"><br><br>
                    <label for="edit_arrival">Arrival:</label>
                    <input type="text" id="edit_arrival" name="arrival" placeholder="Arrival"><br><br>
                    <label for="edit_tracking_id">Tracking ID:</label>
                    <input type="text" id="edit_tracking_id" name="tracking_id" placeholder="Tracking ID"><br><br>
                    <button class="btn save-btn" type="submit">Save Changes</button>
                    <button class="btn close-btn" type="button" onclick="closeEditModal()">Cancel</button>
                </form>
            </div>
        </div><!-- Modal Add & Edit Code (same as your original modal HTML code) -->

    </section>
    <script src="js/script.js"></script>
    <script>
        // Function to open the edit modal and populate the form
        function openEditModal(id, name, departure, weight, arrival, tracking_id) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_departure').value = departure;
            document.getElementById('edit_weight').value = weight;
            document.getElementById('edit_arrival').value = arrival;
            document.getElementById('edit_tracking_id').value = tracking_id;
            document.getElementById('editModal').style.display = 'flex'; // Show the modal
        }

        // Function to close the edit modal
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none'; // Hide the modal
        }

    </script>
</body>
</html>







        