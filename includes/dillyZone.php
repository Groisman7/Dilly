<?php
session_set_cookie_params(86400);
session_start();
if (!isset($_SESSION['username'])) {
    header("Location:../includes/login.php");
    exit;
}
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DillyZone - Voucher Marketplace</title>
  <link rel="stylesheet" href="../css/home.css">
  <link rel="stylesheet" href="../css/profile.css">
  <link rel="stylesheet" href="../css/dillyZone.css">
  <script src="https://www.paypal.com/sdk/js?client-id=AUr07MIiQj1TLqCrs_rWJ-ATEzfXtdK5WMtwEi-_l_WaObsbwGE4CDRz8PQ34Iq4kBwMPPGjJL74PAI4&currency=ILS"></script>
</head>
<body>
  <div class="min-h-screen bg-main text-primary font-sans">
    <!-- Navigation -->
    <nav>
        <div class="container nav-content auth-buttons">
            <img src="../images/dillySmallLogo.png" alt="Dilly Voucher Hub" class="logo">
            <div class="nav-links">
                <a href="../index.php" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                      <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                      <polyline points="9 22 9 12 15 12 15 22"></polyline></svg> Home</a>
                <a href="dillyZone.php" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                      <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                      <path d="M3 6h18"></path>
                      <path d="M16 10a4 4 0 0 1-8 0"></path></svg>  DillyZone</a>
                <a href="profile.php" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                      <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                      <circle cx="12" cy="7" r="4"></circle></svg> Profile</a>
                    </svg>
                    
                 </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto py-8">
      <h1 class="text-4xl font-bold mb-4 text-center">Welcome to DillyZone</h1>
      <h3 class="text-xl text-center mb-10">Your ultimate marketplace for buying, selling, and discovering vouchers that save you more</h3>

      <!-- Filters -->
      <!--<div class="flex gap-4 mb-10">-->
      <!--  <input type="text" id="searchInput" placeholder="Search vouchers..." class="search-input">-->
      <!--  <select id="categoryFilter" class="filter-select">-->
      <!--    <option value="all">All Categories</option>-->
      <!--    <option value="food">Food & Dining</option>-->
      <!--    <option value="retail">Retail</option>-->
      <!--    <option value="entertainment">Entertainment</option>-->
      <!--  </select>-->
      <!--</div>-->

      <!-- MY VOUCHERS SECTION -->
      <section class="section-wrapper my-vouchers-section">
        <div class="section-header">
          <h2 class="section-title">My Vouchers for Sale 🏷️</h2>
          <button class="toggle-section" onclick="toggleSection('myVouchersGrid', this)">−</button>
        </div>
        <div id="myVouchersGrid" class="vouchers-grid"></div>
      </section>

      <!-- MARKETPLACE SECTION -->
      <section class="section-wrapper marketplace-section">
        <div class="section-header">
          <h2 class="section-title">Explore the Marketplace 🛍️</h2>
          <button class="toggle-section" onclick="toggleSection('marketVouchersGrid', this)">−</button>
        </div>
        <div id="marketVouchersGrid" class="vouchers-grid"></div>
      </section>
    </div>

    <!-- Footer -->
    <footer class="bg-secondary text-white py-8 mt-16">
      <div class="container mx-auto text-center">
        <p>© 2025 Dilly. All rights reserved.</p>
      </div>
    </footer>
  </div>

<script src="https://cdn.tailwindcss.com"></script>
<script>
function showSuccessMessage(message, duration = 3000) {
  const msg = document.createElement('div');
  msg.textContent = message;
  msg.style.position = 'fixed';
  msg.style.top = '50%';
  msg.style.left = '50%';
  msg.style.transform = 'translate(-50%, -50%)';
  msg.style.backgroundColor = '#38a169'; // ירוק נעים
  msg.style.color = 'white';
  msg.style.padding = '16px 24px';
  msg.style.borderRadius = '12px';
  msg.style.fontSize = '18px';
  msg.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
  msg.style.zIndex = '9999';
  msg.style.textAlign = 'center';

  document.body.appendChild(msg);

  setTimeout(() => {
    msg.remove();
  }, duration);
}


