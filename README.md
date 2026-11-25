# Who Did It — Securing Against XSS

A small PHP login / logout / activity-feed ("Who Did It") system built for the
**Securing Against XSS** activity. It demonstrates a stored **Cross-Site
Scripting (XSS) defacement** attack and the code that prevents it, plus a
hardened registration password policy.

## Features
- **Register / Login / Logout** with sessions
- **"Who Did It" feed** — every login, logout and message is logged and shown to all users
- **Stored-XSS defacement demo** on the shared feed
- **XSS prevention** via `htmlspecialchars()` output encoding
- **Password policy**: minimum 8 characters with at least one uppercase letter,
  one lowercase letter, and one number
- Passwords stored with `password_hash()`; all queries use PDO prepared statements

## Requirements
- XAMPP (or any PHP 8 + MySQL/MariaDB stack)

## Setup
1. Copy this folder into `C:\xampp\htdocs\who-did-it`.
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`, go to the **SQL** tab, paste the contents
   of `schema.sql`, and run it. This creates the `who_did_it` database.
4. Visit `http://localhost/who-did-it/register.php` and create an account.

## Files
| File | Purpose |
|------|---------|
| `db.php` | PDO database connection |
| `schema.sql` | Creates the database and tables |
| `register.php` | Account creation + password policy |
| `login.php` | Login + session start |
| `logout.php` | Session destroy |
| `dashboard.php` | The "Who Did It" feed (XSS injection point + fix) |
| `style.css` | Styling |
| `payloads.txt` | Defacement payloads used in the attack demo |
| `PROCEDURE.md` | Step-by-step guide for the whole activity |

## Security notes
`dashboard.php` ships in its **secured** form. To reproduce the attack for the
report, temporarily swap in the two commented "VULNERABLE VERSION" lines, run
the demo, then restore the secured lines. See `PROCEDURE.md`.
