document.addEventListener('DOMContentLoaded',()=>{
  const ids = ['settings-language','privacy-visibility','notif-push','notif-email','notif-popups','audio-muted','autoplay-videos','block-popups'];
  const profileDefaults = {
    firstName: 'John',
    lastName: 'Doe',
    displayName: 'John Doe',
    avatar: './images/logo/warner-bross-logo.png'
  };

  let selectedAvatarDataUrl = '';

  const profileFirstName = document.getElementById('profile-first-name');
  const profileLastName = document.getElementById('profile-last-name');
  const profileDisplayName = document.getElementById('profile-display-name');
  const profileAvatarInput = document.getElementById('profile-avatar-input');
  const profileAvatarPreview = document.getElementById('profile-avatar-preview');

  function readUI(){
    return {
      language: document.getElementById('settings-language').value,
      privacy: document.getElementById('privacy-visibility').value,
      profile: {
        firstName: profileFirstName.value.trim(),
        lastName: profileLastName.value.trim(),
        displayName: profileDisplayName.value.trim(),
        avatar: selectedAvatarDataUrl || profileAvatarPreview.src
      },
      notifPush: document.getElementById('notif-push').checked,
      notifEmail: document.getElementById('notif-email').checked,
      notifPopups: document.getElementById('notif-popups').checked,
      audioMuted: document.getElementById('audio-muted').checked,
      autoplayVideos: document.getElementById('autoplay-videos').checked,
      blockPopups: document.getElementById('block-popups').checked,
      restrictions: null,
      blocked: Array.from(document.querySelectorAll('#blockedList li')).map(li=>({id:li.dataset.id,name:li.firstChild.textContent.trim()}))
    };
  }

  function saveSettings(){
    const data = readUI();
    localStorage.setItem('app_settings',JSON.stringify(data));
    showToast('Instellingen opgeslagen');
  }

  function loadSettings(){
    const raw = localStorage.getItem('app_settings');
    if(!raw) return;
    try{
      const s = JSON.parse(raw);
      if(s.language) document.getElementById('settings-language').value = s.language;
      if(s.privacy) document.getElementById('privacy-visibility').value = s.privacy;
      const profile = { ...profileDefaults, ...(s.profile || {}) };
      profileFirstName.value = profile.firstName;
      profileLastName.value = profile.lastName;
      profileDisplayName.value = profile.displayName;
      profileAvatarPreview.src = profile.avatar;
      document.getElementById('notif-push').checked = !!s.notifPush;
      document.getElementById('notif-email').checked = !!s.notifEmail;
      document.getElementById('notif-popups').checked = !!s.notifPopups;
      const audioEnabled = document.getElementById('audio-enabled');
      if(audioEnabled) audioEnabled.checked = !!s.audioEnabled;
      document.getElementById('autoplay-videos').checked = (typeof s.autoplayVideos==='undefined')?true:!!s.autoplayVideos;
      document.getElementById('block-popups').checked = !!s.blockPopups;
      // audioMuted: default true (muted) when not present
      document.getElementById('audio-muted').checked = (typeof s.audioMuted==='undefined')?true:!!s.audioMuted;
      if(Array.isArray(s.blocked)){
        const ul = document.getElementById('blockedList'); ul.innerHTML='';
        s.blocked.forEach(b=>{
          const li = document.createElement('li'); li.dataset.id=b.id; li.innerHTML = `${b.name} <button class="btn" data-action="unblock">Deblokkeren</button>`; ul.appendChild(li);
        });
      }
    }catch(e){console.warn('Kon instellingen niet laden',e)}
  }

  function showToast(msg){
    // simple ephemeral message
    const d = document.createElement('div'); d.textContent = msg;
    d.style.position='fixed'; d.style.right='16px'; d.style.bottom='16px'; d.style.background='#222'; d.style.color='#fff'; d.style.padding='8px 12px'; d.style.borderRadius='8px'; d.style.opacity='0.95';
    document.body.appendChild(d); setTimeout(()=>d.remove(),2200);
  }

  [profileFirstName, profileLastName].forEach((el)=>{
    el.addEventListener('input',()=>{
      const composed = `${profileFirstName.value.trim()} ${profileLastName.value.trim()}`.trim();
      if(!profileDisplayName.value.trim()) profileDisplayName.value = composed;
    });
  });

  profileAvatarInput.addEventListener('change',()=>{
    const file = profileAvatarInput.files && profileAvatarInput.files[0];
    if(!file) return;

    const reader = new FileReader();
    reader.onload = ()=>{
      selectedAvatarDataUrl = String(reader.result || '');
      profileAvatarPreview.src = selectedAvatarDataUrl;
    };
    reader.readAsDataURL(file);
  });

  document.getElementById('save-settings').addEventListener('click',saveSettings);

  document.getElementById('download-data').addEventListener('click',()=>{
    const data = readUI();
    const blob = new Blob([JSON.stringify(data,null,2)],{type:'application/json'});
    const a = document.createElement('a'); a.href = URL.createObjectURL(blob);
    a.download = `settings-data-${new Date().toISOString().slice(0,19).replace(/[:T]/g,'-')}.json`;
    document.body.appendChild(a); a.click(); a.remove();
    showToast('Download gestart');
  });

  document.getElementById('delete-account').addEventListener('click',()=>{
    if(confirm('Weet je zeker dat je je account wilt verwijderen? Dit kan niet ongedaan gemaakt worden.')){
      // perform deletion flow (placeholder)
      localStorage.clear();
      showToast('Account verwijderd (simulatie)');
      setTimeout(()=>location.href='index.html',900);
    }
  });

  // Delegated unblock
  document.getElementById('blockedList').addEventListener('click',e=>{
    const btn = e.target.closest('button[data-action]'); if(!btn) return;
    const li = btn.closest('li'); if(!li) return;
    if(confirm(`Deblokkeer ${li.firstChild.textContent.trim()}?`)){
      li.remove(); showToast('Gebruiker gedeblokkeerd');
    }
  });

  // Save on toggle changes for immediate feedback
  ['notif-push','notif-email','notif-popups','audio-muted','autoplay-videos','block-popups','settings-language','privacy-visibility','profile-first-name','profile-last-name','profile-display-name'].forEach(id=>{
    const el = document.getElementById(id); if(!el) return; el.addEventListener('change',()=>localStorage.setItem('app_settings',JSON.stringify(readUI())));
  });

  loadSettings();
});
