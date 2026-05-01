<form method="post" action="/login">
  <p class="chip">Secure access</p>
  <h1>Sign in to Mac POS</h1>
  <p style="color:#cbd5f5;">Admins and clerks can manage inventory, sales, and reports from here.</p>
  <label>Email
    <input type="email" name="email" placeholder="you@store.com" required autofocus />
  </label>
  <label>Password
    <input type="password" name="password" placeholder="••••••••" required />
  </label>
  <p style="margin-top:0.5rem;margin-bottom:0.25rem;">
    <a href="/forgot-password" style="color:#93c5fd;font-size:0.9rem;">Forgot password?</a>
  </p>
  <button type="submit">Sign in</button>
  <p style="margin-top:1rem;color:#94a3b8;font-size:0.85rem;">
    Need access? On the server, from the <code>siyean-laravel/</code> folder:
    <code>php scripts/list_users.php</code>,
    <code>php scripts/reset_password.php</code>,
    <code>php scripts/create_user.php</code>.
  </p>
</form>

