# Installation

Run this command (you might need to SSH into container on Local by Flywheel):

`bash tests/install-wp-tests.sh <database:wordpress_test> <username:root> <password:****>`

Replace `root` with the username of your database and replace `****` with the database password.
You can find all these values in your `wp-config.php` file.

You can now type `phpunit` into the command line and the unit tests will run.

Please note, you also might need to modify the path(s) in
`bootstrap.php` with correct path for your WordPress location, it's a known
phpunit issue related to git mirroring.

_______________________

# Running Tests

1. Go to plugin folder and execute `phpunit` command. It should start unit testing process and run SampleTest which would return true.

_Note, you might need to SSH into your container on Local by Flywheel._
