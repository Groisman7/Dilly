window.addEventListener("DOMContentLoaded", function () {

  const searchInput = document.getElementById("searchInput");
  const categoryFilter = document.getElementById("categoryFilter");
  const myVouchersGrid = document.getElementById("myVouchersGrid");
  const marketVouchersGrid = document.getElementById("marketVouchersGrid");

  function renderVouchers() {
    const searchTerm = searchInput.value.toLowerCase();
    const category = categoryFilter.value;

    const filterFn = function (v) {
      const matchesSearch = v.title.toLowerCase().includes(searchTerm);
      const matchesCategory = category === "all" || v.category === category;
      return matchesSearch && matchesCategory;
    };

    const filteredMarket = vouchers.filter(filterFn);
    const filteredMine = myVouchers.filter(filterFn);

    myVouchersGrid.innerHTML = generateHTML(filteredMine, true);
    marketVouchersGrid.innerHTML = generateHTML(filteredMarket, false);
  }

 function generateHTML(list, isMine) {
  return list.map(function (voucher) {
    var html = '';
    html += '<div class="voucher-card p-4 bg-white rounded shadow-md flex flex-col items-center space-y-2">';
    html +=   '<img src="' + voucher.image + '" alt="' + voucher.title + '" class="w-16 h-16 object-cover rounded-full">';
    html +=   '<div class="text-lg font-semibold">' + voucher.title + '</div>';
    html +=   '<div class="text-sm text-gray-500">Price: $' + voucher.price + '</div>';
    html +=   '<div class="text-sm text-gray-500">Discount: ' + voucher.discount + '</div>';
    html +=   '<div class="text-sm text-gray-500">Expires: ' + voucher.expiry + '</div>';
    if (isMine) {
      html += '<button class="buy-button mt-2" onclick="alert(\'Removed from sale: ' + voucher.title + '\')">Remove</button>';
    } else {
      html += '<button class="buy-button mt-2" onclick="handleBuy(' + voucher.id + ')">Buy Now</button>';
    }
    html += '</div>';
    return html;
  }).join('');
}


  function handleBuy(voucherId) {
    const voucher = vouchers.find(function (v) { return v.id === voucherId; });
    alert("Processing purchase for: " + voucher.title);
  }

  searchInput.addEventListener("input", renderVouchers);
  categoryFilter.addEventListener("change", renderVouchers);

  renderVouchers();
});

const currentPath = window.location.pathname.split("/").pop();
document.querySelectorAll('.nav-link').forEach(link => {
  if (link.getAttribute('href').includes(currentPath)) {
    link.classList.add('active');
  }
});

function toggleSection(gridId, button) {
  const grid = document.getElementById(gridId);
  if (!grid) return;

  if (grid.style.display === 'none') {
    grid.style.display = 'grid';
    button.textContent = '−';
  } else {
    grid.style.display = 'none';
    button.textContent = '+';
  }
}





