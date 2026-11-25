# Step-by-Step Procedure — Securing Against XSS
**Name:** Sean Linuz L. Barcelon  **Section:** UCOS 3-2
**Chosen attack:** Defacement (stored XSS)

This guide walks you through the whole activity: setting up the system, running
the defacement attack, fixing it with PHP, adding the password rules, and
pushing to GitHub. It tells you **exactly where to take each required
screenshot** so your Brightspace document has the minimum 3 images per section.

---

## PART 0 — Set up the system (do this once)

1. Install/open **XAMPP** and start **Apache** and **MySQL**.
2. Copy the project folder into `C:\xampp\htdocs\` and rename it `who-did-it`.
3. Open `http://localhost/phpmyadmin` → click the **SQL** tab → paste everything
   from `schema.sql` → click **Go**. You should see the `who_did_it` database
   with `users` and `activity_log` tables appear on the left.
4. Open `http://localhost/who-did-it/register.php` in your browser.
5. Register one normal account (e.g. username `sean`, password `Password1`) and
   log in. You now have a working **Login / Logout / Who Did It** system.

> Keep the browser and your code editor (VS Code) side by side — you will be
> switching between them and taking screenshots the whole time.

---

## PART 2 — Execute the defacement XSS attack (needs **3+ screenshots**)

You first make the page **vulnerable on purpose**, attack it, then (in Part 3)
fix it. This proves both the attack and the defense.

### 2.1 Make the feed vulnerable
1. Open `dashboard.php` in VS Code.
2. Find the feed loop. **Delete** these two *secured* lines:
   ```php
   <td><?= e($row['username']) ?></td>
   <td><?= e($row['action']) ?></td>
   ```
   and **replace** them with these *vulnerable* lines (raw output, no escaping):
   ```php
   <td><?php echo $row['username']; ?></td>
   <td><?php echo $row['action']; ?></td>
   ```
3. Save.

> 📸 **Screenshot 1** — your VS Code showing the vulnerable `echo` lines (the
> unescaped output). Caption: *"Vulnerable code: user input is printed directly
> with echo, without htmlspecialchars, so any HTML/JavaScript will run."*

### 2.2 Launch the defacement payload
1. Go back to the dashboard in the browser and log in.
2. Open `payloads.txt`, copy **payload #2** (the full-screen banner):
   ```html
   <script>document.body.innerHTML="<div style='background:black;color:lime;font-size:40px;text-align:center;padding:100px;font-family:monospace'>HACKED BY SEAN<br>XSS DEFACEMENT DEMO</div>";</script>
   ```
3. Paste it into the **Post** box and click **Post**.

> 📸 **Screenshot 2** — the moment you paste the payload into the post box
> (before submitting). Caption: *"Injecting the malicious script into the Who
> Did It feed as if it were a normal message."*

> 📸 **Screenshot 3** — the page **after** it reloads: the whole dashboard is
> replaced by the black/green "HACKED BY SEAN" banner. Caption: *"Stored XSS
> defacement succeeds — the injected script ran in the browser and replaced the
> page content. Because the post is stored, every user who opens the feed sees
> the defacement."*

> (Optional 4th shot) Log in as a different user and open the dashboard — it is
> defaced for them too. This shows it is **stored/persistent** XSS, not just a
> one-time thing.

---

## PART 3 — Implement PHP code to handle XSS (needs **3+ screenshots**)

Now you fix the exact vulnerability you just exploited.

### 3.1 Add output encoding
1. In `dashboard.php`, undo Part 2.1: put the **secured** lines back:
   ```php
   <td><?= e($row['username']) ?></td>
   <td><?= e($row['action']) ?></td>
   ```
2. At the top of the file, the helper that does the work is:
   ```php
   function e($value) {
       return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
   }
   ```
   `htmlspecialchars()` converts `<`, `>`, `"`, `'` and `&` into harmless HTML
   entities, so a `<script>` is **shown as text** instead of being executed.

> 📸 **Screenshot 4** — VS Code showing the `e()` helper function and the
> secured `<td><?= e($row['action']) ?></td>` lines. Caption: *"Fix: every
> stored value is passed through htmlspecialchars() before it is printed, so
> injected scripts are escaped and cannot run."*

### 3.2 Prove the fix works
1. Reload the dashboard in the browser. The old payload row now shows up as the
   **literal text** `<script>...</script>` instead of defacing the page.
2. Paste the same payload into the Post box again and submit.

> 📸 **Screenshot 5** — the feed now displaying the script payload as plain,
> visible text in the "Did what" column, with the page **not** defaced.
> Caption: *"After the fix, the same payload is rendered as text. The attack no
> longer works."*

> 📸 **Screenshot 6** — the final working dashboard in the browser (normal
> messages + the neutralized payload text). This is the **final output** the
> activity asks for. Caption: *"Final secured output of the Who Did It page in
> the browser."*

---

## PART 4 — Add login/registration password security (needs **3+ screenshots**)

The activity requires passwords with **minimum 8 characters** AND a **mix of
numbers, uppercase and lowercase letters**. This is already implemented in
`register.php`. Here is how to document it.

### 4.1 Show the validation code
The rules in `register.php` are:
```php
if (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters long.';
}
if (!preg_match('/[A-Z]/', $password)) {
    $errors[] = 'Password must contain at least one UPPERCASE letter.';
}
if (!preg_match('/[a-z]/', $password)) {
    $errors[] = 'Password must contain at least one lowercase letter.';
}
if (!preg_match('/[0-9]/', $password)) {
    $errors[] = 'Password must contain at least one number.';
}
```
Passwords are then stored hashed:
```php
$hash = password_hash($password, PASSWORD_DEFAULT);
```

> 📸 **Screenshot 7** — VS Code showing the four validation rules above.
> Caption: *"Registration enforces a minimum length of 8 and requires at least
> one uppercase letter, one lowercase letter, and one number."*

### 4.2 Show a weak password being rejected
1. Open `register.php` in the browser.
2. Try to register with a weak password, e.g. username `test`, password `abc`.
3. Submit.

> 📸 **Screenshot 8** — the registration form showing the red error messages
> ("must be at least 8 characters", "must contain at least one uppercase
> letter", "...one number"). Caption: *"A weak password is rejected and the
> reasons are shown to the user."*

### 4.3 Show a strong password being accepted
1. Register again with a valid password, e.g. username `secureuser`, password
   `Password1`.
2. Submit.

> 📸 **Screenshot 9** — the green *"Account created! You can now log in."*
> message (the **final output** in the browser). Caption: *"A password meeting
> all rules is accepted and the account is created with a hashed password."*

---

## PART 5 — Push to GitHub and paste the link

1. Create a new **public** repository on GitHub, e.g. `who-did-it-xss`.
2. In the project folder, run:
   ```bash
   git init
   git add .
   git commit -m "Securing against XSS: login/logout/who-did-it with XSS fix and password policy"
   git branch -M main
   git remote add origin https://github.com/<your-username>/who-did-it-xss.git
   git push -u origin main
   ```
3. Copy the repository URL and paste it into the last part of the Brightspace
   document ("Paste Github repository link here").

---

## Screenshot checklist (so you hit every minimum)

| Section | Required | Suggested shots |
|---------|----------|-----------------|
| Part 2 — Attack | 3 | Vulnerable code, injecting payload, page defaced |
| Part 3 — XSS fix | 3 | `e()`/secured code, payload shown as text, final secured output |
| Part 4 — Password security | 3 | Validation code, weak password rejected, strong password accepted |

Take each screenshot as you go, drop it under the matching heading in your
Brightspace `.docx`, and write the one-line caption shown above each 📸.
