<?php include('0-0-header.php') ?>

<style>
  .fl-layout-container {
    display: flex;
    gap: 20px;
  }

  /* Left side – 3 parts */
  .fl-content-left {
    flex: 3;
  }

  /* Desktop right sidebar */
  .fl-content-right-desktop {
    flex: 2;
    position: sticky;
    top: 120px;
    height: fit-content;
    background: #f7f7f7;
    padding: 20px;
    border-radius: 6px;
    border: 1px solid #ddd;
  }

  /* Mobile right sidebar (hidden on desktop) */
  .fl-content-right-mobile {
    display: none;
    background: #f7f7f7;
    padding: 20px;
    border-radius: 6px;
    border: 1px solid #ddd;
    margin-bottom: 30px;
  }
@media(min-width: 991px) {
    /* Hide mobile version on desktop */
    .fl-content-right-mobile {
      display: none;
    }
    
    /* Show desktop version on desktop */
    .fl-content-right-desktop {
      display: block;
    }
  }
  @media(max-width: 992px) {
    .fl-layout-container {
      flex-direction: column;
    }
    
    /* Hide desktop sidebar on mobile */
    .fl-content-right-desktop {
      display: none;
    }
    
    /* Show mobile sidebar on top */
    .fl-content-right-mobile {
      display: block;
    }
    
    /* Desktop sidebar still visible in normal flow */
    .fl-content-right-desktop {
      position: relative;
      top: 0;
      display: block; /* Show it again below content */
      margin-top: 30px;
      order: 2; /* Make sure it appears after left content */
    }
    
    .fl-content-left {
      order: 1;
    }
  }

  

  .slider-container {
    overflow: hidden;
    width: 100%;
    position:  relative;
  }

  .slide-track {
    display: flex;
    transition: transform 0.3s ease;
  }

  .slide-track img {
    width: calc(100% / 3);
    flex-shrink: 0;
    object-fit: cover;
    padding:5px;

  }

  .prev, .next {
    position: absolute;
    top: 50%;
    background: #e45c00;
    transform: translateY(-50%);
    color: #fff;
    border: none;
    padding: 10px;
    cursor: pointer;
    border-radius: 6px;
    opacity: 0.7;
    z-index: 10;
  }

  .prev:hover, .next:hover { opacity: 1; background: #770cc5; }

  .prev { left: 10px; }
  .next { right: 10px; }

  /* Mobile: only 1 image visible */
  @media (max-width: 768px) {
    .slide-track img {
      width: 100%;
    }
  }
  /* Buttons */
  .slide-btn {
    background: #e45c00;
    color: #fff;
    border: none;
    font-size: 30px;
    padding: 8px 15px;
    cursor: pointer;
    opacity: 0.8;
  }

  .slide-btn:hover {
    opacity: 1;
    background: #770cc5;
  }

  .fl-select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    margin-top: 10px;
  }

  .fl-box {
    background: #fff;
    padding: 15px;
    border-radius: 6px;
    border: 1px solid #ddd;
    margin-top: 20px;
  }

  .fl-label {
    font-weight: bold;
    margin-top: 15px;
    display: block;
  }

  /* Default: No scrollbar when screen height is 900px or more */
  .scroll-box {
    max-height: none;
    overflow-y: visible;
  }

  /* Screen height BELOW 900px → enable scroll with auto-adaptive height */
  @media (max-height: 900px) {
    .scroll-box {
        max-height: calc(100vh - 30vh); 
        overflow-y: auto;
        padding-right: 10px;
    }
  }

  /* Scrollbar styling (Chrome, Edge, Safari) */
  .scroll-box::-webkit-scrollbar {
    width: 2px;
  }

  .scroll-box::-webkit-scrollbar-track {
    background: #e4e4e4;
    border-radius: 4px;
  }

  .scroll-box::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
  }

  .scroll-box::-webkit-scrollbar-thumb:hover {
    background: #555;
  }
  
.timeline {
    max-width: 600px;
    margin: 40px auto;
    padding-left: 20px;
    border-left: 3px solid #ddd;
}

.timeline-item {
    margin-bottom: 40px;
    position: relative;
}

.timeline-item::before {
    content: "";
    width: 14px;
    height: 14px;
    background: #4285f4;
    border-radius: 50%;
    position: absolute;
    left: -12px;
    top: 12px;
}

.timeline-item img {
    width: 100%;
    border-radius: 10px;
    margin-top: 10px;
}
.timeline-item h4 {
    margin: 0 0 10px 10px;
}
 
