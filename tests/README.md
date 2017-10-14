# BEFORE TESTS EXECUTION
Run this command:
`bash tests/install-wp-tests.sh wordpress_test root '' localhost latest`

Replace `root` with the username of your database and replace '' with the database password. Also replace `localhost` with the hostname of your database. You can find all three of these values in your wp-config.php file.

You can now type `phpunit` into the command line and the unit tests will run.

Please note, you also might need to modify the path(s) in bootstrap.php with correct path for your WordPress location, it's a known phpunit issue related to git mirroring.

# HOW TO RUN TESTS
1. Go to plugin folder and execute `phpunit` command. It should start unit testing process and run SampleTest which would return true.
