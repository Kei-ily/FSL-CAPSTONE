<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Filipino Sign Language Learning aaa</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
    }


   /* Hero Banner */
.hero-banner {
  position: relative;
  height: 450px;
  background-color: #212121;
  overflow: hidden;
}

.hero-banner::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: url("homepageHI.png");
  background-size: cover;
  background-position: center;
  filter: brightness(0.7);
}

.hero-banner::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    to right,
    rgba(76, 29, 149, 0.7),
    rgba(219, 39, 119, 0.5)
  );
}

.hero-content {
  position: relative;
  z-index: 10;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  color: white;
  padding: 0 20px;
}

.subtitle {
  font-size: 20px;
  font-weight: 500;
  color: #fef08a;
  margin-bottom: 8px;
  animation: fadeIn 1s ease-out;
}

.title {
  font-size: 36px;
  font-weight: 700;
  margin-bottom: 16px;
  background: linear-gradient(to right, #fef9c3, #ffffff, #fecdd3);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

@media (min-width: 768px) {
  .title {
    font-size: 48px;
  }
}

.description {
  font-size: 16px;
  max-width: 600px;
  margin-bottom: 24px;
  color: #fef3c7;
  line-height: 1.7;
}

.hero-buttons {
  display: flex;
  flex-direction: column;
  gap: 12px;
  max-width: 400px;
}

@media (min-width: 640px) {
  .hero-buttons {
    flex-direction: row;
  }
}

.secondary-button {
  background-color: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(4px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: white;
  font-weight: 500;
  padding: 8px 16px;
  border-radius: 9999px;
  transition: all 0.3s;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.secondary-button:hover {
  background-color: rgba(255, 255, 255, 0.2);
  transform: translateY(-2px);
  box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
}

.primary-button {
  display: block;
  width: 200px;
  margin: 0;
  background: linear-gradient(to right, #8b5cf6, #ec4899);
  color: white;
  font-weight: 700;
  padding: 12px 0;
  border-radius: 9999px;
  transition: all 0.3s;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  font-size: 16px;
  text-decoration: none;
  text-align: center;
}

.primary-button:hover {
  transform: translateY(-3px) scale(1.05);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
  background: linear-gradient(to right, #7c3aed, #db2777);
}

/* Categories Section */
.categories-section {
  padding: 64px 0;
background-color: #0f0f0fff;
}

.section-header {
  text-align: center;
  margin-bottom: 48px;
}

.section-title {
  font-size: 30px;
  font-weight: 700;
  margin-bottom: 12px;
  background: linear-gradient(to right, #8b5cf6, #ec4899);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

.section-description {
  font-size: 18px;
  color: #64748b;
  max-width: 600px;
  margin: 0 auto 16px;
}

.section-divider {
  width: 96px;
  height: 4px;
  background: linear-gradient(to right, #8b5cf6, #ec4899);
  border-radius: 2px;
  margin: 0 auto;
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
  gap: 24px;       /* space between items */
  padding: 0 90px; /* space on container sides */
  justify-items: center;
}

.category-card {
  width: 100%;
  max-width: 320px;
  height: 280px;
  background-color: #232023;
  border-radius: 16px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  padding: 20px;
  transition: all 0.3s;
  display: flex;
  flex-direction: column;
}

.category-card:hover {
  transform: translateY(-5px) scale(1.02);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 16px;
}

.icon-container {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: linear-gradient(to bottom right, #e9d5ff, #fbcfe8);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.icon-container svg {
  height:80%;
  width: 80%;
  font-size: 24px;
  color: #8b5cf6;
}

.lesson-count {
  display: flex;
  align-items: center;
  gap: 4px;
  background-color: #fef3c7;
  padding: 4px 8px;
  border-radius: 9999px;
  font-size: 12px;
  font-weight: 500;
  color: #b45309;
}

.lesson-count i {
  color: #f59e0b;
}

.card-title {
  font-size: 20px;
  font-weight: 700;
  color: white;
  margin-bottom: 8px;
}

.card-description {
  font-size: 14px;
  color: white;
  margin-bottom: 16px;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.time-estimate {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 14px;
  color: #8b5cf6;
  margin-bottom: 16px;
}

.card-button {
  margin-top: auto;
  background: linear-gradient(to right, #8b5cf6, #ec4899);
  color: white;
  font-weight: 500;
  padding: 8px 0;
  border-radius: 9999px;
  transition: all 0.3s;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.card-button:hover {
  background: linear-gradient(to right, #7c3aed, #db2777);
  box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
}


/* CTA Section */
.cta-section {
  padding: 80px 0;
  background-color: #0f0f0fff;
  position: relative;
  overflow: hidden;
}

.cta-section::before {
  content: "";
  position: absolute;
  top: 40px;
  right: 40px;
  width: 128px;
  height: 128px;
  border-radius: 50%;
  background-color: rgba(139, 92, 246, 0.2);
  animation: pulse 3s infinite;
}

.cta-section::after {
  content: "";
  position: absolute;
  bottom: 40px;
  left: 40px;
  width: 96px;
  height: 96px;
  border-radius: 50%;
  background-color: rgba(236, 72, 153, 0.2);
  animation: pulse 3s infinite 1.5s;
}

@keyframes pulse {
  0% {
    transform: scale(1);
    opacity: 0.2;
  }
  50% {
    transform: scale(1.1);
    opacity: 0.3;
  }
  100% {
    transform: scale(1);
    opacity: 0.2;
  }
}

.cta-title {
  font-size: 30px;
  font-weight: 700;
  text-align: center;
  margin-bottom: 16px;
  background: linear-gradient(to right, #8b5cf6, #ec4899);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

.cta-description {
  font-size: 18px;
  color: #64748b;
  text-align: center;
  max-width: 600px;
  margin: 0 auto 32px;
}

.cta-button {
  display: block;
  width: 200px;
  margin: 0 auto;
  background: linear-gradient(to right, #8b5cf6, #ec4899);
  color: white;
  text-decoration: none;
  text-align: center;
  font-weight: 700;
  padding: 12px 0;
  border-radius: 9999px;
  transition: all 0.3s;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  font-size: 16px;
}

.cta-button:hover {
  transform: translateY(-3px) scale(1.05);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
  background: linear-gradient(to right, #7c3aed, #db2777);
}

    
    /* Signup specific */
    .row {
      display: flex;
      gap: 10px;
      /* spacing between inputs */
      width: 100%;
      /* make row full width */
    }

    .row input {
      flex: 1;
      /* equal width */
      min-width: 0;
      /* prevent overflowing */
    }


    .gender {
      display: flex;
      gap: 15px;
      font-size: 14px;
    }

    /* Animation */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: scale(0.9);
      }

      to {
        opacity: 1;
        transform: scale(1);
      }
    }


    /* Footer */
    .footer {
      background: #0d0d0d;
      /* dark footer */
      color: #f5f5f5;
      padding: 30px 0;
      text-align: center;
    }

    .footer-container {
      width: 90%;
      max-width: 1200px;
      margin: auto;
    }

    .footer-logo {
      font-size: 18px;
      margin-bottom: 10px;
    }

    .footer-logo .highlight {
      color: #4f46e5;
      /* same highlight as navbar */
      font-weight: bold;
    }

    .footer-logo .text {
      font-weight: bold;
      margin-left: 5px;
    }

    .footer-links {
      list-style: none;
      padding: 0;
      margin: 10px 0 15px 0;
      display: flex;
      justify-content: center;
      gap: 20px;
    }

    .footer-links a {
      color: #f5f5f5;
      text-decoration: none;
      transition: 0.3s;
    }

    .footer-links a:hover {
      color: #2d9cdb;
      /* hover color */
    }

    .footer-copy {
      font-size: 14px;
      color: #aaa;
    }
  </style>
</head>

<body>
  <?php include "includes/header.php" ?>
    <!-- Hero Banner -->
    <section class="hero-banner">
      <div class="hero-content container">
        <h2 class="subtitle">Kumusta! (Hello!)</h2>
        <h1 class="title">Learn Filipino Sign Language</h1>
        <p class="description">
          Discover the beauty of Filipino Sign Language (FSL) through our
          interactive lessons, visual demonstrations, and practice exercises.
          Start your journey to communicate effectively with the Filipino Deaf
          community.
        </p>
        <div class="hero-buttons">
          <a href="signup.php" class="primary-button">
            Start Learning
          </a>
          
        </div>
      </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section" id="categories-sec">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Explore Categories</h2>
          <p class="section-description">
            Discover different aspects of Filipino Sign Language through our
            categorized lessons and achievements
          </p>
          <div class="section-divider"></div>
        </div>

        <div class="categories-grid">
          <!-- Category Cards -->
          <div class="category-card">
            <div class="card-header">
              <div class="icon-container">
               <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-alphabet-uppercase" viewBox="0 0 16 16">
  <path d="M1.226 10.88H0l2.056-6.26h1.42l2.047 6.26h-1.29l-.48-1.61H1.707l-.48 1.61ZM2.76 5.818h-.054l-.75 2.532H3.51zm3.217 5.062V4.62h2.56c1.09 0 1.808.582 1.808 1.54 0 .762-.444 1.22-1.05 1.372v.055c.736.074 1.365.587 1.365 1.528 0 1.119-.89 1.766-2.133 1.766zM7.18 5.55v1.675h.8c.812 0 1.171-.308 1.171-.853 0-.51-.328-.822-.898-.822zm0 2.537V9.95h.903c.951 0 1.342-.312 1.342-.909 0-.591-.382-.954-1.095-.954zm5.089-.711v.775c0 1.156.49 1.803 1.347 1.803.705 0 1.163-.454 1.212-1.096H16v.12C15.942 10.173 14.95 11 13.607 11c-1.648 0-2.573-1.073-2.573-2.849v-.78c0-1.775.934-2.871 2.573-2.871 1.347 0 2.34.849 2.393 2.087v.115h-1.172c-.05-.665-.516-1.156-1.212-1.156-.849 0-1.347.67-1.347 1.83"/>
</svg>
              </div>
              <div class="lesson-count">
                <i class="fas fa-hashtag"></i>
                <span>26 lessons</span>
              </div>
            </div>
            <h3 class="card-title">Sign Language Alphabet</h3>
            <p class="card-description">
              Learn the basic hand signs for each letter in the Filipino
              alphabet.
            </p>
            <div class="time-estimate">
              <i class="fas fa-clock"></i>
              <span>2 hours</span>
            </div>
            <button class="card-button" onclick="location.href='AlphabetFlashcard.php'">Explore Lessons</button>
          </div>

          <div class="category-card">
            <div class="card-header">
              <div class="icon-container">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-fill" viewBox="0 0 16 16">
  <path d="M8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.771 2.966-.079.186.074.394.273.362 2.256-.37 3.597-.938 4.18-1.234A9 9 0 0 0 8 15"/>
</svg>
              </div>
              <div class="lesson-count">
                <i class="fas fa-hashtag"></i>
                <span>Ongoing</span>
              </div>
            </div>
            <h3 class="card-title">Common Phrases</h3>
            <p class="card-description">
              Master everyday expressions and phrases used in conversations.
            </p>
            <div class="time-estimate">
              <i class="fas fa-clock"></i>
              <span>Ongoing</span>
            </div>
            <button class="card-button">Explore Lessons</button>
          </div>

          <div class="category-card">
            <div class="card-header">
              <div class="icon-container">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-123" viewBox="0 0 16 16">
  <path d="M2.873 11.297V4.142H1.699L0 5.379v1.137l1.64-1.18h.06v5.961zm3.213-5.09v-.063c0-.618.44-1.169 1.196-1.169.676 0 1.174.44 1.174 1.106 0 .624-.42 1.101-.807 1.526L4.99 10.553v.744h4.78v-.99H6.643v-.069L8.41 8.252c.65-.724 1.237-1.332 1.237-2.27C9.646 4.849 8.723 4 7.308 4c-1.573 0-2.36 1.064-2.36 2.15v.057zm6.559 1.883h.786c.823 0 1.374.481 1.379 1.179.01.707-.55 1.216-1.421 1.21-.77-.005-1.326-.419-1.379-.953h-1.095c.042 1.053.938 1.918 2.464 1.918 1.478 0 2.642-.839 2.62-2.144-.02-1.143-.922-1.651-1.551-1.714v-.063c.535-.09 1.347-.66 1.326-1.678-.026-1.053-.933-1.855-2.359-1.845-1.5.005-2.317.88-2.348 1.898h1.116c.032-.498.498-.944 1.206-.944.703 0 1.206.435 1.206 1.07.005.64-.504 1.106-1.2 1.106h-.75z"/>
</svg>
              </div>
              <div class="lesson-count">
                <i class="fas fa-hashtag"></i>
                <span>10 lessons</span>
              </div>
            </div>
            <h3 class="card-title">Numbers & Counting</h3>
            <p class="card-description">
              Learn how to sign numbers and count in Filipino Sign Language.
            </p>
            <div class="time-estimate">
              <i class="fas fa-clock"></i>
              <span>1 hour</span>
            </div>
            <button class="card-button" onclick="location.href='NumberFlashcard.php'">Explore Lessons</button>
          </div>

          <div class="category-card">
            <div class="card-header">
              <div class="icon-container">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-book" viewBox="0 0 16 16">
  <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>
</svg>
              </div>
              <div class="lesson-count">
                <i class="fas fa-hashtag"></i>
                <span>On going</span>
              </div>
            </div>
            <h3 class="card-title">Quizzes</h3>
            <p class="card-description">
              Test your knowledge with interactive quizzes on different FSL
              topics.
            </p>
            <div class="time-estimate">
              <i class="fas fa-clock"></i>
              <span>On going</span>
            </div>
            <button class="card-button">Start Quiz</button>
          </div>

          <div class="category-card">
            <div class="card-header">
              <div class="icon-container">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-controller" viewBox="0 0 16 16">
  <path d="M11.5 6.027a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m-1.5 1.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1m2.5-.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m-1.5 1.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1m-6.5-3h1v1h1v1h-1v1h-1v-1h-1v-1h1z"/>
  <path d="M3.051 3.26a.5.5 0 0 1 .354-.613l1.932-.518a.5.5 0 0 1 .62.39c.655-.079 1.35-.117 2.043-.117.72 0 1.443.041 2.12.126a.5.5 0 0 1 .622-.399l1.932.518a.5.5 0 0 1 .306.729q.211.136.373.297c.408.408.78 1.05 1.095 1.772.32.733.599 1.591.805 2.466s.34 1.78.364 2.606c.024.816-.059 1.602-.328 2.21a1.42 1.42 0 0 1-1.445.83c-.636-.067-1.115-.394-1.513-.773-.245-.232-.496-.526-.739-.808-.126-.148-.25-.292-.368-.423-.728-.804-1.597-1.527-3.224-1.527s-2.496.723-3.224 1.527c-.119.131-.242.275-.368.423-.243.282-.494.575-.739.808-.398.38-.877.706-1.513.773a1.42 1.42 0 0 1-1.445-.83c-.27-.608-.352-1.395-.329-2.21.024-.826.16-1.73.365-2.606.206-.875.486-1.733.805-2.466.315-.722.687-1.364 1.094-1.772a2.3 2.3 0 0 1 .433-.335l-.028-.079zm2.036.412c-.877.185-1.469.443-1.733.708-.276.276-.587.783-.885 1.465a14 14 0 0 0-.748 2.295 12.4 12.4 0 0 0-.339 2.406c-.022.755.062 1.368.243 1.776a.42.42 0 0 0 .426.24c.327-.034.61-.199.929-.502.212-.202.4-.423.615-.674.133-.156.276-.323.44-.504C4.861 9.969 5.978 9.027 8 9.027s3.139.942 3.965 1.855c.164.181.307.348.44.504.214.251.403.472.615.674.318.303.601.468.929.503a.42.42 0 0 0 .426-.241c.18-.408.265-1.02.243-1.776a12.4 12.4 0 0 0-.339-2.406 14 14 0 0 0-.748-2.295c-.298-.682-.61-1.19-.885-1.465-.264-.265-.856-.523-1.733-.708-.85-.179-1.877-.27-2.913-.27s-2.063.091-2.913.27"/>
</svg>
              </div>
              <div class="lesson-count">
                <i class="fas fa-hashtag"></i>
                <span>1 Game</span>
              </div>
            </div>
            <h3 class="card-title">Games</h3>
            <p class="card-description">
              Have fun while learning with memory matching, speed challenges and
              more.
            </p>
            <div class="time-estimate">
              <i class="fas fa-clock"></i>
              <span>On going</span>
            </div>
            <button class="card-button" onclick="location.href='game.php'">Play Games</button>
          </div>

        </div>
      </div>  
    </section>


    <!-- CTA Section -->
    <section class="cta-section">
      <div class="container">
        <h2 class="cta-title">Ready to Start Learning?</h2>
        <p class="cta-description">
          Join thousands of learners mastering Filipino Sign Language through
          our interactive platform. Start your journey today!
        </p>
        <a href="signup.php" class="cta-button">Create Free Account</a>
      </div>
    </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-container">
      <ul class="footer-links">
        <li><a href="#">Terms of Use</a></li>
        <li><a href="#">Privacy Policy</a></li>
      </ul>
      <p class="footer-copy">
        © 2025 Filipino Sign Language. All rights reserved.
      </p>
    </div>
  </footer>

  <!-- External JavaScript -->
  <script>
    const loginModal = document.getElementById("loginModal");
    const signupModal = document.getElementById("signupModal");
    const openLogin = document.getElementById("openLogin");
    const openSignup = document.getElementById("openSignup");
    const closeBtns = document.querySelectorAll(".close");
    const switchToSignup = document.getElementById("switchToSignup");
    const switchToLogin = document.getElementById("switchToLogin");

    // Open modals
    openLogin.onclick = () => loginModal.style.display = "flex";
    openSignup.onclick = () => signupModal.style.display = "flex";

    // Close modals
    closeBtns.forEach(btn => {
      btn.onclick = () => {
        loginModal.style.display = "none";
        signupModal.style.display = "none";
      };
    });

    // Close when clicking outside
    window.onclick = (e) => {
      if (e.target === loginModal) loginModal.style.display = "none";
      if (e.target === signupModal) signupModal.style.display = "none";
    };

    // Switch between Login and Signup
    if (switchToSignup) {
      switchToSignup.onclick = () => {
        loginModal.style.display = "none";
        signupModal.style.display = "flex";
      };
    }

    if (switchToLogin) {
      switchToLogin.onclick = () => {
        signupModal.style.display = "none";
        loginModal.style.display = "flex";
      };
    }
  </script>
</body>


</html>