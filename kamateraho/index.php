<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KamateRaho.com - Earn cash from Home</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            color: #333;
            line-height: 1.6;
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Header Styles */
        header {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            animation: slideInDown 0.5s ease-out;
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 50px;
            width: auto;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .logo span {
            color: #ff6e7f;
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 21px;
            cursor: pointer;
            z-index: 1001;
        }

        .menu-toggle span {
            display: block;
            height: 3px;
            width: 100%;
            background-color: #1a2a6c;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        nav {
            display: flex;
            align-items: center;
        }

        nav ul {
            display: flex;
            list-style: none;
        }

        /* Ensure menu is visible on desktop */
        @media (min-width: 769px) {
            nav ul {
                display: flex !important;
            }
        }

        /* Hide mobile menu by default */
        @media (max-width: 768px) {
            nav ul {
                display: none;
            }
            
            nav ul.active {
                display: flex;
            }
        }

        nav ul li {
            margin-left: 25px;
        }

        nav ul li a {
            color: #1a2a6c;
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 20px;
            white-space: nowrap;
        }

        nav ul li a:hover {
            background: rgba(26, 42, 108, 0.1);
            color: #1a2a6c;
            transform: translateY(-2px);
        }

        .auth-buttons {
            display: flex;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-left: 15px;
            font-size: 1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-login {
            background: transparent;
            color: #1a2a6c;
            border: 2px solid #1a2a6c;
        }

        .btn-login:hover {
            background: #1a2a6c;
            color: white;
        }

        .btn-register {
            background: #1a2a6c;
            color: white;
            border: 2px solid #1a2a6c;
        }

        .btn-register:hover {
            background: transparent;
            color: #1a2a6c;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }

            nav ul {
                position: fixed;
                top: 0;
                right: -100%;
                flex-direction: column;
                background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
                width: 70%;
                height: 100vh;
                padding: 80px 20px 20px;
                transition: right 0.3s ease;
                box-shadow: -5px 0 15px rgba(0,0,0,0.1);
                margin: 0;
                border-left: 1px solid #d1d1d1;
                display: none; /* Hide by default on mobile */
            }

            nav ul.active {
                right: 0;
                display: flex !important; /* Show when active */
            }

            nav ul li {
                margin: 15px 0;
                text-align: center;
            }

            nav ul li a {
                display: block;
                padding: 15px;
                font-size: 1.2rem;
                color: #1a2a6c;
            }

            .auth-buttons {
                position: absolute;
                top: 20px;
                right: 20px;
            }
        }

        /* Hero Section */
        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 120px 5% 80px;
            min-height: 100vh;
            gap: 5%;
            position: relative;
            z-index: 1;
        }

        /* Left Content */
        .hero-content {
            flex: 1;
            max-width: 50%;
            padding: 30px;
            border-radius: 20px;
            animation: slideInLeft 0.8s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Gradient Heading */
        .hero-content h1 {
            font-size: 3.8rem;
            font-weight: 700;
            background: linear-gradient(90deg, #1a2a6c, #f7b733, #ff6e7f);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0 0 20px 0;
            line-height: 1.2;
        }

        /* Typing Effect */
        .typing-text {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
            border-right: 3px solid #1a2a6c;
            white-space: nowrap;
            overflow: hidden;
            display: inline-block;
            animation: blink 0.7s infinite step-end;
            min-height: 50px;
            margin-bottom: 20px;
        }

        @keyframes blink {
            50% { border-color: transparent; }
        }

        /* Paragraph */
        .hero-content p {
            margin: 20px 0;
            font-size: 1.2rem;
            color: #333;
            line-height: 1.6;
        }

        /* CTA Buttons */
        .buttons {
            margin-top: 30px;
            display: flex;
            gap: 20px;
        }

        .btn-primary {
            display: inline-block;
            padding: 14px 32px;
            font-size: 1rem;
            font-weight: 500;
            border-radius: 30px;
            text-decoration: none;
            transition: all 0.3s ease-in-out;
            cursor: pointer;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            color: #1a2a6c;
            border: 2px solid #1a2a6c;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(26, 42, 108, 0.3);
            background: #1a2a6c;
            color: white;
        }

        .btn-outline {
            display: inline-block;
            padding: 14px 32px;
            font-size: 1rem;
            font-weight: 500;
            border-radius: 30px;
            text-decoration: none;
            transition: all 0.3s ease-in-out;
            cursor: pointer;
            border: 2px solid #1a2a6c;
            color: #1a2a6c;
            background: transparent;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-outline:hover {
            background: #1a2a6c;
            color: white;
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(26, 42, 108, 0.3);
        }

        /* Right Side Carousel */
        .hero-visual {
            flex: 1;
            max-width: 45%;
            position: relative;
            height: 500px;
            overflow: hidden;
            border-radius: 25px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            animation: float 6s ease-in-out infinite, slideInRight 0.8s ease-out 0.3s forwards;
            opacity: 0;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .hero-visual img {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            animation: fade 12s infinite;
            border-radius: 25px;
        }

        .hero-visual img:nth-child(1) { animation-delay: 0s; }
        .hero-visual img:nth-child(2) { animation-delay: 4s; }
        .hero-visual img:nth-child(3) { animation-delay: 8s; }

        @keyframes fade {
            0% { opacity: 0; }
            10% { opacity: 1; }
            30% { opacity: 1; }
            40% { opacity: 0; }
            100% { opacity: 0; }
        }

        /* Floating Effect */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        /* Benefits Section */
        .benefits {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 4rem 5%;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            animation: fadeInUp 1s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .benefit-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin: 1rem;
            padding: 2rem;
            text-align: center;
            width: 30%;
            min-width: 250px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: bounceIn 0.8s ease-out;
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .benefit-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .benefit-card h3 {
            color: #1a2a6c;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .benefit-card p {
            color: #333;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        /* Steps Section */
        .steps {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 4rem 5%;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            animation: fadeInUp 1s ease-out;
        }

        .step-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
            padding: 2rem 5%;
        }

        .step-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            padding: 2.5rem;
            text-align: center;
            flex: 1;
            min-width: 280px;
            max-width: 380px;
            transition: all 0.4s ease;
            margin: 1rem;
            border: 1px solid #eee;
            animation: bounceIn 0.8s ease-out;
        }

        .step-number {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #1a2a6c, #f7b733);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 auto 1.5rem;
            font-size: 1.8rem;
            box-shadow: 0 5px 15px rgba(26, 42, 108, 0.3);
            animation: spin 8s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .step-card h3 {
            color: #1a2a6c;
            margin-bottom: 1.2rem;
            font-size: 1.6rem;
        }

        .step-card p {
            color: #333;
            line-height: 1.7;
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        .step-divider {
            background: linear-gradient(to right, #1a2a6c, #f7b733);
            height: 3px;
            border-radius: 2px;
            margin: 1.5rem auto 0;
            width: 50px;
        }

        /* Paid Amount Section */
        .paid-amount {
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
            color: black;
            text-align: center;
            padding: 4rem 5%;
            margin: 2rem 0;
            border-radius: 15px;
            animation: fadeIn 1s ease-out;
        }

        .paid-amount h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .paid-amount p {
            color: black;
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 1.5rem;
        }

        /* Testimonials */
        .testimonials {
            padding: 4rem 5%;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            overflow: hidden;
        }

        .testimonial-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin: 1rem auto;
            padding: 2rem;
            max-width: 800px;
            animation: fadeIn 0.5s ease-out;
        }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            color: #333;
        }

        .testimonial-author {
            font-weight: bold;
            color: #1a2a6c;
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            color: #333;
            padding: 2rem 0 1rem;
            margin-top: 2rem;
            font-family: 'Poppins', sans-serif;
            border-top: 1px solid #d1d1d1;
            animation: fadeInUp 1s ease-out;
        }

        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 0 5%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .footer-section {
            width: 22%;
            min-width: 200px;
            margin: 1rem;
            text-align: left;
        }

        .footer-section h3 {
            color: #1a2a6c;
            margin-bottom: 1rem;
            font-size: 1.2rem;
            position: relative;
            padding-bottom: 0.3rem;
            font-weight: 600;
        }

        .footer-section h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 30px;
            height: 2px;
            background: #ff6e7f;
            border-radius: 2px;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.6rem;
        }

        .footer-links a {
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            display: inline-block;
            position: relative;
            padding-left: 0;
        }

        .footer-links a:hover {
            color: #1a2a6c;
            padding-left: 8px;
        }

        .footer-links a::before {
            content: "→";
            position: absolute;
            left: -15px;
            opacity: 0;
            transition: all 0.3s ease;
            color: #ff6e7f;
            font-size: 0.8rem;
        }

        .footer-links a:hover::before {
            opacity: 1;
            left: -20px;
        }

        .footer-section p {
            color: #333;
            line-height: 1.6;
            font-size: 0.9rem;
            margin-bottom: 0.8rem;
        }

        .footer-contact p {
            position: relative;
            padding-left: 25px;
            margin-bottom: 0.6rem;
        }

        .footer-contact p i {
            position: absolute;
            left: 0;
            top: 3px;
            color: #ff6e7f;
        }

        .footer-social {
            display: flex;
            gap: 12px;
            margin-top: 0.8rem;
        }

        .footer-social a {
            display: inline-block;
            width: 32px;
            height: 32px;
            background: rgba(26, 42, 108, 0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 32px;
            color: #1a2a6c;
            transition: all 0.3s ease;
            font-size: 0.8rem;
            animation: tada 3s infinite;
        }

        @keyframes tada {
            0% { transform: scale(1); }
            10%, 20% { transform: scale(0.9) rotate(-3deg); }
            30%, 50%, 70%, 90% { transform: scale(1.1) rotate(3deg); }
            40%, 60%, 80% { transform: scale(1.1) rotate(-3deg); }
            100% { transform: scale(1) rotate(0); }
        }

        .footer-social a:hover {
            background: #1a2a6c;
            color: white;
            transform: translateY(-3px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 1.5rem;
            margin-top: 1.5rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            font-size: 0.85rem;
            color: #333;
        }

        .footer-bottom p {
            margin: 0;
        }

        /* Single line footer layout */
        .footer-single-line {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            padding: 0 5%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .footer-single-item {
            flex: 1;
            min-width: 200px;
            padding: 0 15px;
            margin: 10px 0;
        }

        .footer-single-item h3 {
            color: #1a2a6c;
            margin-bottom: 0.8rem;
            font-size: 1.1rem;
            position: relative;
            padding-bottom: 0.3rem;
            font-weight: 600;
        }

        .footer-single-item h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 30px;
            height: 2px;
            background: #ff6e7f;
            border-radius: 2px;
        }

        .footer-single-item p,
        .footer-single-item ul {
            color: #333;
            line-height: 1.6;
            font-size: 0.9rem;
            margin: 0;
        }

        .footer-single-item ul {
            list-style: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .footer-single-item li {
            margin-bottom: 0.4rem;
        }

        .footer-single-item a {
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .footer-single-item a:hover {
            color: #1a2a6c;
            padding-left: 5px;
        }

        /* Responsive */
        @media(max-width: 992px) {
            .hero {
                flex-direction: column;
                text-align: center;
                padding: 100px 5% 50px;
            }
            
            .hero-content, .hero-visual {
                max-width: 100%;
                flex: 1;
            }
            
            .hero-visual {
                margin-top: 40px;
                height: 350px;
            }
            
            .hero-content h1 {
                font-size: 2.8rem;
            }
            
            .typing-text {
                font-size: 1.5rem;
            }
            
            .benefit-card, .step-card {
                width: 45%;
            }
            
            nav ul {
                display: none;
            }
            
            .footer-section {
                width: 45%;
            }
            
            .footer-single-item {
                min-width: 45%;
            }
        }

        @media(max-width: 768px) {
            .hero-content h1 {
                font-size: 2.2rem;
            }
            
            .typing-text {
                font-size: 1.2rem;
            }
            
            .benefit-card, .step-card {
                width: 100%;
            }
            
            .buttons {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
            
            .btn-primary, .btn-outline {
                margin: 10px 0;
                width: 80%;
            }
            
            .hero {
                padding: 80px 5% 40px;
                gap: 30px;
            }
            
            .hero-content {
                padding: 20px;
            }
            
            .hero-visual {
                height: 300px;
            }
            
            .footer-section {
                width: 100%;
                text-align: center;
            }
            
            .footer-section h3::after {
                left: 50%;
                transform: translateX(-50%);
            }
            
            .footer-links {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            
            .footer-links a {
                text-align: center;
            }
            
            .footer-links a::before {
                display: none;
            }
            
            .footer-links a:hover {
                padding-left: 0;
            }
            
            .footer-single-item {
                min-width: 100%;
                text-align: center;
                padding: 10px 0;
            }
            
            .footer-single-item h3::after {
                left: 50%;
                transform: translateX(-50%);
            }
            
            .footer-single-item ul {
                justify-content: center;
            }
        }
        
        /* Testimonial Slider Animation */
        @keyframes slide-right {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }
        
        .testimonial-container {
          width: 100%;
          max-width: 56rem;
          padding: 1rem;
          margin: 0 auto;
        }
        .testimonial-grid {
          display: flex;
          flex-direction: column;
          gap: 2rem;
        }
        .image-container {
          position: relative;
          width: 100%;
          height: 16rem;
          perspective: 1000px;
        }
        .testimonial-image {
          position: absolute;
          width: 100%;
          height: 100%;
          object-fit: cover;
          border-radius: 1rem;
          transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
          box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .testimonial-content {
          display: flex;
          flex-direction: column;
          justify-content: space-between;
        }
        .name {
          font-size: 1.125rem;
          font-weight: bold;
          color: #1a2a6c;
          margin-bottom: 0.25rem;
        }
        .designation {
          font-size: 0.875rem;
          color: #6b7280;
          margin-bottom: 1.5rem;
        }
        .quote {
          font-size: 0.95rem;
          color: #333;
          line-height: 1.6;
        }
        .arrow-buttons {
          display: flex;
          gap: 1rem;
          padding-top: 1.5rem;
          justify-content: center;
        }
        .arrow-button {
          width: 2.25rem;
          height: 2.25rem;
          border-radius: 50%;
          background-color: #141414;
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
          transition: background-color 0.3s;
        }
        .arrow-button:hover {
          background-color: #00a6fb;
        }
        .arrow-button svg {
          width: 1.25rem;
          height: 1.25rem;
          fill: #f1f1f7;
          transition: transform 0.3s;
        }
        .arrow-button:hover svg {
          fill: #ffffff;
        }
        .prev-button:hover svg {
          transform: rotate(-12deg);
        }
        .next-button:hover svg {
          transform: rotate(12deg);
        }

        /* Extra Small Devices (Phones, up to 480px) */
        @media (max-width: 480px) {
          .testimonial-container {
            padding: 0.75rem;
          }
          .image-container {
            height: 14rem;
          }
          .name {
            font-size: 1rem;
          }
          .quote {
            font-size: 0.9rem;
          }
          .arrow-button {
            width: 2rem;
            height: 2rem;
          }
        }

        /* Small Devices (Phones, 481px to 768px) */
        @media (min-width: 481px) and (max-width: 768px) {
          .testimonial-container {
            padding: 1rem;
          }
          .image-container {
            height: 18rem;
          }
          .name {
            font-size: 1.25rem;
          }
          .quote {
            font-size: 1rem;
          }
        }

        /* Medium Devices (Tablets, 769px to 1024px) */
        @media (min-width: 769px) and (max-width: 1024px) {
          .testimonial-container {
            padding: 1.5rem;
          }
          .testimonial-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2.5rem;
          }
          .image-container {
            height: 20rem;
          }
          .name {
            font-size: 1.375rem;
          }
          .quote {
            font-size: 1.05rem;
          }
          .arrow-buttons {
            padding-top: 2rem;
            justify-content: flex-start;
          }
        }

        /* Large Devices (Desktops, 1025px to 1200px) */
        @media (min-width: 1025px) and (max-width: 1200px) {
          .testimonial-container {
            padding: 2rem;
          }
          .testimonial-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
          }
          .image-container {
            height: 22rem;
          }
          .name {
            font-size: 1.5rem;
          }
          .quote {
            font-size: 1.125rem;
          }
          .arrow-buttons {
            padding-top: 2.5rem;
          }
        }

        /* Extra Large Devices (Large Desktops, 1201px and up) */
        @media (min-width: 1201px) {
          .testimonial-container {
            padding: 2rem;
          }
          .testimonial-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5rem;
          }
          .image-container {
            height: 24rem;
          }
          .name {
            font-size: 1.5rem;
          }
          .quote {
            font-size: 1.125rem;
          }
          .arrow-buttons {
            padding-top: 3rem;
          }
        }

        /* Ensure proper display on all screen orientations */
        @media (orientation: landscape) and (max-height: 500px) {
          body, html {
            height: auto;
            min-height: 100vh;
          }
          .testimonial-container {
            margin: 1rem auto;
          }
          .image-container {
            height: 14rem;
          }
        }

        /* Withdrawal Information */
        .withdrawal-info {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            padding: 5rem 5%;
            margin: 3rem 0;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 1s ease-out;
        }

        .withdrawal-info .container {
            text-align: center;
            margin-bottom: 3rem;
        }

        .withdrawal-info h2 {
            color: #1a2a6c;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .withdrawal-info p {
            color: #333;
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
        }

        .info-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: center;
        }

        .info-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            flex: 1;
            min-width: 300px;
            max-width: 380px;
            transition: all 0.4s ease;
            border: 1px solid #eee;
            animation: bounceIn 0.8s ease-out;
        }

        .info-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .info-card h3 {
            color: #1a2a6c;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .info-card p {
            color: #333;
            line-height: 1.8;
            font-size: 1rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .register-btn {
            background: linear-gradient(135deg, #3498db, #8e44ad);
            padding: 1.2rem 2.5rem;
            font-size: 1.2rem;
            border-radius: 50px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(52, 152, 219, 0.4);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .register-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(52, 152, 219, 0.5);
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="kamateraho/img/logo.png" alt="KamateRaho Logo" style="height: 65px; width: 250px;">
        </div>
        
        <div class="menu-toggle" id="menuToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <nav>
            <ul id="navMenu">
                <li><a href="#">Home</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#testimonial-container">Testimonials</a></li>
                <li><a href="#withdrawal-info">Withdrawals</a></li>
                <li><a href="#blog">Blog</a></li>
                <li><a href="../register.php">Register</a></li>
                <li><a href="../login.php">Login</a></li>
            </ul>
        </nav>
    </header>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const navMenu = document.getElementById('navMenu');
            
            if (menuToggle && navMenu) {
                menuToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                    
                    // Animate hamburger icon
                    const spans = menuToggle.querySelectorAll('span');
                    if (navMenu.classList.contains('active')) {
                        spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                        spans[1].style.opacity = '0';
                        spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
                    } else {
                        spans[0].style.transform = 'none';
                        spans[1].style.opacity = '1';
                        spans[2].style.transform = 'none';
                    }
                });
                
                // Close menu when clicking on a link
                const navLinks = document.querySelectorAll('nav ul li a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        navMenu.classList.remove('active');
                        const spans = menuToggle.querySelectorAll('span');
                        spans[0].style.transform = 'none';
                        spans[1].style.opacity = '1';
                        spans[2].style.transform = 'none';
                    });
                });
            }
        });
    </script>
    
   <style>
* {box-sizing: border-box}
body {font-family: Verdana, sans-serif; margin:0}
.mySlides {display: none}
img {vertical-align: middle;}

/* Slideshow container */
.slideshow-container {
  width: 100%;
  max-width: 100%;
  position: relative;
  margin: auto;
  height: 550px;
  overflow: hidden;
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}

/* Slides */
.mySlides {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  transition: opacity 1s ease-in-out;
}

.mySlides.active {
  opacity: 1;
  display: block;
}

/* Images */
.slideshow-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* Next & previous buttons */
.prev, .next {
  cursor: pointer;
  position: absolute;
  top: 50%;
  width: auto;
  padding: 16px;
  margin-top: -22px;
  color: white;
  font-weight: bold;
  font-size: 18px;
  transition: 0.6s ease;
  border-radius: 0 3px 3px 0;
  user-select: none;
  background-color: rgba(0,0,0,0.3);
  backdrop-filter: blur(5px);
}

/* Position the "next button" to the right */
.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}

/* On hover, add a black background color with a little bit see-through */
.prev:hover, .next:hover {
  background-color: rgba(0,0,0,0.8);
}

/* Caption text */
.text {
  color: #f2f2f2;
  font-size: 20px;
  padding: 12px 16px;
  position: absolute;
  bottom: 20px;
  width: 100%;
  text-align: center;
  background-color: rgba(0,0,0,0.5);
  backdrop-filter: blur(5px);
  font-weight: bold;
}

/* Number text (1/3 etc) */
.numbertext {
  color: #f2f2f2;
  font-size: 14px;
  padding: 10px 14px;
  position: absolute;
  top: 10px;
  right: 10px;
  background-color: rgba(0,0,0,0.5);
  border-radius: 20px;
  backdrop-filter: blur(5px);
}

/* The dots/bullets/indicators */
.dot {
  cursor: pointer;
  height: 15px;
  width: 15px;
  margin: 0 2px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
  transition: background-color 0.6s ease;
}

.dot.active, .dot:hover {
  background-color: #717171;
}

/* Dots container */
.dots-container {
  text-align: center;
  padding: 20px;
}

/* On smaller screens, decrease text size and adjust height */
@media only screen and (max-width: 768px) {
  .slideshow-container {
    height: 350px;
  }
  .text {
    font-size: 16px;
    padding: 10px 14px;
  }
  .numbertext {
    font-size: 12px;
    padding: 8px 12px;
  }
}

@media only screen and (max-width: 480px) {
  .slideshow-container {
    height: 250px;
  }
  .text {
    font-size: 14px;
    padding: 8px 12px;
    bottom: 10px;
  }
  .numbertext {
    font-size: 10px;
    padding: 6px 10px;
    top: 5px;
    right: 5px;
  }
  .prev, .next {
    padding: 12px;
    font-size: 16px;
  }
}
</style>

<div class="slideshow-container">

<div class="mySlides fade active">
 
  <img src="kamateraho/img/Brown Yellow Make Money Youtube Thumbnail.png" alt="Slide 1">
  <!-- <div class="text">Earn cash from Home</div> -->
</div>

<div class="mySlides fade">
 
  <img src="kamateraho/img/Red and White Money YouTube Thumbnail (1).png" alt="Slide 2">
  <!-- <div class="text">Complete Simple Tasks</div> -->
</div>

<div class="mySlides fade">
  
  <img src="kamateraho/img/G old and Black Modern How to Earn Money Online YouTube Thumbnail.png" alt="Slide 3">
  <!-- <div class="text">Get Paid Instantly</div> -->
</div>

<a class="prev" onclick="plusSlides(-1)">❮</a>
<a class="next" onclick="plusSlides(1)">❯</a>

</div>

<div class="dots-container">
  <span class="dot active" onclick="currentSlide(1)"></span> 
  <span class="dot" onclick="currentSlide(2)"></span> 
  <span class="dot" onclick="currentSlide(3)"></span> 
</div>

<script>
let slideIndex = 1;
let slideInterval;

// Initialize slideshow
showSlides(slideIndex);
startAutoSlide();

// Start automatic slideshow
function startAutoSlide() {
  slideInterval = setInterval(function() {
    plusSlides(1);
  }, 4000); // Change slide every 4 seconds
}

// Pause slideshow on hover
document.addEventListener('DOMContentLoaded', function() {
  let slideshowContainer = document.querySelector('.slideshow-container');
  
  if (slideshowContainer) {
    slideshowContainer.addEventListener('mouseenter', function() {
      clearInterval(slideInterval);
    });

    slideshowContainer.addEventListener('mouseleave', function() {
      startAutoSlide();
    });
  }
});

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");
  
  if (n > slides.length) {slideIndex = 1}    
  if (n < 1) {slideIndex = slides.length}
  
  // Hide all slides
  for (i = 0; i < slides.length; i++) {
    slides[i].classList.remove('active');
  }
  
  // Remove active class from all dots
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }
  
  // Show current slide
  slides[slideIndex-1].classList.add('active');
  dots[slideIndex-1].className += " active";
}
</script>

    
    <!-- Benefits Section -->
    <section class="benefits">
        <div class="benefit-card">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #1a2a6c, #f7b733); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 10px 20px rgba(26, 42, 108, 0.2);">
                <i class="fas fa-home" style="color: white; font-size: 2rem;"></i>
            </div>
            <h3>Earn from Home</h3>
            <p>Work from anywhere, anytime with just your phone and internet.
