A platform for online trading of digital and physical goods
#Get start
After receiving the project, execute the following command
````bash
copmoser update
````
In the next step run
````bash
cp .env.example .env 
````
Now run
````bash
php artisan key:generate
php attisan migrate 
````
And in the last step, you can enter the following command to access the app
````bash
php role:start
php attisan serve
````
Now you can enter the admin panel from the address ****127.0.0.1:8000/auth**** with the username and password of the **admin** - **admin**
