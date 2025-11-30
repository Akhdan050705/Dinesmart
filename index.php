<?php include 'header.php'; ?>

<section class="hero-section">
    <div style="color:var(--primary-orange); font-size: 40px; margin-bottom: 10px;"><i class="fas fa-cloud-meatball"></i></div>
    <h1 class="hero-title">Welcome To Our Restaurant</h1>
    <p class="hero-subtitle">Enjoy An Exclusive Dining Experience With Effortless Reservations, Personalized Service, And Trusted Menu Information.</p>
    <a href="reservation.php" class="btn-orange btn-hero">Reserve Now</a>
</section>

<section class="section-container">
    <h2 class="section-title">View Our Menu</h2>
    <div class="menu-grid">
        <div class="menu-card">
            <img src="https://images.unsplash.com/photo-1544025162-d76694265947?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Main Courses">
            <div class="menu-label">Main Courses</div>
        </div>
        <div class="menu-card">
            <img src="https://images.unsplash.com/photo-1551024709-8f23befc6f87?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Drinks">
            <div class="menu-label">Drinks</div>
        </div>
    </div>
</section>

<section class="section-container">
    <div class="bestseller-card">
        <div class="bs-content">
            <h2>Our Best Seller</h2>
            <p>Grilled Lemon Salmon Served With Fresh Side Salad, Combining Smoky Flavor And Zesty Freshness For A Healthy, Delicious Meal.</p>
            <a href="#" class="btn-orange" style="text-decoration:none; display:inline-block; width:auto; padding: 12px 30px;">View Menu</a>
        </div>
        <div class="bs-image">
            <img src="https://images.unsplash.com/photo-1467003909585-2f8a72700288?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="Grilled Salmon">
        </div>
    </div>
</section>

<section class="section-container" style="margin-bottom: 80px;">
    <h2 class="section-title">CUSTOMER FEEDBACKS</h2>
    <div class="feedback-grid">
        <div class="feedback-card">
            <div class="fb-name">KUMAN TUNMAN</div>
            <p class="fb-text">"Grilled Lemon Salmon-nya wajib coba! Teksturnya lembut, rasa lemonnya pas, dan side salad-nya segar banget. Plus, saya suka bisa lihat informasi nutrisinya sebelum pesan."</p>
            <div class="fb-stars">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
            <i class="fas fa-quote-right fb-quote"></i>
        </div>
        <div class="feedback-card">
            <div class="fb-name">WILLIAM SANTOSO</div>
            <p class="fb-text">"Puas banget sama layanan di sini! Makan enak, sehat, dan semua informasi menu lengkap."</p>
            <div class="fb-stars">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
            </div>
            <i class="fas fa-quote-right fb-quote"></i>
        </div>
        <div class="feedback-card">
            <div class="fb-name">AMANDA PUTRI</div>
            <p class="fb-text">"Pengalaman makan di sini sangat memuaskan. Reservasi mudah lewat website, makanan berkualitas."</p>
            <div class="fb-stars">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
            <i class="fas fa-quote-right fb-quote"></i>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>