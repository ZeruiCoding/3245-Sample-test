/**
 * ==================================================================
 * File: script.js
 * Description: 前台页面 (Homepage, Shopping) 的通用交互逻辑。
 * Functionality:
 * 1. 书籍详情模态框 (Modal) 的打开与关闭。
 * 2. 购物车 AJAX 操作 (加入购物车、立即购买)。
 * 3. Toast 消息提示。
 * 4. 顶部导航栏滚动收起动画 (Sticky Header)。
 * 5. 首页特有动画 (打字机效果、团队成员上滑显现)。
 * ==================================================================
 */

// --- 全局变量 ---
var currentBookId = 0; // 用于记录当前在 Modal 中查看的书籍 ID
let ticking = false;   // 用于 requestAnimationFrame 的防抖标志位

// ==========================================
// 1. Modal Functions (模态框逻辑)
// ==========================================

/**
 * 打开模态框并填充数据
 * @param {HTMLElement} el - 被点击的书籍卡片元素 (this)
 */
function openModal(el){ 
    // 从 data- 属性中获取书籍 ID
    currentBookId = el.getAttribute('data-id');
    
    // --- 填充文本内容 ---
    document.getElementById('m-title').innerText = el.getAttribute('data-title');
    document.getElementById('m-author').innerText = el.getAttribute('data-author');
    document.getElementById('m-price').innerText = '$' + el.getAttribute('data-price');
    document.getElementById('m-desc').innerText = el.getAttribute('data-desc');
    
    // 填充详细参数 (ISBN, 出版社, 分类等)
    document.getElementById('m-publisher').innerText = el.getAttribute('data-publisher');
    document.getElementById('m-date').innerText = el.getAttribute('data-date');
    document.getElementById('m-isbn').innerText = el.getAttribute('data-isbn');
    document.getElementById('m-parent').innerText = el.getAttribute('data-parent');
    document.getElementById('m-category').innerText = el.getAttribute('data-category');
    
    // --- 填充图片 ---
    document.getElementById('m-image').src = el.getAttribute('data-image');
    
    // --- 显示模态框 ---
    // 移除 hidden 类，确保 display 属性正确设置
    document.getElementById('modalOverlay').style.display = 'block';
    document.getElementById('modalOverlay').classList.remove('hidden');
    
    document.getElementById('productModal').style.display = 'flex'; // 使用 flex 布局保持居中
    document.getElementById('productModal').classList.remove('hidden');
}

/**
 * 关闭模态框
 */
function closeModal(){
    document.getElementById('modalOverlay').style.display = 'none';
    document.getElementById('productModal').style.display = 'none';
}

// ==========================================
// 2. Cart Functions (购物车逻辑)
// ==========================================

/**
 * 显示 Toast 轻提示
 * @param {string} msg - 要显示的消息文本
 */
function showToast(msg) {
    var toast = document.getElementById("toast-notification");
    if(toast) {
        toast.innerText = msg;
        toast.className = "show"; // 添加 class 触发 CSS 动画 (淡入上浮)
        
        // 3秒后自动移除 class，隐藏提示
        setTimeout(function(){ 
            toast.className = toast.className.replace("show", ""); 
        }, 3000);
    }
}

/**
 * 加入购物车 (AJAX)
 * 不刷新页面，后台更新 Session，前台更新角标
 */
function addToCart(){
    if(currentBookId == 0) return;

    // 构建表单数据
    var fd = new FormData();
    fd.append('book_id', currentBookId);
    
    // 发送 POST 请求到 add_to_cart.php
    fetch('add_to_cart.php', { method: 'POST', body: fd })
    .then(r => r.json()) // 解析返回的 JSON
    .then(d => {
        if(d.status === 'success'){
            // 更新右上角购物车数量
            var badge = document.getElementById('cart-badge');
            if(badge) badge.innerText = d.total;
            
            // 显示成功提示并关闭弹窗
            showToast("Book added to cart!");
            closeModal();
        }
    });
}

/**
 * 立即购买 (AJAX + 跳转)
 * 加入购物车成功后，直接跳转到 cart.php
 */
