<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us - Filipino Sign Language Learning</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
     * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #0f0f0f;
      color: #f5f5f5;
    }

    .container {
      width: 90%;
      max-width: 1200px;
      margin: 0 auto;
    }

    /* Hero Section */
    .hero-banner {
      position: relative;
      height: 250px;
      background-color: #212121;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .hero-banner::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(139, 92, 246, 0.3), rgba(236, 72, 153, 0.3));
    }

    .hero-content {
      position: relative;
      z-index: 10;
      text-align: center;
      color: white;
    }

    .hero-content h1 {
      font-size: 48px;
      font-weight: 700;
      margin-bottom: 16px;
      background: linear-gradient(to right, #fef9c3, #ffffff, #fecdd3);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    .hero-content p {
      font-size: 18px;
      color: #fef3c7;
      max-width: 600px;
    }

    /* Contact Section */
    .contact-section {
      padding: 80px 0;
      background-color: #0f0f0f;
    }

    .section-header {
      text-align: center;
      margin-bottom: 48px;
    }

    .section-title {
      font-size: 36px;
      font-weight: 700;
      margin-bottom: 12px;
      background: linear-gradient(to right, #8b5cf6, #ec4899);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    .section-description {
      font-size: 16px;
      color: #64748b;
      max-width: 700px;
      margin: 0 auto;
      line-height: 1.8;
    }

    .section-divider {
      width: 96px;
      height: 4px;
      background: linear-gradient(to right, #8b5cf6, #ec4899);
      border-radius: 2px;
      margin: 16px auto 10px;
    }

    /* Contact Grid */
    .contact-grid {
     display: flex;
     justify-content: center;
      gap: 48px;
      margin-top: 48px;
    }

    /* Contact Info */
    .contact-info {
      display: flex;
      flex-direction: row;
      gap: 32px;
    }

    .info-card {
      display: flex;
      gap: 16px;
      align-items: flex-start;
    }

    .info-icon {
      width: 48px;
      height: 48px;
      background: linear-gradient(135deg, #8b5cf6, #ec4899);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      color: white;
      flex-shrink: 0;
    }

    .info-content h3 {
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 4px;
      color: white;
    }

    .info-content p {
      font-size: 14px;
      color: #a0aec0;
      line-height: 1.6;
    }

    .info-content a {
      color: #8b5cf6;
      text-decoration: none;
      transition: 0.3s;
    }

    .info-content a:hover {
      color: #ec4899;
    }

  </style>
</head>

<body>
  <?php include "includes/header.php" ?>
  <!-- Hero Banner -->
  <section class="hero-banner">
    <div class="hero-content">
      <h1>Contact Us</h1>
      <p>We'd Love to Hear From You</p>
    </div>
  </section>

  <!-- Contact Section -->
  <section class="contact-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Get In Touch</h2>
        <div class="section-divider"></div>
        <p class="section-description">
          Have questions about our platform or want to collaborate? Reach out contact us directly through any of our channels.
        </p>
      </div>

      <div class="contact-grid">
        <!-- Contact Info -->
        <div class="contact-info">
          <div class="info-card">
            <div class="info-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="info-content">
              <h3>Address</h3>
              <p>
                Lagro High School<br>
                District V Quezon City
              </p>
            </div>
          </div>

          <div class="info-card">
            <div class="info-icon">
              <i class="fas fa-phone"></i>
            </div>
            <div class="info-content">
              <h3>Phone</h3>
              <p>
                <a href="tel:+09292753869">+63 (9) 292753-869</a>
              </p>
            </div>
          </div>

          <div class="info-card">
            <div class="info-icon">
              <i class="fas fa-envelope"></i>
            </div>
            <div class="info-content">
              <h3>Email</h3>
              <p>
                <a href="mailto:tuasonadrian19@gmail.com">tuasonadrian19@gmail.com</a>
              
              </p>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>


</body>

</html>
