<?php
include 'header.php';

// Proteksi: Jika belum isi form tahap 1, tendang balik
if(!isset($_SESSION['temp_booking'])) {
    header("Location: reservation.php");
    exit();
}
?>

<div class="auth-wrapper">
    <div class="auth-card" style="width: 700px;">
        <h2 class="auth-title" style="border:none; font-size:24px;">CHOOSE YOUR TABLE</h2>
        <p class="auth-subtitle">Let's Start Your Dinner With Us</p>
        
        <form action="booking_success.php" method="POST">
            <div class="table-grid">
                
                <div>
                    <input type="radio" name="table_no" id="t1" value="1" class="table-radio" required>
                    <label for="t1" class="table-option">
                        <div class="furniture rect-long">
                            1
                            <div class="chair c-top"></div>
                            <div class="chair c-bottom"></div>
                        </div>
                    </label>
                </div>

                <div>
                    <input type="radio" name="table_no" id="t2" value="2" class="table-radio">
                    <label for="t2" class="table-option">
                        <div class="furniture square">
                            2
                            <div class="chair c-top"></div>
                            <div class="chair c-bottom"></div>
                            <div class="chair c-left"></div>
                            <div class="chair c-right"></div>
                        </div>
                    </label>
                </div>

                <div>
                    <input type="radio" name="table_no" id="t3" value="3" class="table-radio">
                    <label for="t3" class="table-option">
                        <div class="furniture round">
                            3
                            <div class="chair c-top"></div>
                            <div class="chair c-bottom"></div>
                            <div class="chair c-left"></div>
                            <div class="chair c-right"></div>
                        </div>
                    </label>
                </div>

                <div>
                    <input type="radio" name="table_no" id="t4" value="4" class="table-radio">
                    <label for="t4" class="table-option">
                        <div class="furniture round">
                            4
                            <div class="chair c-top"></div>
                            <div class="chair c-bottom"></div>
                        </div>
                    </label>
                </div>
                 </div>

            <button type="submit" name="confirm_booking" class="btn-orange">RESERVE</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>