No skills needed – start earning today.</p>
            <div style="background: linear-gradient(to right, #1a2a6c, #f7b733); height: 3px; border-radius: 2px; margin: 1.5rem 0;"></div>
        </div>
        
        <div class="benefit-card">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #3498db, #8e44ad); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 10px 20px rgba(52, 152, 219, 0.2);">
                <i class="fas fa-bolt" style="color: white; font-size: 2rem;"></i>
            </div>
            <h3>Instant Payments</h3>
            <p>Earn money online anytime, anywhere with just your smartphone and internet. Get fast payments in Paytm, PhonePe, or Google Pay within 24–48 hours.</p>
            <div style="background: linear-gradient(to right, #3498db, #8e44ad); height: 3px; border-radius: 2px; margin: 1.5rem 0;"></div>
        </div>
        
        <div class="benefit-card">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #e74c3c, #e67e22); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 10px 20px rgba(231, 76, 60, 0.2);">
                <i class="fas fa-users" style="color: white; font-size: 2rem;"></i>
            </div>
            <h3>Referral Bonus</h3>
            <p>Invite friends and earn ₹3 for every successful referral. Share more, refer more, and increase your online earnings instantly!</p>
            <div style="background: linear-gradient(to right, #e74c3c, #e67e22); height: 3px; border-radius: 2px; margin: 1.5rem 0;"></div>
        </div>
    </section>
    
    <!-- How It Works Section -->
    <section class="steps" id="how-it-works">
        <div class="container">
            <h2>How It Works</h2>
           
        </div>
        
        <div style="padding: 2rem 5%; max-width: 1200px; margin: 0 auto;">
            <!-- Video Container -->
            <div style="margin-bottom: 3rem;">
                <div style="position: relative; padding-bottom: 56.25%; height: 0; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                    <video 
                        src="https://res.cloudinary.com/dqsxrixfq/video/upload/v1758879139/f1_iy6avf.mp4" 
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; object-fit: cover;"
                        autoplay 
                        loop 
                        muted
                        playsinline>
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
            
            <!-- Steps Container -->
            <div>
                <div class="step-container" style="padding: 0; display: flex; justify-content: flex-start; gap: 1.5rem; flex-wrap: wrap;">
                    <div class="step-card" style="padding: 1.5rem; max-width: 300px; flex: 1; min-width: 250px;">
                        <div class="step-number" style="width: 50px; height: 50px; font-size: 1.4rem;">1</div>
                        <h3 style="font-size: 1.4rem; margin-bottom: 0.8rem;">Join KamateRaho.com</h3>
                        <p style="font-size: 1rem; margin-bottom: 0;">Create your free account and get Rs 50 instantly as a welcome bonus.</p>
                    </div>
                    
                    <div class="step-card" style="padding: 1.5rem; max-width: 300px; flex: 1; min-width: 250px;">
                        <div class="step-number" style="width: 50px; height: 50px; font-size: 1.4rem;">2</div>
                        <h3 style="font-size: 1.4rem; margin-bottom: 0.8rem;">Participate in Offers</h3>
                        <p style="font-size: 1rem; margin-bottom: 0;">Browse and register in offers that match your interests and skills.</p>
                    </div>
                    
                    <div class="step-card" style="padding: 1.5rem; max-width: 300px; flex: 1; min-width: 250px;">
                        <div class="step-number" style="width: 50px; height: 50px; font-size: 1.4rem;">3</div>
                        <h3 style="font-size: 1.4rem; margin-bottom: 0.8rem;">Get Paid</h3>
                        <p style="font-size: 1rem; margin-bottom: 0;">Complete tasks, get approved, and receive payments directly to your wallet.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        function changeVideo(tabId) {
            // Reset all tabs to inactive style
            document.getElementById('tab1').style.background = '#e4edf9';
            document.getElementById('tab1').style.color = '#1a2a6c';
            document.getElementById('tab2').style.background = '#e4edf9';
            document.getElementById('tab2').style.color = '#1a2a6c';
            document.getElementById('tab3').style.background = '#e4edf9';
            document.getElementById('tab3').style.color = '#1a2a6c';
            // Set active tab style
            document.getElementById(tabId).style.background = '#1a2a6c';
            document.getElementById(tabId).style.color = 'white';
            
            // Change video based on tab (using placeholder videos for now) with mute parameter
            var videoSrc = '';
            if (tabId === 'tab1') {
                videoSrc = 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&loop=1&playlist=dQw4w9WgXcQ&mute=1';
            } else if (tabId === 'tab2') {
                videoSrc = 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&loop=1&playlist=dQw4w9WgXcQ&start=30&mute=1';
            } else if (tabId === 'tab3') {
                videoSrc = 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&loop=1&playlist=dQw4w9WgXcQ&start=60&mute=1';
            }
            
            document.getElementById('howItWorksVideo').src = videoSrc;
        }
    </script>
    <!-- Paid Amount Section -->
     <section class="paid-amount">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 2rem; text-align: left; max-width: 1200px; margin: 0 auto; padding: 2rem;">
            <div style="flex: 1; min-width: 300px;">
                <h2 style="font-size: 2.2rem; margin-bottom: 1rem; animation: pulse 2s infinite;">₹17,68,087 already paid to KamateRaho users. Start earning today!</h2>
                <p style="font-size: 1.2rem; margin-bottom: 2rem; max-width: 90%;">Join our community and start earning cash today. Over ₹17 lakhs already paid to our users!</p>
                <a href="register.php" class="btn-primary" style="animation: bounce 2s infinite;">Create Your FREE Account Now</a>
            </div>
            <div style="flex: 1; min-width: 300px; text-align: center;">
                <div style="background: white; border-radius: 15px; padding: 15px;  display: inline-block; animation: float 3s ease-in-out infinite;">
                    <img src="kamateraho/img/brandIcons.gif" alt="Payment Success" style="max-width: 100%; height: auto; border-radius: 10px;">
                </div>
            </div>
        </div>
    </section>
 

    <!-- Withdrawal Information -->
    <section class="withdrawal-info" id="withdrawal-info">
        <div class="container">
            <h2>Withdrawal Information</h2>
            <p>Understand our simple and secure withdrawal process</p>
        </div>
        
        <div class="info-cards">
            <!-- How Withdrawals Work Card -->
            <div class="info-card">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #3498db, #8e44ad); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 10px 20px rgba(52, 152, 219, 0.2);">
                        <i class="fas fa-exchange-alt" style="color: white; font-size: 2rem;"></i>
                    </div>
                    <h3>How Withdrawals Work</h3>
                </div>
                <div style="background: linear-gradient(to right, #3498db, #8e44ad); height: 3px; border-radius: 2px; margin: 1.5rem 0;"></div>
                <p>Withdraw a minimum of ₹200. After verification within 12 hours, your cash is transferred instantly via UPI to your bank account.</p>
                <div style="text-align: center;">
                    <div style="display: inline-flex; align-items: center; gap: 0.5rem; color: #3498db; font-weight: 500;">
                        <span>Fast Processing</span>
                        <i class="fas fa-bolt" style="color: #f39c12;"></i>
                    </div>
                </div>
            </div>

            <!-- Registration Bonus Card -->
            <div class="info-card">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #e74c3c, #e67e22); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 10px 20px rgba(231, 76, 60, 0.2);">
                        <i class="fas fa-gift" style="color: white; font-size: 2rem;"></i>
                    </div>
                    <h3>Registration Bonus</h3>
                </div>
                <div style="background: linear-gradient(to right, #e74c3c, #e67e22); height: 3px; border-radius: 2px; margin: 1.5rem 0;"></div>
                <p>Register now and get ₹50 instantly in your wallet. No hidden charges, no minimum withdrawal – start earning immediately!</p>
                <div style="text-align: center;">
                    <div style="display: inline-block; background: #e74c3c; color: white; padding: 0.5rem 1.5rem; border-radius: 30px; font-weight: 600; font-size: 1.1rem; box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);">
                        ₹50 Bonus
                    </div>
                </div>
            </div>

            <!-- Payment Methods Card -->
            <div class="info-card">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #27ae60, #2ecc71); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 10px 20px rgba(39, 174, 96, 0.2);">
                        <i class="fas fa-cash-check-alt" style="color: white; font-size: 2rem;"></i>
                    </div>
                    <h3>Payment Methods</h3>
                </div>
                <div style="background: linear-gradient(to right, #27ae60, #2ecc71); height: 3px; border-radius: 2px; margin: 1.5rem 0;"></div>
                <p>Get instant transfers to your bank via UPI ID or QR code. Withdrawals are processed within 24 hours.</p>
                <div style="display: flex; justify-content: center; gap: 1rem; margin-top: 1rem;">
                    <div style="width: 50px; height: 50px; background: #27ae60; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div style="width: 50px; height: 50px; background: #27ae60; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 4rem;">
            <a href="../register.php" class="register-btn">Register Now</a>
           
        </div>
         
    </section>
         
    <div class="testimonial-container">