function buyNow(){
    if(currentBookId == 0) return;
    
    var fd = new FormData();
    fd.append('book_id', currentBookId);
    
    fetch('add_to_cart.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
        if(d.status === 'success'){
            window.location.href = 'cart.php'; // 跳转至购物车结算页
        }
    });
}

// ==========================================
// 3. Header Scroll Animation (顶部导航栏动画)
// ==========================================

// 监听窗口滚动事件，使用 requestAnimationFrame 进行防抖优化
window.addEventListener('scroll', function() {
    if (!ticking) {
        window.requestAnimationFrame(function() {
            const header = document.getElementById('main-header');
            if (header) {
                // 当滚动超过 50px 时，添加 .scrolled 类 (收起 Logo)
                if (window.scrollY > 50) {
                    header.classList.add('scrolled');
                } else {
                    // 回到顶部时，移除 .scrolled 类 (恢复 Logo)
                    header.classList.remove('scrolled');
                }
            }
            ticking = false; // 重置标志位
        });
        ticking = true;
    }
});

// ==========================================
// 4. Homepage Specific (首页特有动画)
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    
    // --- A. 打字机特效 (Typewriter Effect) ---
    // 仅当页面存在 'aboutUsSection' 时执行 (即仅在首页执行)
    const aboutSection = document.getElementById('aboutUsSection');
    if (aboutSection) {
        const elements = document.querySelectorAll('.js-typewriter');
        let hasStarted = false; // 确保动画只播放一次

        // 使用 IntersectionObserver 监听元素是否进入视口
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                // 当 About Us 区域进入屏幕 50% 时触发
                if (entry.isIntersecting && !hasStarted) {
                    hasStarted = true;
                    startTypingSequence(elements, 0);
                }
            });
        }, { threshold: 0.5 }); 
        
        observer.observe(aboutSection);

        // 递归函数：按顺序逐个段落播放打字动画
        function startTypingSequence(elements, index) {
            if (index >= elements.length) return; // 所有段落播放完毕

            const el = elements[index];
            const text = el.getAttribute('data-text'); // 获取要显示的文本
            el.classList.add('typing'); // 添加光标样式
            
            let charIndex = 0;
            
            // 单个字符输出函数
            function typeChar() {
                if (charIndex < text.length) {
                    el.textContent += text.charAt(charIndex);
                    charIndex++;
                    setTimeout(typeChar, 30); // 打字速度 (30ms/字)
                } else {
                    // 当前段落打完，移除光标
                    el.classList.remove('typing');
                    // 延迟 300ms 后开始下一段
                    setTimeout(() => { 
                        startTypingSequence(elements, index + 1); 
                    }, 300);
                }
            }
            typeChar();
        }
    }

    // --- B. 团队成员上滑动画 (Team Animation) ---
    const teamMembers = document.querySelectorAll('.team-member');
    if (teamMembers.length > 0) {
        const teamObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                // 当成员卡片进入视口 20% 时触发
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible'); // 添加 visible 类触发 CSS transition
                    teamObserver.unobserve(entry.target);  // 动画只播放一次，停止观察
                }
            });
        }, { threshold: 0.2 }); 
        
        teamMembers.forEach(member => {
            teamObserver.observe(member);
        });
    }
    
});

// ... (之前的代码) ...

    // ==========================================
    // [新增] Feedback Form Submission (AJAX)
    // ==========================================
    const feedbackForm = document.getElementById('feedbackForm');
    if (feedbackForm) {
        feedbackForm.addEventListener('submit', function(e) {
            e.preventDefault(); // 阻止默认表单跳转

            const formData = new FormData(this);
            const submitBtn = this.querySelector('.send-btn');
            const originalBtnText = submitBtn.innerHTML;

            // 禁用按钮防止重复提交
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Sending...';

            fetch('submit_feedback.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message); // 或者使用 showToast(data.message)
                    feedbackForm.reset(); // 清空表单
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                // 恢复按钮状态
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    }; // 结束 DOMContentLoaded