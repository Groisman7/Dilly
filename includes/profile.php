<?php
session_set_cookie_params(86400); // יום אחד
session_start();
$username = $_SESSION['username'] ?? null;
if (!isset($_SESSION['username'])) {
    header("Location:../includes/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dilly Profile</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/profile.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
                 <a href="notifications.php" title="Notifications Setting" class="notification-button nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                      <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                    </svg>
                    
                 </a>
            </div>
        </div>
    </nav>

      <div class="container mx-auto py-8">
        <h1 class="text-4xl font-bold mb-8 text-center"><?php echo htmlspecialchars($username); ?> Wallet</h1>
        
        <!-- Upload Voucher Section -->
    <div class="profile-section">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Upload Voucher</h2>
      </div>
    
      <form id="voucherForm" class="space-y-4">
        <div class="form-group">
          <input
            type="text"
            id="voucherNumber"
            class="w-80 p-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary"
            placeholder="Enter your voucher code here..."
          >
        </div>
    
        <div class="button-group mt-4 justify-end">
          <button type="submit" class="w-full primary-button flex items-center gap-2 justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
              <line x1="7" y1="7" x2="7.01" y2="7"></line>
            </svg>
            Add Voucher
          </button>
        </div>
      </form>
    </div>

        
        <!-- Vouchers List -->
        <div class="profile-section mt-8">
          <h2 class="text-2xl font-semibold mb-6">My Vouchers</h2>
          <div id="vouchersList" class="vouchers-grid"></div>
        </div>
      </div>
      
      <!-- Footer -->
      <footer class="bg-main py-8 text-center">
        <p class="text-primary-light">© 2025 Dilly. All rights reserved.</p>
      </footer>
    </div>
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
    <script src="../js/profile.js"></script>
 </body>
</html>