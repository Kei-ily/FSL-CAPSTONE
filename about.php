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
      overflow: hidden;
    }

    .member-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
        transform: scale(1.3);
        object-position: 0 1.2px;
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

    .team-member {
      cursor: pointer;
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.8);
      animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    .modal.show {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-content {
      background-color: #232023;
      border-radius: 16px;
      padding: 40px;
      max-width: 500px;
      width: 90%;
      border: 2px solid rgba(139, 92, 246, 0.3);
      position: relative;
      animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
      from {
        transform: translateY(50px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .modal-close {
      position: absolute;
      top: 16px;
      right: 16px;
      font-size: 28px;
      font-weight: bold;
      color: #ec4899;
      cursor: pointer;
      background: none;
      border: none;
      padding: 0;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s;
    }

    .modal-close:hover {
      color: #fecdd3;
      transform: rotate(90deg);
    }

    .modal-avatar {
      width: 120px;
      height: 120px;
      background: linear-gradient(135deg, #8b5cf6, #ec4899);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      font-size: 50px;
      color: white;
      border: 3px solid rgba(139, 92, 246, 0.5);
      overflow: hidden;
    }

    .modal-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
      transform: scale(1.3);
    }

    .modal-header {
      text-align: center;
      margin-bottom: 24px;
    }

    .modal-name {
      font-size: 24px;
      font-weight: 700;
      color: white;
      margin-bottom: 8px;
    }

    .modal-role {
      font-size: 16px;
      color: #8b5cf6;
      margin-bottom: 12px;
    }

    .modal-description {
      font-size: 14px;
      color: #a0aec0;
      line-height: 1.8;
      margin-bottom: 20px;
      text-align: center;
    }

    .modal-social {
      display: flex;
      justify-content: center;
      gap: 16px;
      margin-top: 24px;
      padding-top: 20px;
      border-top: 1px solid rgba(139, 92, 246, 0.2);
    }

    .social-link {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #8b5cf6, #ec4899);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      text-decoration: none;
      transition: all 0.3s;
      font-size: 18px;
    }

    .social-link:hover {
      transform: scale(1.2) translateY(-3px);
      box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
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

        <div class="team-member" onclick="openModal('modal-adrian')">
          <div class="member-avatar">
            <img src="https://via.placeholder.com/100x100?text=Adrian+Kelly" alt="Adrian Kelly T. Tuason">
          </div>
          <h4>Tuason, Adrian Kelly T.</h4>
          <p>Lead Programmer</p>
        </div>

        <div class="team-member" onclick="openModal('modal-maria')">
          <div class="member-avatar">
           <img src="Images/creators_profile/lia.jpg" alt="Maria Zhullianne M. Abaloyan">
          </div>
          <h4>Abaloyan, Maria Zhullianne M.</h4>
          <p>Animator</p>
        </div>

         <div class="team-member" onclick="openModal('modal-franzenelle')">
          <div class="member-avatar">
             <img src="Images/creators_profile/bosch.jpg" alt="Franzenelle G. Bosch">
          </div>
          <h4>Bosch, Franzenelle G.</h4>
          <p>Programmer</p>
        </div>

        <div class="team-member" onclick="openModal('modal-ashly')">
          <div class="member-avatar">
            <img src="Images/creators_profile/ashly.jpg" alt="Ashly Mendoza">
          </div>
          <h4>Mendoza, Ashly </h4>
          <p>Researcher</p>
        </div>

        <div class="team-member" onclick="openModal('modal-jasmin')">
          <div class="member-avatar">
            <img src="Images/creators_profile/jasmin.jpg" alt="Jasmin S. Jacob">
          </div>
          <h4>Jacob, Jasmin S.</h4>
          <p>Researcher</p>
        </div>

        <div class="team-member" onclick="openModal('modal-ann')">
          <div class="member-avatar">
            <img src="Images/creators_profile/ann.jpg" alt="Ann Kirstine Barbadillo">
          </div>
          <h4>Barbadillo, Ann Kirstine</h4>
          <p>Researcher</p>
        </div>

         <div class="team-member" onclick="openModal('modal-john')">
          <div class="member-avatar">
            <img src="Images/creators_profile/lemar.jpg" alt="John Lemar C. Garcia">
          </div>
          <h4>Garcia, John Lemar C.</h4>
          <p>Researcher</p>
        </div>

    
      </div>
    </div>
  </section>




 

  <!-- Modal for Adrian Kelly T. Tuason -->
  <div id="modal-adrian" class="modal" onclick="closeModal(event, 'modal-adrian')">
    <div class="modal-content" onclick="event.stopPropagation()">
      <button class="modal-close" onclick="closeModal(null, 'modal-adrian')">&times;</button>
      <div class="modal-avatar">
  
      </div>
      <div class="modal-header">
        <div class="modal-name">Tuason, Adrian Kelly T.</div>
        <div class="modal-role">Lead Programmer</div>
      </div>
      <div class="modal-description">
        Passionate software developer with expertise in full-stack development. Leads the technical team in building innovative solutions for sign language learning.
      </div>
      <div class="modal-social">
        <a href="#" class="social-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="social-link" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        <a href="#" class="social-link" title="GitHub"><i class="fab fa-github"></i></a>
        <a href="#" class="social-link" title="Email"><i class="fas fa-envelope"></i></a>
      </div>
    </div>
  </div>

  <!-- Modal for Maria Zhullianne M. Abaloyan -->
  <div id="modal-maria" class="modal" onclick="closeModal(event, 'modal-maria')">
    <div class="modal-content" onclick="event.stopPropagation()">
      <button class="modal-close" onclick="closeModal(null, 'modal-maria')">&times;</button>
      <div class="modal-avatar">
        <img src="Images/creators_profile/lia.jpg" alt="Maria Zhullianne M. Abaloyan" >
      </div>
      <div class="modal-header">
        <div class="modal-name">Abaloyan, Maria Zhullianne M.</div>
        <div class="modal-role">Animator</div>
      </div>
      <div class="modal-description">
       The stars are proof that even on the darkest night, there is a little light.
      </div>
      <div class="modal-social">
        <a href="https://www.facebook.com/Mo.kuniix" class="social-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>
       
      </div>
    </div>
  </div>

  <!-- Modal for Franzenelle G. Bosch -->
  <div id="modal-franzenelle" class="modal" onclick="closeModal(event, 'modal-franzenelle')">
    <div class="modal-content" onclick="event.stopPropagation()">
      <button class="modal-close" onclick="closeModal(null, 'modal-franzenelle')">&times;</button>
      <div class="modal-avatar">
         <img src="Images/creators_profile/bosch.jpg" alt="Franzenelle G. Bosch" >
      </div>
      <div class="modal-header">
        <div class="modal-name">Bosch, Franzenelle G.</div>
        <div class="modal-role">Programmer</div>
      </div>
      <div class="modal-description">
        We are all in the gutter, but some of us are looking at the stars.
      </div>
      <div class="modal-social">
        <a href="https://www.facebook.com/franzenelle.bosch.3?rdid=f62GI14MoZFrOWiQ&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2F1847qy27Ga#" class="social-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>
       
      </div>
    </div>
  </div>

  <!-- Modal for Ashly Mendoza -->
  <div id="modal-ashly" class="modal" onclick="closeModal(event, 'modal-ashly')">
    <div class="modal-content" onclick="event.stopPropagation()">
      <button class="modal-close" onclick="closeModal(null, 'modal-ashly')">&times;</button>
      <div class="modal-avatar">
        <img src="Images/creators_profile/ashly.jpg" alt="Ashly Mendoza" >
      </div>
      <div class="modal-header">
        <div class="modal-name">Mendoza, Ashly</div>
        <div class="modal-role">Researcher</div>
      </div>
      <div class="modal-description">
       Isaiah 43:1<br>
        <br>
But now, this is what the Lord says— he who created you, Jacob, he who formed you, Israel: “Do not fear, for I have redeemed you; I have summoned you by name; you are mine."

      </div>
      <div class="modal-social">
        <a href="https://www.facebook.com/profile.php?id=61582917736763&rdid=zI2Ci9cyI255qqsE&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2F1BuoaYLkqF" class="social-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>
      </div>
    </div>
  </div>

  <!-- Modal for Jasmin S. Jacob -->
  <div id="modal-jasmin" class="modal" onclick="closeModal(event, 'modal-jasmin')">
    <div class="modal-content" onclick="event.stopPropagation()">
      <button class="modal-close" onclick="closeModal(null, 'modal-jasmin')">&times;</button>
      <div class="modal-avatar">
        <img src="Images/creators_profile/jasmin.jpg" alt="Jasmin S. Jacob" >
      </div>
      <div class="modal-header">
        <div class="modal-name">Jacob, Jasmin S.</div>
        <div class="modal-role">Researcher</div>
      </div>
      <div class="modal-description">
        Everything happens for a reason
      </div>
      <div class="modal-social">
        <a href="https://www.instagram.com/jas.wakeup?igsh=eGpic2Q3dzRvam40&fbclid=IwY2xjawQCOx5leHRuA2FlbQIxMABicmlkETJDejBtS1V1TTRmbDh5aGlrc3J0YwZhcHBfaWQQMjIyMDM5MTc4ODIwMDg5MgABHpXE5rU3lBahM2tB1AfxmVn99jr11Z_ajdCv3bdO8AaX2NoR5-Xx2-cPQdwL_aem_-7QgCT2Z1A9S0eEhqDu6mw" class="social-link" title="Instagram"><i class="fab fa-instagram"></i></a>
       
      </div>
    </div>
  </div>

  <!-- Modal for Ann Kirstine Barbadillo -->
  <div id="modal-ann" class="modal" onclick="closeModal(event, 'modal-ann')">
    <div class="modal-content" onclick="event.stopPropagation()">
      <button class="modal-close" onclick="closeModal(null, 'modal-ann')">&times;</button>
      <div class="modal-avatar">
        <img src="Images/creators_profile/ann.jpg" alt="Ann Kirstine Barbadillo" >
      </div>
      <div class="modal-header">
        <div class="modal-name">Barbadillo, Ann Kirstine</div>
        <div class="modal-role">Researcher</div>
      </div>
      <div class="modal-description">
       God gives his hardest battles to his strongest warriors
      </div>
      <div class="modal-social">
        <a href="https://www.facebook.com/ivy.958804?rdid=CxwkDXsKYS2jAvx1&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2F1GgQAqRVpA%2F#" class="social-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>
      </div>
    </div>
  </div>

  <!-- Modal for John Lemar C. Garcia -->
  <div id="modal-john" class="modal" onclick="closeModal(event, 'modal-john')">
    <div class="modal-content" onclick="event.stopPropagation()">
      <button class="modal-close" onclick="closeModal(null, 'modal-john')">&times;</button>
      <div class="modal-avatar">
        <img src="Images/creators_profile/lemar.jpg" alt="John Lemar C. Garcia">
      </div>
      <div class="modal-header">
        <div class="modal-name">Garcia, John Lemar C.</div>
        <div class="modal-role">Researcher</div>
      </div>
      <div class="modal-description">
       Isaiah 12.2 <br> <br>

Behold God is my salvation. I will trust and will not be afraid for the lord god is my strength and my song  and he has become my salvation
      </div>
      <div class="modal-social">
        <a href="https://www.facebook.com/johnlemar.garciall?rdid=aAzAWDRm3BffEaMX&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2F18oJxvDR5Z%2F#" class="social-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>
       
      </div>
    </div>
  </div>

  <script>
    function openModal(modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
      }
    }

    function closeModal(event, modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
      }
    }

    // Close modal when pressing Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
          modal.classList.remove('show');
        });
        document.body.style.overflow = 'auto';
      }
    });
  </script>

</body>

</html>
