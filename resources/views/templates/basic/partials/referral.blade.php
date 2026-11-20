 <div class="max-w-[200px] w-full bg-card-white rounded-2xl border border-border-gray overflow-hidden shadow-sm"> <div class="bg-brand-teal p-3 text-center">
    <div class="flex items-center justify-center gap-1.5 mb-0.5">
      <span class="text-sm">📧</span>
      <h3 class="text-white font-bold text-xs uppercase tracking-wide">Invite Friends</h3>
    </div>
    <p class="text-white text-[10px] leading-tight opacity-90">
      Share your link and earn rewards
    </p>
  </div>

  <div class="p-3 flex flex-col gap-2">
    <label class="text-brand-teal font-bold text-[11px]">
      Your Referral Link
    </label>

    <input
      id="referralLink"
      type="text"
      readonly
      value="{{ route('home') }}?reference={{ auth()->user()->username }}"
      class="w-full px-2 py-1.5 text-[10px] border border-border-gray rounded-lg bg-bg-main text-text-secondary focus:outline-none"
    />

    <button 
      onclick="copyReferralLink()"
      id="copyBtn"
      class="w-full bg-brand-gold hover:opacity-90 transition-opacity py-2 rounded-lg flex items-center justify-center gap-2 font-bold text-brand-teal text-xs"
    >
      <span id="btnIcon">📋</span>
      <span id="btnText">Copy</span>
    </button>
  </div>
</div>

<script>
function copyReferralLink() {
  const linkInput = document.getElementById('referralLink');
  const btnText = document.getElementById('btnText');
  const btnIcon = document.getElementById('btnIcon');
  const btn = document.getElementById('copyBtn');
  
  // Copy logic
  navigator.clipboard.writeText(linkInput.value).then(() => {
    // Visual Feedback
    btnText.innerText = 'Copied!';
    btnIcon.innerText = '✅';
    btn.style.backgroundColor = 'var(brand-teal)'; // Switch to Teal briefly
    btn.style.color = '#ffffff';

    setTimeout(() => {
      btnText.innerText = 'Copy';
      btnIcon.innerText = '📋';
      btn.style.backgroundColor = 'var(brand-gold)'; // Back to Gold
      btn.style.color = 'var(brand-teal)';
    }, 2000);
  });
}
</script>
