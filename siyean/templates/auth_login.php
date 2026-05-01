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
  <button type="submit">Sign in</button>
  <p style="margin-top:1rem;color:#94a3b8;font-size:0.85rem;">
    Need access? On the server: <code>php scripts/list_users.php</code> shows accounts,
    <code>php scripts/reset_password.php</code> sets a new password,
    <code>php scripts/create_user.php</code> adds a user.
  </p>
</form>

