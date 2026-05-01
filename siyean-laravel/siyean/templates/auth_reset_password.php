<form method="post" action="/reset-password">
  <p class="chip">Set new password</p>
  <h1>Reset password</h1>
  <p style="color:#cbd5f5;">Choose a new password for <strong><?= htmlspecialchars($email ?? '') ?></strong>.</p>

  <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>" />
  <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>" />

  <label>New password
    <input type="password" name="password" placeholder="At least 8 characters" required minlength="8" />
  </label>
  <label>Confirm password
    <input type="password" name="password_confirmation" placeholder="Re-enter new password" required minlength="8" />
  </label>

  <button type="submit">Reset password</button>
  <p style="margin-top:1rem;color:#94a3b8;font-size:0.9rem;">
    <a href="/login" style="color:#93c5fd;">Back to sign in</a>
  </p>
</form>