</script>
<script>
// 🛒 טוען שוברים למכירה
async function loadMyVouchersForSale() {
  try {
    const res = await fetch('/includes/load_vouchers.php?onlySellable=1', { credentials: 'include' });
    const data = await res.json();

    if (data.success && Array.isArray(data.vouchers)) {
      const myVouchersGrid = document.getElementById('myVouchersGrid');
      if (data.vouchers.length === 0) {
        myVouchersGrid.innerHTML = '<div class="text-center text-gray-500">No vouchers for sale yet.</div>';
      } else {
        myVouchersGrid.innerHTML = data.vouchers.map(voucher => `
          <div class="voucher-card p-4 bg-white rounded shadow-md flex flex-col items-center space-y-2">
            <img src="${voucher.businessLogo}" alt="${voucher.businessName}" class="w-16 h-16 object-cover rounded-full">
            <div class="text-lg font-semibold">${voucher.businessName}</div>
            <div class="text-sm text-gray-500">Balance: ₪${voucher.balance}</div>
            <div class="text-sm text-gray-500">Expires: ${voucher.expiryDate}</div>
          </div>
        `).join('');
      }
    } else {
      console.error('❌ Failed to load vouchers:', data.message);
    }
  } catch (err) {
    console.error('❌ Error loading vouchers:', err);
  }
}

async function loadMarketplaceVouchers() {
  try {
    const res = await fetch('/includes/load_vouchers.php?onlySellable=1&excludeSelf=1', { credentials: 'include' });
    const data = await res.json();
    const grid = document.getElementById('marketVouchersGrid');

    if (data.success && Array.isArray(data.vouchers)) {
      grid.innerHTML = data.vouchers.length === 0
        ? '<div class="text-center text-gray-500">No marketplace vouchers available.</div>'
        : data.vouchers.map(v => `
            <div class="voucher-card p-4 bg-white rounded shadow-md flex flex-col items-center space-y-2">
              <img src="${v.businessLogo}" alt="${v.businessName}" class="w-16 h-16 object-cover rounded-full">
              <div class="text-lg font-semibold">${v.businessName}</div>
              <div class="text-sm text-gray-500">Balance: ₪${v.balance}</div>
              <div class="text-sm text-gray-500">Expires: ${v.expiryDate}</div>
              <div id="paypal-button-container-${v.id}" class="mt-2 w-full"></div>
            </div>
          `).join('');

     data.vouchers.forEach(v => {
      // ודא שהbalance מספר תקין
      const price = parseFloat(v.balance);
      const validPrice = isNaN(price) || price <= 0 ? 1.00 : price.toFixed(2); // ערך ברירת מחדל: ₪1.00
    
      paypal.Buttons({
        createOrder: function(data, actions) {
          return actions.order.create({
            purchase_units: [{
              amount: {
                value: validPrice
              }
            }]
          });
        },
        onApprove: function(data, actions) {
          return actions.order.capture().then(function(details) {
            // שליחת בקשה לעדכון הבעלות של השובר
            fetch('/includes/update_vouchers.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json'
              },
              credentials: 'include',
              body: JSON.stringify({
                id: v.id
              })
            })
            .then(res => res.json())
            .then(result => {
              if (result.success) {
                showSuccessMessage('✅ הרכישה הצליחה והשובר עבר לבעלותך!', 5000);
                setTimeout(() => {
                  window.location.href = 'profile.php';
                }, 3000);
                 
              } else {
                alert('❌ שגיאה בעדכון השובר: ' + result.message);
              }
            })
            .catch(err => {
              console.error('❌ שגיאה בפנייה לשרת:', err);
              alert('❌ שגיאה טכנית בעדכון השובר.');
            });
          });
        },
        onCancel: function(data) {
          alert('התשלום בוטל.');
        },
        onError: function(err) {
          console.error('PayPal error:', err);
          alert('❌ שגיאה במהלך תשלום ב-PayPal.');
        }
      }).render('#paypal-button-container-' + v.id);
    });


    } else {
      console.error('❌ Failed to load marketplace vouchers:', data.message);
    }
  } catch (err) {
    console.error('❌ Error loading marketplace vouchers:', err);
  }
}


// 🔄 קיפול / פתיחה של הסקשן
function toggleSection(sectionId, button) {
  const section = document.getElementById(sectionId);
  if (section.style.display === 'none') {
    section.style.display = 'grid';
    button.textContent = '−';
  } else {
    section.style.display = 'none';
    button.textContent = '+';
  }
}
// 📦 התחלת טעינת הדף
loadMyVouchersForSale();
loadMarketplaceVouchers();
</script>
<script>
      // מקבל את הנתיב של הדף הנוכחי
      const currentPath = window.location.pathname.split("/").pop(); 
    
      // עובר על כל הקישורים בתפריט
      document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
          link.classList.add('active'); // מוסיף את המחלקה active
        }
      });
    </script>
<div id="successToast" style="
  display: none;
  position: fixed;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  background-color: #38a169;
  color: white;
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 16px;
  z-index: 9999;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
"></div>

</body>
</html>
