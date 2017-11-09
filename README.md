
The Shared E-mail module overrides the 'user' module's validation,
that prevents the same e-mail address being used by more than one user.

Works for both registration and account updates.

Displays a warning to the user that they are using a shared email.

Based on http://drupal.org/node/15578#comment-249157

All this module does is remove the unique constraint for the email using a hook.

Installation
* Enable the module in /admin/modules
