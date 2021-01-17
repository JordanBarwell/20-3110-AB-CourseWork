Installation Instructions (Assuming you have a P3T environment):
1. Place ab_coursework into your includes folder.
2. Place ab_coursework_public into your public_php folder.
3. Edit the email address and phone number in ab_coursework.sql line 38 to be your email address and phone number.
3. Run the ab_coursework.sql file in the mysql terminal or however you choose to run your sql scripts.
4. Put your EE M2M username & password into settings.php (settings.php > $settings > settings > soap > login).

Basic Usage:

An Admin user account has been already created for you in the sql script, to access it login with the usernam as admin and the password as password.

Otherwise, to be able to use the web application you must register an account with a valid username, email address and phone number.
The phone number must start with a country code, e.g. +44/+1, followed by the rest of the number.

Once a user has registered their account a menu will show four options (Or five if the user is Admin):

1.Send New Message:
Allows a user to send inputs (below) to the circuit board:

Temperature(-150 to 150°C)
Fan Direction(Forward/Reverse)
Last Digit(Interger 0-9999)
Switch 1(On/Off)
Switch 2(On/Off)
Switch 3(On/Off)
Switch 4(On/Off)

2.Download Messages:
Pulls messages from the M2M server and saves them in the web application database, only if they are newer than the latest message in the database and the ID XML tag is present in the message and is set to GroupAB.

3.View Messages:
Shows all the messages from the circuit board that are stored in the database. (3 dummy messages are already provided for you, to get more, see 2. Download Messages)

4.Logout
Ends user session, requiring them to return to the homepage and either register or login again.

5.Admin Menu (ADMIN ONLY):
If a user is admin, they have a menu which will have three options which are to view all registered users, view all downloaded messages and to return to the main menu. If a non-admin user attempts to visit any of the admin pages, they will be redirected to the main menu.
