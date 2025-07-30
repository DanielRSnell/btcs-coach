# Page snapshot

```yaml
- link "Log in to your account":
  - /url: http://btcs-coach.test/
  - img
  - text: Log in to your account
- heading "Log in to your account" [level=1]
- paragraph: Enter your email and password below to log in
- text: Email address
- textbox "Email address": invalid@email.com
- paragraph: These credentials do not match our records.
- text: Password
- link "Forgot password?":
  - /url: http://btcs-coach.test/forgot-password
- textbox "Password"
- checkbox "Remember me"
- text: Remember me
- button "Log in"
- text: Don't have an account?
- link "Sign up":
  - /url: http://btcs-coach.test/register
```