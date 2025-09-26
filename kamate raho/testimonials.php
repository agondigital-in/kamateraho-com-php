<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonials - KamateRaho.com</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>

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
  color: #000;
  margin-bottom: 0.25rem;
}
.designation {
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 1.5rem;
}
.quote {
  font-size: 0.95rem;
  color: #4b5563;
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

    </style>
</head>
<body>
  
    <!-- Disclaimer: The testimonials and restaurant name presented here are entirely fictional and created for demonstrational purposes only. Shining Yam is not a real establishment or enterprise. These fictional testimonials are designed to showcase the functionality of the Animated Testimonials component and do not represent real customer experiences or opinions. Any resemblance to actual persons, living or dead, or actual businesses is purely coincidental. This demonstration is intended solely for illustrative purposes in a web development context. -->
    <!-- This component is based on https://ui.aceternity.com/components/animated-testimonials -->
    <!-- Credit -->
    <!-- https://www.perplexity.ai/ -->
    <!-- Used Photos -->
    <!-- https://unsplash.com/photos/woman-standing-beside-lights-xE87C_OvVO4?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash -->
    <!-- https://unsplash.com/photos/man-in-gray-crew-neck-t-shirt-standing-beside-white-wall-MbYgpI1D-cA?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash -->
    <!-- https://unsplash.com/photos/closed-eye-woman-wearing-brown-hat-YbzfTr0pwLE?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash -->

    <div class="testimonial-container">
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
</body>
<script>
       <!-- Image opacity can be adjusted in the const opacity = index === activeIndex ? 1 : 0.7; line -->
const testimonials = [
            {
                quote: "I was impressed by the food — every dish is bursting with flavor! And I could really tell that they use high-quality ingredients. The staff was friendly and attentive, going the extra mile. I'll definitely be back for more!",
                name: "Tamar Mendelson",
                designation: "Restaurant Critic",
                src: "https://images.unsplash.com/photo-1512316609839-ce289d3eba0a?q=80&w=1368&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
            },
            {
                quote: "This place exceeded all expectations! The atmosphere is inviting, and the staff truly goes above and beyond to ensure a fantastic visit. I'll definitely keep returning for more exceptional dining experience.",
                name: "Joe Charlescraft",
                designation: "Frequent Visitor",
                src: "https://images.unsplash.com/photo-1628749528992-f5702133b686?q=80&w=1368&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fA%3D%3D",
            },
            {
                quote: "Shining Yam is a hidden gem! From the moment I walked in, I knew I was in for a treat. The impeccable service and overall attention to detail created a memorable experience. I highly recommend it!",
                name: "Martina Edelweist",
                designation: "Satisfied Customer",
                src: "https://images.unsplash.com/photo-1524267213992-b76e8577d046?q=80&w=1368&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fA%3D%3D",
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
</html>