# Payment process project
#### Test task issued by Sun Finance

### The project was developed using Laragon, a local server environment, PHP version 8.1, and sqlite3 as the database management system.

## Technical requirements:
* PHP interpreter installed
* Local server installed (Apache or NGINX)
* sqlite3 extension installed in php.ini file.


## Import payments from console
To import a CSV file, open the command line and navigate to the project folder. Then, run the following command: 'php import.php --file=<path_to_csv_file>' 

The command line will display the results for each payment, and the data will also be logged into a log file.

## API
To use the API, move the project folder to the "www" directory on your local server and turn on the server. To send data, use the POST method to the following url: localhost/{project_name}/api/payment.

## Report
To view a report for a specific date, navigate to the project folder and run the following command: 'php report.php --date=YYYY-MM-DD'.

## Files
* 'payment_processor.php' - processes payments
* 'api.php' - processes POST requests for payments
* 'import.php' - processes CSV files containing payment data from the command line
* 'report.php' - generates a report for a specific date from the command line
* 'storage.php' - manages the database
* 'logger.php' - logs data
* 'communication.php' - processes sending notifications via email and/or phone.


P.S. Completed only the main part of the task, without the optional part.