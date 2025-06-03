const vouchers = [];

const voucherForm = document.getElementById('voucherForm');
const voucherNumber = document.getElementById('voucherNumber');
const vouchersList = document.getElementById('vouchersList');

// 🟢 טעינת שוברים מהשרת
async function loadVouchersFromServer() {
  try {
    const res = await fetch('/includes/load_vouchers.php', { credentials: 'include' });
    const data = await res.json();

    if (data.success && Array.isArray(data.vouchers)) {
      vouchers.length = 0;
      data.vouchers.forEach(v => vouchers.push(v));
      renderVouchers();
    } else {
      console.error('❌ Failed to load vouchers:', data.message);
    }
  } catch (err) {
    console.error('❌ Error loading vouchers:', err);
  }
}


function renderVouchers() {
  vouchersList.innerHTML = vouchers.map(voucher => `
    <div class="voucher-card p-4 bg-white rounded shadow-md flex flex-col items-center space-y-2">
      <img src="${voucher.businessLogo}" alt="${voucher.businessName}" class="w-16 h-16 object-cover rounded-full">
      <div class="text-lg font-semibold">${voucher.businessName}</div>
      <div class="text-sm text-gray-500">Balance: ₪${voucher.balance}</div>
      <div class="text-sm text-gray-500">Expires: ${voucher.expiryDate}</div>

      <!-- כפתור SHOW CODE -->
      <button style="background-color: #586466;" class="show-code-btn hover:opacity-90 text-white px-3 py-1 rounded text-sm mt-2">
        SHOW CODE
      </button>
      <div class="voucher-code hidden text-black text-bg font-bold font-mono mt-1">${voucher.code}</div>

      <div class="text-center mt-2">
        <div class="text-sm mb-1">Sell this voucher?</div>
        <div class="text-2xl cursor-pointer sellable-toggle ${voucher.sellable ? 'text-green-500' : 'text-red-500'}"
            data-id="${voucher.id}" data-sellable="${voucher.sellable}">
          ${voucher.sellable == 1 ? '❌' : '💰'}
        </div>
      </div>
    </div>
  `).join('');

  // כפתור מכירה
  document.querySelectorAll('.sellable-toggle').forEach(button => {
    button.addEventListener('click', () => toggleSellable(parseInt(button.dataset.id)));
  });

  // כפתור הצגת קוד
  document.querySelectorAll('.show-code-btn').forEach((button, i) => {
    button.addEventListener('click', () => {
      const code = document.querySelectorAll('.voucher-code')[i];
      code.classList.toggle('hidden');
    });
  });
}




async function toggleSellable(id) {
  const voucherIndex = vouchers.findIndex(v => v.id === id);
  if (voucherIndex !== -1) {
    const voucher = vouchers[voucherIndex];
    const newSellable = voucher.sellable == 1 ? 0 : 1;

    const confirmSell = confirm(`Are you sure you want to ${newSellable ? 'sell' : 'cancel selling'} this voucher?`);
    if (!confirmSell) return;

    try {
      const response = await fetch('/includes/update_vouchers.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ id: voucher.id, sellable: newSellable })
      });

      const text = await response.text();
      console.log('🔵 Server raw response:', text);

      let data;
      try {
        data = JSON.parse(text);
      } catch (err) {
        console.error('❌ JSON parsing error:', err);
        alert('Server returned invalid JSON.');
        return;
      }

      if (data.success) {
        vouchers[voucherIndex].sellable = newSellable;
        renderVouchers();
      } else {
        console.error('❌ Server error:', data.message);
        alert('Failed to update voucher: ' + data.message);
      }
    } catch (err) {
      console.error('❌ Error during fetch:', err);
      alert('Network error occurred.');
    }
  }
}


// 📝 שליחת שובר חדש
voucherForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  const code = voucherNumber.value.trim();
  if (!code) {
    alert('Please enter a voucher number');
    return;
  }

  try {
    // שליחה ל-API בשרת
    const response = await fetch(`../BuyMe_demo/api.php?action=check&code=${encodeURIComponent(code)}`);
    const data = await response.json();
    console.log("API Response:", data); // הוספנו לוג עבור התגובה

    if (!data.success) {
      alert(data.message || 'Voucher not found');
      return;
    }

    // אם השובר כבר קיים
    const existingVoucher = vouchers.find(voucher => voucher.id === data.id);
    if (existingVoucher) {
      alert('This voucher is already added.');
      return;
    }

    // יצירת שובר חדש ללקוח
    const voucherData = {
      id: data.id, // מזהה זמני
      businessName: data.businessName,
      businessLogo: data.businessLogo,
      balance: data.balance,
      expiryDate: data.expiryDate,
      sellable: 0 // ברירת מחדל: לא למכירה
    };

    vouchers.push(voucherData);
    voucherNumber.value = '';
    renderVouchers();

    // שליחה לשרת לשמירה
    const saveRes = await fetch('/includes/save_voucher.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({
        code: code,
        businessName: data.businessName,
        businessLogo: data.businessLogo,
        balance: data.balance,
        expiryDate: data.expiryDate
      })
    });

    const rawText = await saveRes.text();
    console.log('Raw Response from server (save_voucher.php):', rawText); // לוג של התשובה מהשרת

    let saveData;
    try {
      saveData = JSON.parse(rawText);
    } catch (parseErr) {
      console.error('❌ Failed to parse JSON:', parseErr);
      alert('Server error: Invalid response.');
      return;
    }

    if (!saveData.success) {
      alert('Error saving voucher: ' + saveData.message);
    } else {
      console.log('✅ Voucher added successfully');
      // רענן את הדף אחרי הוספת השובר
      location.reload(); // רענון הדף
    }

  } catch (err) {
    console.error('❌ Error adding voucher:', err);
    alert('Something went wrong. Please try again.');
  }
});


// 🟢 התחלה: טעינת שוברים מהשרת
loadVouchersFromServer();
