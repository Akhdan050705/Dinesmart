<?php
include 'header.php';
include 'config.php';

// Proteksi: Jika tidak ada data reschedule di session, tendang balik
if(!isset($_SESSION['reschedule_data'])) {
    header("Location: profile.php");
    exit();
}

// Ambil data dari session
$r_data = $_SESSION['reschedule_data'];
$people_count = $r_data['people'];

// --- LOGIKA FILTER MEJA (Sama seperti reservation) ---
$show_small_tables = true;
$show_large_tables = true;
$filter_msg = "";

if ($people_count >= 5) {
    // Jika 5 orang atau lebih -> HANYA Tampilkan Meja Besar
    $show_small_tables = false;
    $show_large_tables = true;
    $filter_msg = "Showing tables suitable for large groups (5+ people)";
} else {
    // Jika 1-4 orang -> HANYA Tampilkan Meja Kecil/Sedang
    $show_large_tables = false;
    $show_small_tables = true;
    $filter_msg = "Showing tables suitable for small groups (1-4 people)";
}

// PROSES UPDATE DATABASE
if(isset($_POST['confirm_reschedule'])) {
    $res_id = $r_data['id'];
    $new_date = $r_data['date'];
    $new_time = $r_data['time'];
    $new_people = $r_data['people'];
    $new_table = $_POST['table_no']; 

    // Query UPDATE
    $sql = "UPDATE reservations SET 
            reservation_date = '$new_date', 
            reservation_time = '$new_time', 
            guests = '$new_people', 
            table_number = '$new_table',
            status = 'Reserved' 
            WHERE id = '$res_id'"; 
            
    if($conn->query($sql)) {
        unset($_SESSION['reschedule_data']); // Hapus session
        echo "<script>alert('Reschedule Successful!'); window.location='profile.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-card" style="width: 700px;">
        <h2 class="auth-title" style="border:none; font-size:24px;">CONFIRM NEW TABLE</h2>
        
        <div style="text-align:center; margin-bottom:15px; color:#555;">
            Date: <b><?php echo date('d M Y', strtotime($r_data['date'])); ?></b> | 
            Time: <b><?php echo $r_data['time']; ?></b> |
            Guests: <b><?php echo $people_count; ?> Pax</b>
        </div>

        <p style="text-align:center; color:#F2994A; font-size:14px; margin-bottom:20px; font-weight:bold;">
            <i class="fas fa-info-circle"></i> <?php echo $filter_msg; ?>
        </p>
        
        <form method="POST">
            <div class="table-grid">
                
                <?php if($show_large_tables): ?>
                <div>
                    <input type="radio" name="table_no" id="t1" value="1" class="table-radio" required>
                    <label for="t1" class="table-option">
                        <div class="furniture rect-long">
                            1
                            <div class="chair c-top"></div>
                            <div class="chair c-bottom"></div>
                        </div>
                        <div style="text-align:center; font-size:12px; margin-top:5px; color:#888;">Capacity: 8 Pax</div>
                    </label>
                </div>
                <?php endif; ?>

                <?php if($show_small_tables): ?>
                <div>
                    <input type="radio" name="table_no" id="t2" value="2" class="table-radio" required>
                    <label for="t2" class="table-option">
                        <div class="furniture square">
                            2
                            <div class="chair c-top"></div>
                            <div class="chair c-bottom"></div>
                            <div class="chair c-left"></div>
                            <div class="chair c-right"></div>
                        </div>
                        <div style="text-align:center; font-size:12px; margin-top:5px; color:#888;">Capacity: 4 Pax</div>
                    </label>
                </div>

                <div>
                    <input type="radio" name="table_no" id="t3" value="3" class="table-radio" required>
                    <label for="t3" class="table-option">
                        <div class="furniture round">
                            3
                            <div class="chair c-top"></div>
                            <div class="chair c-bottom"></div>
                            <div class="chair c-left"></div>
                            <div class="chair c-right"></div>
                        </div>
                        <div style="text-align:center; font-size:12px; margin-top:5px; color:#888;">Capacity: 4 Pax</div>
                    </label>
                </div>

                <div>
                    <input type="radio" name="table_no" id="t4" value="4" class="table-radio" required>
                    <label for="t4" class="table-option">
                        <div class="furniture round">
                            4
                            <div class="chair c-top"></div>
                            <div class="chair c-bottom"></div>
                        </div>
                        <div style="text-align:center; font-size:12px; margin-top:5px; color:#888;">Capacity: 2 Pax</div>
                    </label>
                </div>
                <?php endif; ?>

            </div>

            <div style="display:flex; gap:10px; margin-top:20px;">
                <a href="reschedule.php?id=<?php echo $r_data['id']; ?>" class="btn" style="background:#ccc; color:#333; text-decoration:none; flex:1; text-align:center; border-radius:5px; padding:12px; font-weight:bold;">BACK</a>
                <button type="submit" name="confirm_reschedule" class="btn-orange" style="flex:1;">CONFIRM RESCHEDULE</button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>