</style>

<div class="template-content">
  <div class="template-content-section template-padding-top-reset template-main">
    <div class="template-component-header-subheader">
      <h2>Rebuild Sri Lanka</h2>
      <h6>A united humanitarian mission to support flood-affected families across the island</h6>
    </div>
    <div class="template-post-section-icon">
      <div class="template-post-icon template-post-icon-sticky"></div>
    </div>

    <div class="template-post-section-preambule">
      <div class="template-component-nivo-slider template-component-nivo-slider-style-2 template-preloader">
        <div>
          <img src="../media/gallery/sri-lanka-flood-1.jfif" alt="Sri Lanka Flood 2025">
          <img src="../media/gallery/sri-lanka-flood-2.avif" alt="Sri Lanka Flood 2025">
          <img src="../media/gallery/sri-lanka-flood-3.webp" alt="Sri Lanka Flood 2025">
        </div>
      </div>
      <div class="template-component-divider template-component-divider-style-1"></div>
    </div>

    <!-- MOBILE VERSION - TOP POSITION -->
    <div class="fl-content-right-mobile">
      <div class="payment-section scroll-box">
        <p>Your contribution helps deliver food, medicine, and essential aid to affected families.</p>

        <!-- PAYMENT TYPE DROPDOWN -->
        <label class="fl-label">Select Payment Type</label>
        <select id="fl-payment-type-mobile" class="fl-select">
          <option value="bank">Bank Transfer</option>
          <option value="paypal">PayPal</option>
          <option value="wise">TransferWise</option>
        </select>

        <!-- COUNTRY DROPDOWN -->
        <div id="fl-country-box-mobile">
          <label class="fl-label">Select Country</label>
          <select id="fl-country-mobile" class="fl-select">
            <option value="sl">Sri Lanka</option>
            <option value="eu">Europe</option>
            <option value="usa">USA</option>
            <option value="uk">UK</option>
            <option value="ca">Canada</option>
            <option value="au">Australia</option>
          </select>
        </div>

        <!-- RESULT BOX -->
        <div id="fl-result-mobile" class="fl-box">
          <!-- Content will be populated by JavaScript -->
        </div>
      </div>
    </div>

    <div class="fl-layout-container">
      <!-- LEFT SIDE CONTENT (3:1 Layout) -->
      <div class="fl-content-left">
        <div class="template-post">
          <div class="template-post-section-content">
            <div class="template-post-content">
              <h4 class="template-margin-top-3">Sri Lanka Flood Relief Appeal</h4>

              <h6 class="template-margin-top-3">Sri Lanka Faces an Unprecedented Humanitarian Crisis</h6>
              <p align="justify">
                Sri Lanka is facing one of the deadliest and most destructive disasters in recent history. Cyclone Ditwah and the island-wide floods have left communities devastated, homes destroyed, and thousands of families fighting for survival.
              </p>

              <h6 class="template-margin-top-3">The Reality on the Ground</h6>
              <p align="justify">
                According to the latest national updates:
                <br>• <strong>465 lives lost</strong>
                <br>• <strong>366 people still missing</strong>
                <br>• <strong>147,000+ people displaced</strong>
                <br>• <strong>25,000+ homes destroyed or damaged</strong>
                <br>• <strong>Over 1 million people affected island-wide</strong>
              </p>

              <p align="justify">
                Entire neighbourhoods have been washed away or buried under mud. Districts including Colombo, Gampaha, Kandy, Nuwara Eliya, Badulla, Batticaloa, Mullaitivu, Trincomalee, Ampara, Ratnapura, and Monaragala have suffered catastrophic damage. Many regions remain without electricity, drinking water, or communication.
              </p>

              <p align="justify">
                Remote villages, including island communities, are accessible only by boat, leaving families trapped without food, medicine, or basic essentials.
              </p>

              <h6 class="template-margin-top-3">Government Support — Not Enough for the Scale of the Crisis</h6>
              <p align="justify">
                The Government has allocated LKR 50 million (USD 150,000) per Divisional Secretariat for immediate emergency response. While this support is essential, the needs on the ground are far greater:
                <br>• Thousands require daily meals and dry rations
                <br>• Children urgently need milk powder, baby food, clothing
                <br>• Families lack bedsheets, mats, sanitary items, medicine
                <br>• Many communities require boats to access relief
                <br>• Livelihoods have been destroyed, requiring long-term rebuilding
              </p>

              <p align="justify">
                No single system can handle a crisis of this magnitude alone. Public support is critical.
              </p>

              <h6 class="template-margin-top-3">What DreamSpace Academy Is Doing</h6>
              <p align="justify">
                DreamSpace Academy, together with KathiraGreens, United Catalyst Network, and dedicated grassroots teams, is actively delivering support in some of the hardest-hit and most inaccessible regions.
              </p>

              <p align="justify">
                We are currently providing:
                <br>• Dry rations & meal packs
                <br>• Medical support & first-aid
                <br>• Sanitation & hygiene essentials
                <br>• Drinking water
                <br>• Baby food & milk powder
                <br>• Clothes, mats & bedsheets
                <br>• Emergency boat access for isolated communities
                <br>• Livelihood recovery support for post-flood rebuilding
              </p>

              <p align="justify">
                We have already supported over 100 families, but thousands more are waiting for urgent help. Every contribution helps us reach another family in need.
              </p>
              <div class="slider-container">
                <button class="prev">◀</button>

                <div class="slide-track">
                  <img src="../media/gallery/sri-lanka-flood-help-1.jpg" alt="Sri lanka flood relief 1">
                  <img src="../media/gallery/sri-lanka-flood-help-2.jpg" alt="Sri lanka flood relief 2">
                  <img src="../media/gallery/sri-lanka-flood-help-3.jpg" alt="Sri lanka flood relief 3">
                  <img src="../media/gallery/sri-lanka-flood-help-4.jpg" alt="Sri lanka flood relief 4">
                  <img src="../media/gallery/sri-lanka-flood-help-5.jpg" alt="Sri lanka flood relief 5">
                  <img src="../media/gallery/sri-lanka-flood-help-6.jpg" alt="Sri lanka flood relief 6">
                  <img src="../media/gallery/sri-lanka-flood-help-7.jpg" alt="Sri lanka flood relief 7">
                </div>

                <button class="next">▶</button>
              </div>

              <h6 class="template-margin-top-3">You Can Help Save Lives</h6>
              <p align="justify">
                This is a moment for compassion and collective responsibility. Your generosity can help a family eat, stay safe, regain dignity, and begin rebuilding their lives.
              </p>

              <p align="justify">
                <strong>Donate Now. Stand With Sri Lanka. Restore Hope.</strong><br>
                Together, we can rebuild lives and strengthen communities through this crisis.
              </p>
            </div>
          </div>
        </div>
        <h3>Donation - Income & Expense Report</h3>
        <iframe
          width="100%"
          height="600"
          src="https://docs.google.com/spreadsheets/d/e/2PACX-1vQMiNowqfEJn35hOny3vADtNfKgBq2vFal2VwnJ3HvicdDifCubx7RfnS_WW8cRqw-83UNoPhHfnPyJ/pubhtml?gid=0&single=true&widget=true&headers=false"></iframe>
          
        <h3>Timeline</h3>
        <div class="timeline">
    <div class="timeline-item">
        <h4>4 Dec 2025</h4>
        <img src="../media/gallery/flood-timeline-1.jpeg" alt="Step 1 Image">
    </div>

    <div class="timeline-item">
        <h4>3 Dec 2025</h4>
        <img src="../media/gallery/flood-timeline-2.jpeg" alt="Step 2 Image">
    </div>

    <div class="timeline-item">
        <h4>1 Dec 2025</h4>
        <img src="../media/gallery/flood-timeline-3.jpeg" alt="Step 3 Image">
    </div>
    <div class="timeline-item">
        <h4>30 Nov 2025</h4>
        <img src="../media/gallery/flood-timeline-4.jpeg" alt="Step 4 Image">
    </div>