<div style="text-align: center; margin-bottom: 2rem;">
  <h2 style="
    display: inline-block;
    font-size: 2.8rem;
    font-weight: 700;
    background: linear-gradient(90deg, #1a2a6c, #b21f1f, #fdbb2d);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: 2px;
    text-transform: uppercase;
    position: relative;
    padding-bottom: 10px;
  ">
    What Our Clients Say
  </h2>
</div>
      
        <br>
        <div class="testimonial-grid">
            
            <div class="image-container" id="image-container"></div>
            <div class="testimonial-content">
                <div>
                    <h3 class="name" id="name"></h3>
                    <p class="designation" id="designation"></p>
                    <p class="quote" id="quote"></p>
                </div>
                <div class="arrow-buttons">
                    <button class="arrow-button prev-button" id="prev-button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
                        </svg>
                    </button>
                    <button class="arrow-button next-button" id="next-button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const testimonials = [
            {
                quote: "KamateRaho ने मुझे घर बैठे पैसे कमाने का सबसे आसान तरीका दिया! सच में जल्दी और भरोसेमंद।",
                name: "Mohan Kumar",
               
                src: "https://media.istockphoto.com/id/1093206918/photo/portrait-indian-man-standing-at-his-house-in-village.jpg?s=612x612&w=0&k=20&c=UnoX47wcHldmeF_RN7FZ9dcJZ6oSAHM74xkLxJejJBs=",
            },
            {
                quote: "Instant payouts और आसान process – मैं हर महीने अच्छा खासा कमा रहा हूँ। Highly recommend!",
                name: "Apurna davi",
            
                src: "https://media.istockphoto.com/id/1309084086/photo/rural-women-using-phone-in-villlage.jpg?s=612x612&w=0&k=20&c=U-m5YS4jVAva2iErd10TAcOYWut2IZgu5P89ysOKR1s=",
            },
            {
                quote: "Referral और tasks के जरिए कमाई करना इतना आसान कभी नहीं था। KamateRaho ने सच में मदद की!",
                name: "Ram Kumar",
              
                src: "https://as1.ftcdn.net/jpg/04/77/80/58/1000_F_477805833_bx7BuNnQpsZAxVpDaQhQ1QwB1SvoHXdR.jpg",
            },
        ];

        let activeIndex = 0;
        const imageContainer = document.getElementById('image-container');
        const nameElement = document.getElementById('name');
        const designationElement = document.getElementById('designation');
        const quoteElement = document.getElementById('quote');
        const prevButton = document.getElementById('prev-button');
        const nextButton = document.getElementById('next-button');

        function updateTestimonial(direction) {
            const oldIndex = activeIndex;
            activeIndex = (activeIndex + direction + testimonials.length) % testimonials.length;

            testimonials.forEach((testimonial, index) => {
                let img = imageContainer.querySelector(`[data-index="${index}"]`);
                if (!img) {
                    img = document.createElement('img');
                    img.src = testimonial.src;
                    img.alt = testimonial.name;
                    img.classList.add('testimonial-image');
                    img.dataset.index = index;
                    imageContainer.appendChild(img);
                }

                const offset = index - activeIndex;
                const absOffset = Math.abs(offset);
                const zIndex = testimonials.length - absOffset;
                const opacity = index === activeIndex ? 1 : 0.7;
                const scale = 1 - (absOffset * 0.15);
                const translateY = offset === -1 ? '-20%' : offset === 1 ? '20%' : '0%';
                const rotateY = offset === -1 ? '15deg' : offset === 1 ? '-15deg' : '0deg';

                img.style.zIndex = zIndex;
                img.style.opacity = opacity;
                img.style.transform = `translateY(${translateY}) scale(${scale}) rotateY(${rotateY})`;
            });

            nameElement.textContent = testimonials[activeIndex].name;
            designationElement.textContent = testimonials[activeIndex].designation;
            quoteElement.innerHTML = testimonials[activeIndex].quote.split(' ').map(word => `<span class="word">${word}</span>`).join(' ');

            animateWords();
        }

        function animateWords() {
            const words = quoteElement.querySelectorAll('.word');
            words.forEach((word, index) => {
                word.style.opacity = '0';
                word.style.transform = 'translateY(10px)';
                word.style.filter = 'blur(10px)';
                setTimeout(() => {
                    word.style.transition = 'opacity 0.2s ease-in-out, transform 0.2s ease-in-out, filter 0.2s ease-in-out';
                    word.style.opacity = '1';
                    word.style.transform = 'translateY(0)';
                    word.style.filter = 'blur(0)';
                }, index * 20);
            });
        }

        function handleNext() {
            updateTestimonial(1);
        }

        function handlePrev() {
            updateTestimonial(-1);
        }

        prevButton.addEventListener('click', handlePrev);
        nextButton.addEventListener('click', handleNext);

        // Initial setup
        updateTestimonial(0);

        // Autoplay functionality
        const autoplayInterval = setInterval(handleNext, 5000);

        // Stop autoplay on user interaction
        [prevButton, nextButton].forEach(button => {
            button.addEventListener('click', () => {
                clearInterval(autoplayInterval);
            });
        });
    </script>
    <!-- Footer -->
    <footer>
        <div class="footer-single-line">
            <div class="footer-single-item">
                <h3>Navigate</h3>
                <ul class="footer-links">
                    <li><a href="/">Home</a></li>
                    <li><a href="kamateraho/privacy-policy.php">Privacy Policy</a></li>
                    <li><a href="kamateraho/terms-conditions.php">Terms & Conditions</a></li>
                </ul>
            </div>
            
            <div class="footer-single-item">
                <h3>Who we are?</h3>
                <p>KamateRaho.com is your exclusive site to earn pocket cash online. Instant payouts supported via Paytm, PhonePe, Google Pay, and more.</p>
            </div>
            
            <div class="footer-single-item">
                <h3>How it Works?</h3>
                <p>Participate in offers on our page with genuine details and send a redeem request. Once approved, your Paytm amount will be transferred instantly.</p>
            </div>
            
            <div class="footer-single-item">
                <h3>Stay Connected</h3>
                <p>Connect with us on social media for updates and offers.</p>
                <div class="footer-social">
                    <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>© 2025 KamateRaho.com. All rights reserved.</p>
        </div>
    </footer>

    <script>
        /* Typing Effect */
        const textArray = [
            "Earn cash from Home",
            "Complete Simple Tasks",
            "Get Paid Instantly"
        ];
        let index = 0;
        let charIndex = 0;
        let currentText = "";
        let isDeleting = false;
        const typingElement = document.getElementById("typing");

        function type() {
            if (isDeleting) {
                currentText = textArray[index].substring(0, charIndex--);
            } else {
                currentText = textArray[index].substring(0, charIndex++);
            }

            typingElement.textContent = currentText;

            if (!isDeleting && charIndex === textArray[index].length) {
                isDeleting = true;
                setTimeout(type, 1500);
                return;
            }

            if (isDeleting && charIndex === 0) {
                isDeleting = false;
                index = (index + 1) % textArray.length;
            }

            setTimeout(type, isDeleting ? 50 : 100);
        }

        type();

        /* Responsive Menu */
        function toggleMenu() {
            const nav = document.querySelector('nav ul');
            nav.classList.toggle('active');
        }
        
        // Update the year in footer
        document.querySelector('.footer-bottom p').innerHTML = '© ' + new Date().getFullYear() + ' KamateRaho.com. All rights reserved.';
    </script>
</body>
</html>