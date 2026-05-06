# 356Final
356 Final project for Spring 2026 Class

## Installation instructions
### Windows Setup (WSL)
Before installing PHP, __Windows users__ should set up WSL to run a native Linux environment.

1. Install WSL: Open PowerShell as an Administrator and run:
```
wsl --install
```
_Restart: Reboot your machine when the installation finishes._

2. Initialize Ubuntu: Search for "Ubuntu" in your Start menu and launch it.
- Set your username and password when prompted.

3. Update your package lists to ensure you get the latest software:
```
sudo apt update && sudo apt upgrade -y
```

### Installing PHP & Composer
__Within your Linux/WSL terminal__, run the following commands to:
- install PHP
- install Composer dependency manager 

1. Install Packages
This command installs the PHP engine, common extensions required by Composer, and Composer itself:
```
sudo apt install php php-cli php-curl php-mbstring php-xml php-zip composer -y
```
2. Verify Installation
Check that both are installed and responding correctly:
```
php -v
composer --version
```

### VS Code Integration & Project Setup
1. Open your Project: Navigate to your project folder in the terminal and type:
```
code .
```

_This will launch a VS Code window running "inside" Linux._

Run Composer Install:
Open the integrated terminal in VS Code (Ctrl +  `) and run:
- make sure the terminal is an Ubuntu (WSL) Terminal, not a generic powershell terminal
- (if it is powershell, press the `v` dropdown in the top right of the terminal and press the `bash` or `WSL` option)
```
composer install
```
This downloads all dependencies listed in your composer.lock file into a vendor folder.

### .ENV file
due to security reasons, the database URL and API key ar enot publically available. Reach out to get access to the API key, or make your own database.

### Running the Local Server
PHP has a built-in server that you can run to host the website!

1. Start the Server:
In your VS Code terminal, run the following command:
```
php -S localhost:8000
```
_The terminal will stay active as long as the server is running._

2. Open the Website:

- Direct Click: Hold Ctrl and click http://localhost:8000 inside the terminal.

- Manual: Open your preferred web browser and type http://localhost:8000 into the address bar.

`[NOTE]`
If you see a 404 Not Found error, ensure you are running the command from the directory that contains your index.php file.
