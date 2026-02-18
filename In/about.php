<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
} ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us - Filipino Sign Language Learning</title>
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

    /* About Section */
    .about-section {
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
      margin: 16px auto 20px;
    }

    /* Mission & Vision Cards */
    .cards-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 32px;
      margin-top: 48px;
    }

    .card {
      background-color: #232023;
      border-radius: 16px;
      padding: 32px;
      text-align: center;
      transition: all 0.3s;
      border: 1px solid rgba(139, 92, 246, 0.2);
    }

    .card:hover {
      transform: translateY(-8px);
      border-color: rgba(139, 92, 246, 0.5);
      box-shadow: 0 8px 20px rgba(139, 92, 246, 0.1);
    }

    .card-icon {
      width: 64px;
      height: 64px;
      background: linear-gradient(135deg, #8b5cf6, #ec4899);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px;
      font-size: 32px;
      color: white;
    }

    .card h3 {
      font-size: 24px;
      font-weight: 700;
      margin-bottom: 12px;
      color: white;
    }

    .card p {
      font-size: 14px;
      color: #a0aec0;
      line-height: 1.8;
    }

    /* Team Section */
    .team-section {
      padding: 80px 0;
      background-color: #0f0f0f;
    }

    .team-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 32px;
      margin-top: 48px;
    }

    .team-member {
      background-color: #232023;
      border-radius: 16px;
      padding: 24px;
      text-align: center;
      transition: all 0.3s;
    }

    .team-member:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(139, 92, 246, 0.15);
    }

    .member-avatar {
      width: 100px;
      height: 100px;
      background: linear-gradient(135deg, #8b5cf6, #ec4899);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px;
      font-size: 40px;
      color: white;
    }

    .team-member h4 {
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 10px;
      color: white;
    }

    .team-member p {
      font-size: 14px;
      color: #8b5cf6;
      margin-bottom: 12px;
    }



    @media (max-width: 768px) {
      .hero-content h1 {
        font-size: 32px;
      }

      .section-title {
        font-size: 28px;
      }

      .cards-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body>
  <?php include "includes/header.php" ?>
  <!-- Hero Banner -->
  <section class="hero-banner">
    <div class="hero-content">
      <h1>About Us</h1>
      <p>Empowering Communication Through Filipino Sign Language</p>
    </div>
  </section>

  <!-- About Section -->
  <section class="about-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Our Story</h2>
        <div class="section-divider"></div>
        <p class="section-description">
          Filipino Sign Language Learning Website was created with a mission to bridge the communication gap between the hearing and Deaf communities. We believe that language is a fundamental right, and everyone deserves access to quality education in sign language.
        </p>
      </div>

      <div class="cards-grid">
        <div class="card">
          <div class="card-icon">
            <i class="fas fa-bullseye"></i>
          </div>
          <h3>Our Mission</h3>
          <p>
            To provide accessible, interactive, and engaging Filipino Sign Language education to learners of all levels, fostering understanding and inclusion within our communities.
          </p>
        </div>

        <div class="card">
          <div class="card-icon">
            <i class="fas fa-eye"></i>
          </div>
          <h3>Our Vision</h3>
          <p>
            A world where Filipino Sign Language is widely understood and respected, enabling seamless communication and genuine inclusion for the Deaf community.
          </p>
        </div>

        <div class="card">
          <div class="card-icon">
            <i class="fas fa-heart"></i>
          </div>
          <h3>Our Values</h3>
          <p>
            Inclusivity, accessibility, respect, and empowerment guide everything we do. We're committed to creating a supportive learning environment for everyone.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Team Section -->
  <section class="team-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Meet our Team</h2>
        <div class="section-divider"></div>
      </div>

      <div class="team-grid">
        <div class="team-member">
          <div class="member-avatar">
            <i class="fas fa-user"></i>
          </div>
          <h4>Tuason, Adrian Kelly T.</h4>
          <p>Lead Programmer</p>
        </div>

        <div class="team-member">
          <div class="member-avatar">
            <i class="fas fa-user"></i>
          </div>
          <h4>Abaloyan, Maria Zhullianne M.</h4>
          <p>Animator</p>
        </div>

         <div class="team-member">
          <div class="member-avatar">
            <i class="fas fa-user"></i>
          </div>
          <h4>Bosch, Franzenelle G.</h4>
          <p>Programmer</p>
        </div>

        <div class="team-member">
          <div class="member-avatar">
            <i class="fas fa-user"></i>
          </div>
          <h4>Mendoza, Ashly </h4>
          <p>Researcher</p>
        </div>

        <div class="team-member">
          <div class="member-avatar">
            <i class="fas fa-user"></i>
          </div>
          <h4>Jacob, Jasmin S.</h4>
          <p>Researcher</p>
        </div>

        <div class="team-member">
          <div class="member-avatar">
            <i class="fas fa-user"></i>
          </div>
          <h4>Barbadillo, Ann Kirstine</h4>
          <p>Researcher</p>
        </div>

         <div class="team-member">
          <div class="member-avatar">
            <i class="fas fa-user"></i>
          </div>
          <h4>Garcia, John Lemar C.</h4>
          <p>Researcher</p>
        </div>

     

    
      </div>
    </div>
  </section>

</body>

</html>
