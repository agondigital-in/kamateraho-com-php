<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Earn ₹1,000+ Daily — 2025 Guide</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Google Fonts: Poppins for modern look -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Animate On Scroll Library -->
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css" />

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(115deg,#e1e9fc 10%, #cfd9e9 100%);
      min-height: 100vh;
      margin: 0;
      color: #2e3466;
    }

    /* New Header */
    header {
      background: linear-gradient(120deg, #4f8cff, #6c4fff);
      padding: 80px 22px 40px 22px;
      text-align: center;
      position: relative;
      box-shadow: 0 10px 32px rgba(77,84,255,0.08);
      color: #fff;
      border-radius: 0 0 32px 32px;
      overflow: hidden;
    }
    header h1 {
      font-size: 2.2rem;
      letter-spacing: 1px;
      font-weight: 700;
    }
    .back-link {
      position: absolute;
      left: 24px;
      top: 24px;
      color: #fff;
      font-weight: 700;
      text-decoration: none;
      transition: color .2s;
      opacity: 0.85;
      z-index: 10;
    }
    .back-link:hover { color: #ffd700; }

    /* Glassmorphism Section Cards */
    .section {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: center;
      max-width: 980px;
      margin: 48px auto;
      padding: 38px 26px;
      border-radius: 22px;
      background: rgba(255,255,255,0.73);
      backdrop-filter: blur(8px);
      box-shadow: 0 6px 22px rgba(60,90,180,0.07);
      position: relative;
    }
    .section-content {
      flex: 1 1 55%;
      min-width: 250px;
      padding: 15px 32px 15px 15px;
      z-index: 2;
    }
    .section-content h2 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #6c4fff;
      margin-bottom: 12px;
    }
    .section-content ul, .section-content p {
      font-size: 1.05rem;
      color: #2e3466;
      margin-bottom: 11px;
    }
    .section-content ul {
      margin-left: 22px;
      margin-bottom: 14px;
    }

    .section-images {
      display: flex;
      flex-direction: row;
      gap: 20px;
      flex: 1 1 293px;
      justify-content: flex-end;
    }
    .section-images img {
      width: 90px;
      height: 90px;
      border-radius: 22px;
      object-fit: cover;
      border: 2.5px solid #e9e1f8;
      background: #f4f7ff;
      box-shadow: 0 3px 12px rgba(80,120,255,0.08);
      transition: transform 0.32s;
    }
    .section-images img:hover {
      transform: scale(1.09) rotate(-2deg);
      box-shadow: 0 8px 20px rgba(60,82,255,0.15);
    }
    /* Even sections reverse content order on desktop */
    .section:nth-of-type(even) {
      flex-direction: row-reverse;
    }

    /* Divider - modern colorful wave */
    .divider {
      border: none;
      height: 8px;
      width: 84px;
      margin: 36px auto;
      background: linear-gradient(90deg, #ffa63e, #42e695 45%, #3b82f6 94%);
      border-radius: 4px;
      opacity: 0.9;
      box-shadow: 0 2px 10px #e9ecfa;
      animation: wave 2.4s ease-in-out infinite alternate;
      display: block;
    }
    @keyframes wave {
      to { width: 120px;}
    }

    .btn {
      display: inline-block;
      padding: 12px 29px;
      background: linear-gradient(92deg,#625bff,#59caff);
      color: #fff;
      font-weight: 700;
      font-size: 1.09rem;
      border-radius: 16px;
      text-decoration: none;
      margin-top: 16px;
      box-shadow: 0 3px 10px rgba(73,127,255,0.13);
      transition: background 0.18s, transform .15s;
    }
    .btn:hover {
      background: linear-gradient(110deg,#42e695 30%,#3b82f6 96%);
      transform: scale(1.06);
    }

    /* Floating section title annotation */
    .annot {
      position: fixed;
      top: 32px;
      right: 30px;
      z-index: 1002;
      background: linear-gradient(110deg, #42e695 20%, #3b82f6 80%);
      padding: 12px 28px;
      color: #fff;
      font-weight: 700;
      border-radius: 20px;
      font-size: 1.1rem;
      box-shadow: 0 2px 9px rgba(55,170,188,0.2);
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.5s;
    }
    .annot.visible { opacity: 1;}

    /* New footer */
    footer {
      text-align: center;
      font-size: 0.96rem;
      color: #444;
      background: #f8f9ff;
      border-top: 1.5px solid #eee;
      padding: 32px 0 18px 0;
      margin-top: 34px;
    }
    /* Responsive */
    @media (max-width: 900px) {
      .section { flex-direction: column!important; }
      .section-content { padding: 10px; }
      .section-images { justify-content: flex-start; margin-top: 23px; }
      .section-images img { width: 80px; height: 80px; }
      header { padding: 65px 12px 28px 12px; }
    }
  </style>
</head>
<body>
  <!-- Fixed annotation for visible section -->
  <div id="annotation" class="annot"></div>
  <header>
    <a href="./index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back</a>
    <h1>🤑 How to Earn ₹1,000+ Daily Using Just Your Smartphone (2025)</h1>
  </header>

  <section class="section" data-section="Introduction" data-aos="fade-up">
    <div class="section-content">
      <h2>📱 Introduction</h2>
      <p>Your smartphone is your earning machine in 2025. Apps, micro-tasks, and referrals make daily ₹1,000+ achievable.</p>
    </div>
    <div class="section-images">
      <img src="https://cdn-icons-png.flaticon.com/512/5968/5968776.png" alt="Smartphone">
      <img src="https://cdn-icons-png.flaticon.com/512/2920/2920243.png" alt="Money app">
      <img src="https://cdn-icons-png.flaticon.com/512/1048/1048953.png" alt="Online income">
    </div>
  </section>

  <hr class="divider">

  <section class="section" data-section="Why Smartphone Income" data-aos="fade-up">
    <div class="section-content">
      <h2>💼 Why Your Smartphone Is The New Income Source</h2>
      <ul>
        <li>Work anywhere — home, college, travel</li>
        <li>No investment required</li>
        <li>Flexible hours</li>
        <li>Instant Paytm / UPI withdrawals</li>
      </ul>
    </div>
    <div class="section-images">
      <img src="https://cdn-icons-png.flaticon.com/512/5529/5529248.png" alt="Remote work">
      <img src="https://cdn-icons-png.flaticon.com/512/2088/2088617.png" alt="Earnings chart">
      <img src="https://cdn-icons-png.flaticon.com/512/2331/2331970.png" alt="UPI icon">
    </div>
  </section>

  <hr class="divider">

  <section class="section" data-section="Best Apps" data-aos="fade-up">
    <div class="section-content">
      <h2>💰 Best Apps to Earn ₹1,000+ Daily</h2>
      <ul>
        <li>KamateRaho App – Spin & Earn, Referrals, Cashback</li>
        <li>RozDhan & TaskBucks – Watch videos & complete tasks</li>
        <li>CashKaro, GoSathi – Cashback shopping</li>
      </ul>
    </div>
    <div class="section-images">
      <img src="https://cdn-icons-png.flaticon.com/512/2331/2331776.png" alt="KamateRaho">
      <img src="https://cdn-icons-png.flaticon.com/512/3126/3126647.png" alt="Task App">
      <img src="https://cdn-icons-png.flaticon.com/512/2170/2170153.png" alt="Shopping cashback">
    </div>
  </section>

  <hr class="divider">

  <section class="section" data-section="Earning Tips" data-aos="fade-up">
    <div class="section-content">
      <h2>💡 Tips to Boost Earnings</h2>
      <ul>
        <li>Focus on 2–3 top apps</li>
        <li>Be consistent daily</li>
        <li>Use referral power & groups</li>
        <li>Avoid fake paid apps</li>
      </ul>
      <a href="#" class="btn">Start Earning Now</a>
    </div>
    <div class="section-images">
      <img src="https://cdn-icons-png.flaticon.com/512/2463/2463510.png" alt="Target">
      <img src="https://cdn-icons-png.flaticon.com/512/1055/1055646.png" alt="Success">
      <img src="https://cdn-icons-png.flaticon.com/512/2454/2454250.png" alt="Growth">
    </div>
  </section>
  
  <footer>
    © 2025 KamateRaho Earning Guide. All rights reserved.
  </footer>
  <!-- Animate On Scroll Library -->
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init({ duration: 850, once: true });

    // Simple annotation for section titles
    const annot = document.getElementById('annotation');
    const sections = document.querySelectorAll('.section');
    let timer;
    function showAnnot(name) {
      annot.textContent = 'Section: ' + name;
      annot.classList.add('visible');
      clearTimeout(timer);
      timer = setTimeout(() => annot.classList.remove('visible'), 2200);
    }
    window.addEventListener('scroll', () => {
      sections.forEach(sec => {
        const rect = sec.getBoundingClientRect();
        // Only show annotation if heading is in upper-middle of window
        if(rect.top < window.innerHeight/2 && rect.bottom > window.innerHeight/5) {
          showAnnot(sec.getAttribute('data-section'));
        }
      });
    });
  </script>
</body>
</html>
