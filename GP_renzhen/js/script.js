var currentBookId = 0;
let ticking = false;

// 1. Modal Functions 

/**
 * fill data
 * @param {HTMLElement} 
 */
function openModal(el){ 
    // get book id from data
    currentBookId = el.getAttribute('data-id');
    
    // context
    document.getElementById('m-title').innerText = el.getAttribute('data-title');
    document.getElementById('m-author').innerText = el.getAttribute('data-author');
    document.getElementById('m-price').innerText = '$' + el.getAttribute('data-price');
    document.getElementById('m-desc').innerText = el.getAttribute('data-desc');
    
    // detail info
    document.getElementById('m-publisher').innerText = el.getAttribute('data-publisher');
    document.getElementById('m-date').innerText = el.getAttribute('data-date');
    document.getElementById('m-isbn').innerText = el.getAttribute('data-isbn');
    document.getElementById('m-parent').innerText = el.getAttribute('data-parent');
    document.getElementById('m-category').innerText = el.getAttribute('data-category');
    
    // img
    document.getElementById('m-image').src = el.getAttribute('data-image');
    
    document.getElementById('modalOverlay').style.display = 'block';
    document.getElementById('modalOverlay').classList.remove('hidden');
    
    document.getElementById('productModal').style.display = 'flex'; // center
    document.getElementById('productModal').classList.remove('hidden');
}


function closeModal(){
    document.getElementById('modalOverlay').style.display = 'none';
    document.getElementById('productModal').style.display = 'none';
}

// 2. Cart Functions 

/**
 * Toast 
 * @param {string} msg 
 */
function showToast(msg) {
    var toast = document.getElementById("toast-notification");
    if(toast) {
        toast.innerText = msg;
        toast.className = "show";
        
        // 3s
        setTimeout(function(){ 
            toast.className = toast.className.replace("show", ""); 
        }, 3000);
    }
}

function addToCart(){
    if(currentBookId == 0) return;

    var fd = new FormData();
    fd.append('book_id', currentBookId);
    
    // send POST
    fetch('add_to_cart.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
        if(d.status === 'success'){
            // update no.
            var badge = document.getElementById('cart-badge');
            if(badge) badge.innerText = d.total;
            
            showToast("Book added to cart!");
            closeModal();
        }
    });
}


function buyNow(){
    if(currentBookId == 0) return;
    
    var fd = new FormData();
    fd.append('book_id', currentBookId);
    
    fetch('add_to_cart.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
        if(d.status === 'success'){
            window.location.href = 'cart.php'; 
        }
    });
}


// 3. Header Scroll 
window.addEventListener('scroll', function() {
    if (!ticking) {
        window.requestAnimationFrame(function() {
            const header = document.getElementById('main-header');
            if (header) {
                // exceed 50px 
                if (window.scrollY > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            }
            ticking = false;
        });
        ticking = true;
    }
});

// 4. Homepage Specific
document.addEventListener('DOMContentLoaded', function() {
    
    const aboutSection = document.getElementById('aboutUsSection');
    if (aboutSection) {
        const elements = document.querySelectorAll('.js-typewriter');
        let hasStarted = false; // once

        // IntersectionObserver 
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                // 50%will start
                if (entry.isIntersecting && !hasStarted) {
                    hasStarted = true;
                    startTypingSequence(elements, 0);
                }
            });
        }, { threshold: 0.5 }); 
        
        observer.observe(aboutSection);

        function startTypingSequence(elements, index) {
            if (index >= elements.length) return; 

            const el = elements[index];
            const text = el.getAttribute('data-text'); // get text
            el.classList.add('typing');
            
            let charIndex = 0;
            
            function typeChar() {
                if (charIndex < text.length) {
                    el.textContent += text.charAt(charIndex);
                    charIndex++;
                    setTimeout(typeChar, 30);
                } else {
                    el.classList.remove('typing');
                    setTimeout(() => { 
                        startTypingSequence(elements, index + 1); 
                    }, 300);
                }
            }
            typeChar();
        }
    }

    //Team Animatio
    const teamMembers = document.querySelectorAll('.team-member');
    if (teamMembers.length > 0) {
        const teamObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                // 20% trigger
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible'); //  CSS transition trigger
                    teamObserver.unobserve(entry.target);  // once
                }
            });
        }, { threshold: 0.2 }); 
        
        teamMembers.forEach(member => {
            teamObserver.observe(member);
        });
    }
    
});

    // Feedback Form 
    const feedbackForm = document.getElementById('feedbackForm');
    if (feedbackForm) {
        feedbackForm.addEventListener('submit', function(e) {
            e.preventDefault(); 

            const formData = new FormData(this);
            const submitBtn = this.querySelector('.send-btn');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Sending...';

            fetch('submit_feedback.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message); 
                    feedbackForm.reset(); 
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    }; 