</div>
      </div>

      <!-- DESKTOP VERSION - RIGHT SIDE STICKY (1-part) -->
      <div class="fl-content-right-desktop">
        <div class="payment-section scroll-box">
          <p>Your contribution helps deliver food, medicine, and essential aid to affected families.</p>

          <!-- PAYMENT TYPE DROPDOWN -->
          <label class="fl-label">Select Payment Type</label>
          <select id="fl-payment-type-desktop" class="fl-select">
            <option value="bank">Bank Transfer</option>
            <option value="paypal">PayPal</option>
            <option value="wise">TransferWise</option>
          </select>

          <!-- COUNTRY DROPDOWN -->
          <div id="fl-country-box-desktop">
            <label class="fl-label">Select Country</label>
            <select id="fl-country-desktop" class="fl-select">
              <option value="sl">Sri Lanka</option>
              <option value="eu">Europe</option>
              <option value="usa">USA</option>
              <option value="uk">UK</option>
              <option value="ca">Canada</option>
              <option value="au">Australia</option>
            </select>
          </div>

          <!-- RESULT BOX -->
          <div id="fl-result-desktop" class="fl-box">
            <!-- Content will be populated by JavaScript -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // JavaScript for the slider functionality
  const track = document.querySelector(".slide-track");
  const prev = document.querySelector(".prev");
  const next = document.querySelector(".next");

  const totalImages = document.querySelectorAll(".slide-track img").length;
  let visibleImages = window.innerWidth <= 768 ? 1 : 3;
  let index = 0;

  function updateSlide() {
    track.style.transform = `translateX(-${(100 / visibleImages) * index}%)`;
  }

  next.addEventListener("click", () => {
    index++;
    if (index > totalImages - visibleImages) index = 0;
    updateSlide();
  });

  prev.addEventListener("click", () => {
    index--;
    if (index < 0) index = totalImages - visibleImages;
    updateSlide();
  });

  window.addEventListener("resize", () => {
    visibleImages = window.innerWidth <= 768 ? 1 : 3;
    updateSlide();
  });

  // Payment dropdown functionality - NO FORM SUBMISSION
  const paymentTypes = {
    mobile: document.getElementById("fl-payment-type-mobile"),
    desktop: document.getElementById("fl-payment-type-desktop")
  };

  const countries = {
    mobile: document.getElementById("fl-country-mobile"),
    desktop: document.getElementById("fl-country-desktop")
  };

  // Data arrays
  const bankData = {
    'sl': `
      <h4>Sri Lanka</h4>
      <b>Account Name:</b> DreamSpace Foundation<br>
      <b>Bank Name:</b> Bank of Ceylon<br>
      <b>Account Number:</b> 88669857<br>
      <b>Branch / Routing:</b> 426 - Kallady<br>
      <b>SWIFT Code:</b> BCEYLKLX<br><br>
      <b>Organisation:</b> Guarantee Limited (Non-Profit)<br>
      <b>Address:</b> 50 New Kalmunai Road, Batticaloa 30000, Sri Lanka
    `,
    'eu': `
      <h4>Europe</h4>
      <b>Account Name:</b> DreamSpace Foundation gUG<br>
      <b>Bank Name:</b> GLS Gemeinschaftsbank<br>
      <b>IBAN:</b> DE26 4306 0967 1322 7554 00<br><br>
      <b>Organisation:</b> Gemeinnützige Unternehmergesellschaft (Non-Profit)<br>
      <b>Address:</b> Hubertusstr. 5, 12163 Berlin, Germany
    `,
    'usa': `
      <h4>USA</h4>
      <b>Account Name:</b> DreamSpace Foundation gUG<br>
      <b>Bank Name:</b> Community Federal Savings Bank<br>
      <b>Account Number:</b> 8314357954<br>
      <b>Account Type:</b> Checking<br>
      <b>Routing Code:</b> 026073150<br>
      <b>SWIFT Code:</b> CMFGUS33
    `,
    'uk': `
      <h4>UK</h4>
      <b>Account Name:</b> DreamSpace Foundation gUG<br>
      <b>Bank Name:</b> Wise Payments Limited<br>
      <b>Account Number:</b> 61272545<br>
      <b>Sort Code:</b> 23-14-70<br>
      <b>IBAN:</b> GB45 TRWI 2314 7061 2725 45<br>
      <b>SWIFT Code:</b> TRWIGB2LXXX
    `,
    'ca': `
      <h4>Canada</h4>
      <b>Account Name:</b> DreamSpace Foundation gUG<br>
      <b>Bank Name:</b> Wise Payments Canada Inc<br>
      <b>Account Number:</b> 200116145584<br>
      <b>Institution Number:</b> 621<br>
      <b>Transit Number:</b> 16001<br>
      <b>SWIFT Code:</b> TRWICAW1XXX
    `,
    'au': `
      <h4>Australia</h4>
      <b>Account Name:</b> DreamSpace Foundation gUG<br>
      <b>Bank Name:</b> Wise Australia Pty Ltd<br>
      <b>Account Number:</b> 218103907<br>
      <b>BSB Code:</b> 774-001<br>
      <b>SWIFT Code:</b> TRWIAUS1XXX
    `
  };

  const paypalData = `
    <h4>PayPal</h4>
    <b>Link:</b>
    <a href="https://www.paypal.com/paypalme/dreamspacefoundation" target="_blank" style="word-break: break-all;">
      paypal.com/paypalme/dreamspacefoundation
    </a><br><br>
    <b>QR:</b><br>
    <img src="../media/qr/paypal-qr-code.png" width="60%">
    </br>
    <b>Donate (Any Debit or Credit Card):</b></br></br>

					<div style="padding-left: 110px;">
						<form action="https://www.paypal.com/donate" method="post" target="_top">
							<input type="hidden" name="hosted_button_id" value="KEFGWUB7BQ622" />
							<input type="image" src="https://www.paypalobjects.com/en_US/DK/i/btn/btn_donateCC_LG.gif"
								border="0" name="submit" title="PayPal - The safer, easier way to pay online!"
								alt="Donate with PayPal button" />
							<img alt="" border="0" src="https://www.paypal.com/en_DE/i/scr/pixel.gif" width="1"
								height="1" />
						</form>
					</div>
  `;

  const wiseData = `
    <h4>TransferWise (Multi Currency)</h4>
    <b>Quick Pay Link:</b>
    <a href="https://wise.com/pay/business/dreamspacefoundationgug" 
   target="_blank" 
   style="word-break: break-all;">
  wise.com/pay/business/dreamspacefoundationgug
</a><br><br>
    <b>QR:</b><br>
    <img src="../media/qr/wise-quick-pay-qr-code.png" width="60%">
  `;

  // Show/hide country dropdown based on payment type
  function updateCountryBoxVisibility(paymentType) {
    const countryBoxMobile = document.getElementById("fl-country-box-mobile");
    const countryBoxDesktop = document.getElementById("fl-country-box-desktop");
    
    if (paymentType === "bank") {
      countryBoxMobile.style.display = "block";
      countryBoxDesktop.style.display = "block";
    } else {
      countryBoxMobile.style.display = "none";
      countryBoxDesktop.style.display = "none";
    }
  }

  // Update result box content
  function updateResultBox(paymentType, country) {
    const resultBoxMobile = document.getElementById("fl-result-mobile");
    const resultBoxDesktop = document.getElementById("fl-result-desktop");
    
    let content = '';
    
    if (paymentType === 'bank') {
      content = bankData[country] || bankData['sl'];
    } else if (paymentType === 'paypal') {
      content = paypalData;
    } else if (paymentType === 'wise') {
      content = wiseData;
    }
    
    resultBoxMobile.innerHTML = content;
    resultBoxDesktop.innerHTML = content;
  }

  // Initialize on page load
  document.addEventListener('DOMContentLoaded', function() {
    updateCountryBoxVisibility('bank');
    updateResultBox('bank', 'sl');
  });

  // Add event listeners for mobile
  paymentTypes.mobile.addEventListener("change", function() {
    paymentTypes.desktop.value = this.value;
    updateCountryBoxVisibility(this.value);
    updateResultBox(this.value, countries.mobile.value);
  });

  countries.mobile.addEventListener("change", function() {
    countries.desktop.value = this.value;
    if (paymentTypes.mobile.value === "bank") {
      updateResultBox('bank', this.value);
    }
  });

  // Add event listeners for desktop
  paymentTypes.desktop.addEventListener("change", function() {
    paymentTypes.mobile.value = this.value;
    updateCountryBoxVisibility(this.value);
    updateResultBox(this.value, countries.desktop.value);
  });

  countries.desktop.addEventListener("change", function() {
    countries.mobile.value = this.value;
    if (paymentTypes.desktop.value === "bank") {
      updateResultBox('bank', this.value);
    }
  });

  // LinkedIn posts navigation
  const posts = [
    "https://www.linkedin.com/embed/feed/update/urn:li:share:7401589840414490625?collapsed=1",
    "https://www.linkedin.com/embed/feed/update/urn:li:share:7402178417477218304?collapsed=1",
    "https://www.linkedin.com/embed/feed/update/urn:li:share:7401986001486979072?collapsed=1",
    "https://www.linkedin.com/embed/feed/update/urn:li:share:7400888670670684160?collapsed=1"
  ];

  let current = 0;

  function changePost(direction) {
    current = (current + direction + posts.length) % posts.length;
    document.getElementById("socialFrame").src = posts[current];
  }
</script>

<?php include('9-0-footer.php') ?>